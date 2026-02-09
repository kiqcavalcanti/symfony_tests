<?php

namespace App\Command;

use Symfony\Bundle\MakerBundle\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Input\StreamableInputInterface;

#[AsCommand(
  name: 'make:feature',
  description: 'Cria uma feature completa (entidade, migration, controllers, templates, service)'
)]
class MakeFeatureCommand extends Command
{
  /**
   * Tipos disponíveis e seus aliases no Doctrine
   */
  private const TYPES_MAP = [
    'string' => 'string',
    'int' => 'integer',
    'bool' => 'boolean',
    'datetime' => 'datetime',
    'text' => 'text',
  ];

  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $io = new SymfonyStyle($input, $output);

    $io->title('Gerador de Feature');

    // Coleta o nome da entidade
    $entityName = $io->ask(
      'Nome da entidade (ex: User, BlogPost)',
      null,
      function ($value) {
        if (empty($value)) {
          throw new \RuntimeException('O nome da entidade é obrigatório');
        }
        return $value;
      }
    );

    // Normaliza o nome da entidade para PascalCase (estilo de classe)
    $entityName = $this->normalizeEntityName($entityName);

    $io->writeln('');
    $io->writeln('<info>Configurando campos da entidade:</info>');
    $io->writeln('');

    $fieldInputs = [];

    while (true) {
      $fieldName = $io->ask(
        'Nome do campo (enter para finalizar)',
        null
      );

      if (!$fieldName) {
        break;
      }

      // Usa choice mas aceitando também o nome da chave
      $typeIndex = $io->choice(
        'Tipo do campo',
        array_keys(self::TYPES_MAP),
        'string'
      );

      $nullable = $io->confirm('Pode ser nulo?', false);

      // Pergunta se o campo deve ser único
      $unique = $io->confirm('Valor único (não repetível)?', false);

      // Armazena os dados estruturados
      $fieldInputs[] = [
        'name' => $fieldName,
        'type' => $typeIndex,
        'doctrineType' => self::TYPES_MAP[$typeIndex],
        'nullable' => $nullable,
        'unique' => $unique,
      ];

      $io->writeln("<comment>✓ Campo '{$fieldName}' ({$typeIndex})" . ($nullable ? ', nullable' : '') . " adicionado</comment>");
      $io->writeln('');
    }

    // Se não houver campos, pergunta se deseja continuar
    if (empty($fieldInputs)) {
      if (!$io->confirm('Nenhum campo foi adicionado. Deseja continuar?', false)) {
        $io->warning('Operação cancelada pelo usuário');
        return Command::SUCCESS;
      }
    }

    // Resumo dos dados coletados
    $io->section('Resumo da Entidade');
    $io->writeln("<info>Nome da entidade:</info> {$entityName}");
    $io->writeln("<info>Campos:</info>");

    if (!empty($fieldInputs)) {
      foreach ($fieldInputs as $field) {
        $nullableStr = $field['nullable'] ? ' (nullable)' : '';
        $io->writeln("  - {$field['name']}: {$field['type']}{$nullableStr}");
      }
    } else {
      $io->writeln("  (Nenhum campo)");
    }

    $io->writeln('');

    // Seção de funcionalidades adicionais
    $io->section('Funcionalidades Adicionais');

    $useSoftDelete = $io->confirm('Deseja adicionar SoftDelete (exclusão lógica)?', false);
    $useTimestamps = $io->confirm('Deseja adicionar Timestamps (created_at e updated_at)?', false);

    $io->writeln('');


    if (!$io->confirm('Deseja prosseguir com a criação da entidade?', true)) {
      $io->warning('Operação cancelada pelo usuário');
      return Command::SUCCESS;
    }

    // Chama o comando make:entity com os dados coletados
    if (!empty($fieldInputs)) {
      $this->callMakeEntity($entityName, $fieldInputs, $io, $output);
      // Aplica constraints de unique diretamente no arquivo da entidade, se necessário
      $this->applyUniqueConstraintsToEntityFile($entityName, $fieldInputs, $io);
    } else {
      // Se não há campos, apenas cria a entidade vazia
      $this->createEmptyEntity($entityName, $output);
    }

    // Pós-processamento: garante que a entidade extenda BaseEntity
    $this->ensureEntityExtendsBase($entityName, $io, $useSoftDelete, $useTimestamps);

    // Adiciona método getUniqueFields na entidade, se necessário
    $uniqueFields = array_values(array_filter(array_map(function($f){ return !empty($f['unique']) ? $f['name'] : null; }, $fieldInputs)));
    if (!empty($uniqueFields)) {
      $this->addGetUniqueFieldsToEntityFile($entityName, $uniqueFields, $io);
    }

    // Gera Controller, DTOs, Service e Transformer a partir dos templates
    $this->generateArtifactsFromTemplates($entityName, $fieldInputs, $io, $output);

    // Cria migration e pergunta se deseja executar
    $this->createMigrationAndMaybeExecute($io, $output);

    $io->success('Feature criada com sucesso!');

    return Command::SUCCESS;
  }

  /**
   * Cria uma entidade vazia usando make:entity
   */
  private function createEmptyEntity(string $entityName, OutputInterface $output): void
  {
    $projectRoot = dirname(__DIR__, 2);
    $command = sprintf(
      'cd %s && echo "" | php bin/console make:entity %s',
      escapeshellarg($projectRoot),
      escapeshellarg($entityName)
    );

    passthru($command, $returnCode);

    if ($returnCode !== 0) {
      throw new \RuntimeException(sprintf('make:entity command failed with code %d', $returnCode));
    }
  }

  /**
   * Chama o comando make:entity do MakerBundle com stream de inputs customizado
   */
  private function callMakeEntity(string $entityName, array $fieldInputs, SymfonyStyle $io, OutputInterface $output): void
  {
    $io->section('Criando entidade com make:entity');

    // Prepara a entrada para o comando interativo
    $inputLines = [];

    foreach ($fieldInputs as $field) {
      // Nome do campo
      $inputLines[] = $field['name'];

      // Tipo do campo (mapeado para Doctrine)
      $inputLines[] = $field['doctrineType'];

      // Se for string, pede o tamanho
      if ($field['doctrineType'] === 'string') {
        $inputLines[] = '255'; // tamanho padrão
      }

      // Pode ser nulo?
      $inputLines[] = $field['nullable'] ? 'yes' : 'no';
    }

    // Linha vazia para finalizar
    $inputLines[] = '';

    $input = implode("\n", $inputLines);

    // Cria arquivo temporário com os inputs
    $tmpFile = tempnam(sys_get_temp_dir(), 'make_entity_inputs_');
    file_put_contents($tmpFile, $input);

    try {
      // Executa o comando via proc_open para ter controle total sobre stdin
      $projectRoot = dirname(__DIR__, 2);

      // Abre o arquivo temporário para leitura
      $inputHandle = fopen($tmpFile, 'r');

      // Descriptores para proc_open
      $descriptorspec = [
        0 => $inputHandle,  // stdin do arquivo
        1 => ['pipe', 'w'],  // stdout
        2 => ['pipe', 'w'],  // stderr
      ];

      $process = proc_open(
        sprintf('cd %s && php bin/console make:entity %s', $projectRoot, $entityName),
        $descriptorspec,
        $pipes
      );

      if (is_resource($process)) {
        // Lê stdout
        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        // Lê stderr
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        // Fecha o handle do arquivo
        fclose($inputHandle);

        // Aguarda o processo
        $returnCode = proc_close($process);

        // Filtra mensagens de erro esperadas quando stdin é fechado
        $filteredStderr = '';
        if ($stderr) {
          $lines = explode("\n", $stderr);
          foreach ($lines as $line) {
            // Ignora mensagens de "Aborted" que são esperadas
            if (stripos($line, 'Aborted') === false && stripos($line, 'make:entity') === false) {
              $filteredStderr .= $line . "\n";
            }
          }
          $filteredStderr = trim($filteredStderr);
        }

        // Exibe a saída
        $output->writeln($stdout);
        if ($filteredStderr) {
          $output->writeln($filteredStderr);
        }

        // O comando pode retornar 1 se o stdin foi abortado após os campos serem criados
        // O importante é se a classe foi gerada. Vamos ser mais leniente com erros
        if ($returnCode !== 0 && $returnCode !== 1) {
          throw new \RuntimeException(sprintf('make:entity command failed with code %d', $returnCode));
        }
      } else {
        fclose($inputHandle);
        throw new \RuntimeException('Failed to start make:entity process');
      }
    } finally {
      // Remove arquivo temporário
      if (file_exists($tmpFile)) {
        unlink($tmpFile);
      }
    }
  }

  /**
   * Garante que a entidade extenda BaseEntity e adiciona o use se necessário
   */
  private function ensureEntityExtendsBase(string $entityName, SymfonyStyle $io, bool $useSoftDelete = false, bool $useTimestamps = false): void
  {
    $projectRoot = dirname(__DIR__, 2);
    $entityPath = $projectRoot . '/src/Entity/' . $entityName . '.php';

    if (!file_exists($entityPath)) {
      $io->warning("Arquivo de entidade não encontrado em: {$entityPath}");
      return;
    }

    $content = file_get_contents($entityPath);
    $modified = false;

    // Se já estende BaseEntity, nada a fazer
    if (stripos($content, 'extends BaseEntity') === false) {
      // Adiciona use App\Entity\BaseEntity; se não existir
      $useLine = "use App\\Entity\\BaseEntity;";
      if (strpos($content, $useLine) === false) {
        // Tenta inserir após o último use; se não houver use, insere após a declaração do namespace
        if (preg_match_all('/^use\s.+;$/m', $content, $matches, PREG_OFFSET_CAPTURE)) {
          $last = end($matches[0]);
          $insertPos = $last[1] + strlen($last[0]);
          $content = substr_replace($content, "\n" . $useLine, $insertPos, 0);
        } else {
          // Insere após namespace
          $ns = "namespace App\\Entity;";
          if (strpos($content, $ns) !== false) {
            $content = str_replace($ns, $ns . "\n" . $useLine, $content);
          }
        }
      }

      // Insere extends BaseEntity na declaração da classe
      $content = preg_replace('/class\s+' . preg_quote($entityName, '/') . '(\s*)(implements\s+[^{]+)?/m', 'class ' . $entityName . ' extends BaseEntity $2', $content, 1);

      $io->writeln("Atualizada entidade para estender BaseEntity: {$entityPath}");
      $modified = true;
    } else {
      $io->writeln("Entidade {$entityName} já estende BaseEntity");
    }

    // Adiciona traits se solicitadas
    if ($useSoftDelete) {
      $content = $this->addTraitToEntityContent($content, 'SoftDeletableTrait', 'App\\Entity\\Traits\\SoftDeletableTrait', $io);
      $modified = true;
    }

    if ($useTimestamps) {
      $content = $this->addTraitToEntityContent($content, 'TimestampableTrait', 'App\\Entity\\Traits\\TimestampableTrait', $io);
      $modified = true;
    }

    if ($modified) {
      file_put_contents($entityPath, $content);
    }
  }

  /**
   * Adiciona uma trait ao conteúdo da entidade e retorna o conteúdo modificado
   */
  private function addTraitToEntityContent(string $content, string $traitName, string $traitNamespace, SymfonyStyle $io): string
  {
    // Verifica se a trait já está sendo usada
    if (stripos($content, 'use ' . $traitName) !== false) {
      $io->writeln("Trait {$traitName} já está sendo usada na entidade");
      return $content;
    }

    // Adiciona use namespace statement para a trait
    $useNamespaceLine = "use " . $traitNamespace . ";";

    // Insere após os últimos use statements
    if (preg_match_all('/^use\s.+;$/m', $content, $matches, PREG_OFFSET_CAPTURE)) {
      $lastMatch = end($matches[0]);
      $insertPos = $lastMatch[1] + strlen($lastMatch[0]);
      $content = substr_replace($content, "\n" . $useNamespaceLine, $insertPos, 0);
    } else {
      // Fallback: insere após namespace
      if (preg_match('/^namespace\s+[^;]+;/m', $content, $matches, PREG_OFFSET_CAPTURE)) {
        $insertPos = $matches[0][1] + strlen($matches[0][0]);
        $content = substr_replace($content, "\n" . $useNamespaceLine, $insertPos, 0);
      }
    }

    // Insere o uso da trait dentro da classe (após {)
    if (preg_match('/class\s+\w+\s+extends\s+\w+\s*{/', $content, $matches, PREG_OFFSET_CAPTURE)) {
      $classStart = $matches[0][1] + strlen($matches[0][0]);
      $content = substr_replace($content, "\n    use " . $traitName . ";", $classStart, 0);
      $io->writeln("Adicionada trait {$traitName} à entidade");
    }

    return $content;
  }

  // Normaliza para nome de classe PascalCase, remove caracteres inválidos
  private function normalizeEntityName(string $name): string
  {
    // Remove caracteres não alfanuméricos, substitui separadores por espaço
    $clean = preg_replace('/[^a-zA-Z0-9_\-\s]/', '', $name);
    $clean = str_replace(['-', '_'], ' ', $clean);
    $clean = ucwords(strtolower($clean));
    $clean = str_replace(' ', '', $clean);
    // Garantir que comece com maiúscula
    if ($clean === '') {
      return ucfirst($name);
    }
    return ucfirst($clean);
  }

  /**
   * Aplica unique: true nas colunas da entidade que foram marcadas como únicas
   */
  private function applyUniqueConstraintsToEntityFile(string $entityName, array $fieldInputs, SymfonyStyle $io): void
  {
    $projectRoot = dirname(__DIR__, 2);
    $entityPath = $projectRoot . '/src/Entity/' . $entityName . '.php';

    if (!file_exists($entityPath)) {
      $io->warning("Arquivo de entidade não encontrado para aplicar unique: {$entityPath}");
      return;
    }

    $content = file_get_contents($entityPath);
    $original = $content;

    foreach ($fieldInputs as $field) {
      if (empty($field['unique'])) continue;

      $prop = $field['name'];

      // Encontra a declaração da propriedade (public/protected/private ... $prop)
      $propRegex = '/(public|protected|private)\s+[^$;=]*' . preg_quote('$' . $prop, '/') . '\b/m';
      if (!preg_match($propRegex, $content, $propMatch, PREG_OFFSET_CAPTURE)) {
        continue;
      }

      $propPos = $propMatch[0][1];

      // Procura o atributo #[ORM\Column( antes da propriedade
      $searchArea = substr($content, 0, $propPos);
      $attrStart = strrpos($searchArea, '#[ORM\Column(');

      if ($attrStart !== false) {
        // encontra o fechamento do atributo ')]' após $attrStart
        $attrEnd = strpos($content, ')]', $attrStart);
        if ($attrEnd !== false && $attrEnd < $propPos) {
          // extrai o conteúdo dentro dos parênteses
          $openParenPos = strpos($content, '(', $attrStart);
          $inside = substr($content, $openParenPos + 1, $attrEnd - $openParenPos - 1);

          if (stripos($inside, 'unique') !== false) {
            continue; // já tem unique
          }

          $newInside = trim($inside) === '' ? 'unique: true' : trim($inside) . ', unique: true';

          // substitui a parte dentro dos parênteses
          $content = substr_replace($content, $newInside, $openParenPos + 1, $attrEnd - $openParenPos - 1);
          continue;
        }
      }

      // Se não encontrou atributo, tenta encontrar anotação @ORM\Column na phpdoc imediatamente acima
      // procura a posição da propriedade e pega até 300 chars antes para procurar phpdoc
      $docSearchStart = max(0, $propPos - 300);
      $docBlock = substr($content, $docSearchStart, $propPos - $docSearchStart);
      if (preg_match('/@' . preg_quote('ORM\\Column', '/') . '\(([^)]*)\)/i', $docBlock, $docMatch, PREG_OFFSET_CAPTURE)) {
        $inside = $docMatch[1][0];
        if (stripos($inside, 'unique') !== false) continue;
        $newInside = trim($inside) === '' ? 'unique=true' : trim($inside) . ', unique=true';
        // substitui a primeira ocorrência encontrada no bloco
        $docBlockNew = preg_replace('/@' . preg_quote('ORM\\Column', '/') . '\(([^)]*)\)/i', '@ORM\\Column(' . $newInside . ')', $docBlock, 1);
        // grava de volta no content
        $content = substr_replace($content, $docBlockNew, $docSearchStart, strlen($docBlock));
      } else {
        // procura por @ORM\Column( ... ) manually to avoid regex escape issues
        $needle = '@ORM\Column(';
        $posRel = stripos($docBlock, $needle);
        if ($posRel !== false) {
          $openPos = strpos($docBlock, '(', $posRel);
          $closePos = strpos($docBlock, ')', $openPos);
          if ($openPos !== false && $closePos !== false) {
            $inside = substr($docBlock, $openPos + 1, $closePos - $openPos - 1);
            if (stripos($inside, 'unique') === false) {
              $newInside = trim($inside) === '' ? 'unique=true' : trim($inside) . ', unique=true';
              $docBlockNew = substr($docBlock, 0, $openPos) . '(' . $newInside . ')' . substr($docBlock, $closePos + 1);
              $content = substr_replace($content, $docBlockNew, $docSearchStart, strlen($docBlock));
            }
          }
        }
      }
    }

    if ($content !== $original) {
      file_put_contents($entityPath, $content);
      $io->writeln("Aplicadas constraints unique na entidade: {$entityPath}");
    }
  }

  /**
   * Gera arquivos a partir dos templates em config/templates
   */
  private function generateArtifactsFromTemplates(string $entityName, array $fieldInputs, SymfonyStyle $io, OutputInterface $output): void
  {
    $projectRoot = dirname(__DIR__, 2);
    $templatesDir = $projectRoot . '/config/templates';

    if (!is_dir($templatesDir)) {
      $io->warning('Pasta de templates não encontrada: ' . $templatesDir);
      return;
    }

    $placeholders = [
      '__TEMPLATE_NAME__' => $entityName,
      '__TEMPLATE_NAME_IN_PLURAL__' => strtolower($entityName) . 's',
      '__TEMPLATE_ENTITY__' => $entityName,
    ];

    // Controller
    $tpl = @file_get_contents($templatesDir . '/TemplateController.php');
    if ($tpl !== false) {
      $content = str_replace(array_keys($placeholders), array_values($placeholders), $tpl);
      // Ajusta namespace para Controller (substitui a primeira linha de namespace do template)
      $content = preg_replace('/^namespace\s+[^;]+;/m', 'namespace App\\Controller;', $content, 1);
      $targetDir = $projectRoot . '/src/Controller';
      if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
      $targetFile = $targetDir . '/' . $entityName . 'Controller.php';
      if (file_exists($targetFile) && !$io->confirm("Arquivo {$targetFile} já existe. Sobrescrever?", false)) {
        $io->writeln("Pulando Controller: {$targetFile}");
      } else {
        file_put_contents($targetFile, $content);
        $io->writeln("Controller criado: {$targetFile}");
      }
    }

    // Service
    $tpl = @file_get_contents($templatesDir . '/TemplateService.php');
    if ($tpl !== false) {
      $content = str_replace(array_keys($placeholders), array_values($placeholders), $tpl);
      // Ajusta namespace para Service
      $content = preg_replace('/^namespace\s+[^;]+;/m', 'namespace App\\Application\\Services;', $content, 1);
      $targetDir = $projectRoot . '/src/Application/Services';
      if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
      $targetFile = $targetDir . '/' . $entityName . 'Service.php';
      if (file_exists($targetFile) && !$io->confirm("Arquivo {$targetFile} já existe. Sobrescrever?", false)) {
        $io->writeln("Pulando Service: {$targetFile}");
      } else {
        // Ajusta o nome da classe TemplateService -> {Entity}Service
        $content = preg_replace('/class\s+TemplateService/', 'class ' . $entityName . 'Service', $content, 1);
        file_put_contents($targetFile, $content);
        $io->writeln("Service criado: {$targetFile}");
      }
    }

    // Transformer
    $tpl = @file_get_contents($templatesDir . '/TemplateTransformer.php');
    if ($tpl !== false) {
      $content = str_replace(array_keys($placeholders), array_values($placeholders), $tpl);
      // Ajusta namespace para Transformer
      $content = preg_replace('/^namespace\s+[^;]+;/m', 'namespace App\\Application\\Transformers;', $content, 1);
      $targetDir = $projectRoot . '/src/Application/Transformers';
      if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
      $targetFile = $targetDir . '/' . $entityName . 'Transformer.php';
      if (file_exists($targetFile) && !$io->confirm("Arquivo {$targetFile} já existe. Sobrescrever?", false)) {
        $io->writeln("Pulando Transformer: {$targetFile}");
      } else {
        $content = preg_replace('/class\s+TemplateTransformer/', 'class ' . $entityName . 'Transformer', $content, 1);
        file_put_contents($targetFile, $content);
        $io->writeln("Transformer criado: {$targetFile}");
      }
    }

    // DTOs: cria pasta src/Application/Dto/{Entity} e gera Create/Update DTOs com campos e validações
    $tpl = @file_get_contents($templatesDir . '/TemplateDto.php');
    if ($tpl !== false) {
      $dtoDir = $projectRoot . '/src/Application/Dto/' . $entityName;
      if (!is_dir($dtoDir)) mkdir($dtoDir, 0777, true);

      $createFile = $dtoDir . '/Create' . $entityName . 'Dto.php';
      $updateFile = $dtoDir . '/Update' . $entityName . 'Dto.php';

      $baseNamespace = 'App\\Application\\Dto\\' . $entityName;

      // Extrai informações dos campos da entidade
      $fieldInfos = $this->extractFieldsFromEntity($entityName, $fieldInputs);

      // Gera conteúdo das DTOs com campos e validações
      $createContent = $this->generateDtoContent($entityName, 'Create' . $entityName . 'Dto', $baseNamespace, $fieldInfos, true);
      $updateContent = $this->generateDtoContent($entityName, 'Update' . $entityName . 'Dto', $baseNamespace, $fieldInfos, false);

      if (file_exists($createFile) && !$io->confirm("Arquivo {$createFile} já existe. Sobrescrever?", false)) {
        $io->writeln("Pulando DTO Create: {$createFile}");
      } else {
        // Validate generated PHP before writing
        $tmp = tempnam(sys_get_temp_dir(), 'dto_');
        file_put_contents($tmp, $createContent);
        $lintCmd = sprintf('php -l %s 2>&1', escapeshellarg($tmp));
        $outputStr = null;
        $ret = null;
        exec($lintCmd, $outputLines, $ret);
        $outputStr = implode("\n", $outputLines);
        if ($ret === 0) {
          file_put_contents($createFile, $createContent);
          $io->writeln("DTO criado: {$createFile}");
        } else {
          // Keep failed file for inspection
          $failed = $createFile . '.failed.php';
          file_put_contents($failed, $createContent);
          $io->error("Erro de sintaxe ao gerar DTO. Conteúdo salvo em: {$failed}\n" . $outputStr);
        }
        @unlink($tmp);
      }

      if (file_exists($updateFile) && !$io->confirm("Arquivo {$updateFile} já existe. Sobrescrever?", false)) {
        $io->writeln("Pulando DTO Update: {$updateFile}");
      } else {
        $tmp = tempnam(sys_get_temp_dir(), 'dto_');
        file_put_contents($tmp, $updateContent);
        $lintCmd = sprintf('php -l %s 2>&1', escapeshellarg($tmp));
        $outputLines = [];
        $ret = null;
        exec($lintCmd, $outputLines, $ret);
        $outputStr = implode("\n", $outputLines);
        if ($ret === 0) {
          file_put_contents($updateFile, $updateContent);
          $io->writeln("DTO criado: {$updateFile}");
        } else {
          $failed = $updateFile . '.failed.php';
          file_put_contents($failed, $updateContent);
          $io->error("Erro de sintaxe ao gerar DTO. Conteúdo salvo em: {$failed}\n" . $outputStr);
        }
        @unlink($tmp);
      }
    }
  }

  /**
   * Executa make:migration e pergunta se deseja rodar migrate
   */
  private function createMigrationAndMaybeExecute(SymfonyStyle $io, OutputInterface $output): void
  {
    $projectRoot = dirname(__DIR__, 2);

    // Verifica se há migrations pendentes ANTES de criar uma nova
    $io->section('Verificando migrations pendentes');
    $statusCmd = sprintf('cd %s && php bin/console doctrine:migrations:status 2>&1', escapeshellarg($projectRoot));
    $statusOutput = shell_exec($statusCmd);

    if ($statusOutput !== null && (
        stripos($statusOutput, 'New Migrations') !== false ||
        preg_match('/New Migrations:\s+(\d+)/i', $statusOutput, $matches)
      )) {
      // Extrai o número de migrations pendentes
      if (preg_match('/New Migrations:\s+(\d+)/i', $statusOutput, $matches)) {
        $pendingCount = (int)$matches[1];

        if ($pendingCount > 0) {
          $io->warning("Existem {$pendingCount} migration(s) pendente(s) que ainda não foram executadas.");
          $io->writeln('Para evitar que a nova migration recrie tabelas existentes, é recomendado executar as migrations pendentes primeiro.');
          $io->writeln('');

          if ($io->confirm('Deseja executar as migrations pendentes AGORA antes de criar a nova?', true)) {
            $io->section('Executando migrations pendentes');
            $migrateCmd = sprintf('cd %s && php bin/console doctrine:migrations:migrate --no-interaction', escapeshellarg($projectRoot));
            passthru($migrateCmd, $migrateCode);

            if ($migrateCode !== 0) {
              $io->error('Erro ao executar migrations pendentes. Abortando criação de nova migration.');
              return;
            }

            $io->success('Migrations pendentes executadas com sucesso!');
            $io->writeln('');
          } else {
            if (!$io->confirm('Deseja continuar mesmo assim? (A nova migration pode conter tabelas já existentes)', false)) {
              $io->warning('Operação cancelada. Execute as migrations pendentes e tente novamente.');
              return;
            }
          }
        }
      }
    }

    $io->section('Criando migration');
    $command = sprintf('cd %s && php bin/console make:migration', escapeshellarg($projectRoot));

    passthru($command, $returnCode);

    if ($returnCode !== 0) {
      $io->warning('make:migration retornou um código diferente de 0: ' . $returnCode);
      return;
    }

    if ($io->confirm('Deseja executar a nova migration agora? (rodar doctrine:migrations:migrate)', false)) {
      $io->section('Executando migrations');
      $migrateCmd = sprintf('cd %s && php bin/console doctrine:migrations:migrate --no-interaction', escapeshellarg($projectRoot));
      passthru($migrateCmd, $migrateCode);
      if ($migrateCode !== 0) {
        $io->warning('Erro ao executar migrations: codigo ' . $migrateCode);
      } else {
        $io->writeln('Migrations executadas com sucesso');
      }
    }
  }

  /**
   * Insere o método getUniqueFields na classe da entidade se não existir
   */
  private function addGetUniqueFieldsToEntityFile(string $entityName, array $uniqueFields, SymfonyStyle $io): void
  {
    $projectRoot = dirname(__DIR__, 2);
    $entityPath = $projectRoot . '/src/Entity/' . $entityName . '.php';

    if (!file_exists($entityPath)) {
      $io->warning("Arquivo de entidade não encontrado para adicionar getUniqueFields: {$entityPath}");
      return;
    }

    $content = file_get_contents($entityPath);

    // Se já existe um método getUniqueFields, não altera
    if (stripos($content, 'function getUniqueFields(') !== false) {
      $io->writeln("Entidade já possui getUniqueFields, pulando: {$entityPath}");
      return;
    }

    // Prepara array PHP com os campos únicos
    $items = array_map(function($f){ return "'" . $f . "'"; }, $uniqueFields);
    $arrayCode = '[' . implode(', ', $items) . ']';

    $method = "\n    public function getUniqueFields(): array\n    {\n        return {$arrayCode};\n    }\n";

    // Insere o método antes da última chave '}' do arquivo
    $pos = strrpos($content, "}\n");
    if ($pos === false) {
      // fallback: append
      $content .= $method;
    } else {
      $content = substr_replace($content, $method, $pos, 0);
    }

    file_put_contents($entityPath, $content);
    $io->writeln("Adicionado getUniqueFields na entidade: {$entityPath}");
  }

  /**
   * Extrai informações dos campos da entidade a partir dos fieldInputs
   */
  private function extractFieldsFromEntity(string $entityName, array $fieldInputs): array
  {
    $fields = [];

    foreach ($fieldInputs as $field) {
      $fields[] = [
        'name' => $field['name'],
        'type' => $field['type'],
        'doctrineType' => $field['doctrineType'],
        'nullable' => $field['nullable'],
        'unique' => $field['unique'] ?? false,
      ];
    }

    return $fields;
  }

  /**
   * Gera o conteúdo da DTO com campos e validações baseadas na entidade
   */
  private function generateDtoContent(string $entityName, string $dtoClassName, string $namespace, array $fieldInfos, bool $isCreate): string
  {
    $code = "<?php\n\n";
    $code .= "namespace " . $namespace . ";\n\n";
    $code .= "use App\Application\Dto\Common\BaseDto;\n";
    $code .= "use Symfony\Component\Validator\Constraints as Assert;\n\n";
    $code .= "class " . $dtoClassName . " extends BaseDto\n";
    $code .= "{\n";

    // Gera propriedades com validações
    foreach ($fieldInfos as $field) {
      $fieldName = $field['name'];
      $nullable = $field['nullable'];
      $unique = $field['unique'];
      $type = $field['type'];

      // Tipo PHP baseado no tipo doctrine
      $phpType = $this->getPhpTypeFromDoctrineType($field['doctrineType']);

      // Validações
      $validations = [];

      if (!$nullable && $isCreate) {
        // Campos obrigatórios na create
        // Use NotBlank for strings, NotNull for other scalar types (so false or 0 are allowed)
        if ($type === 'string') {
          $validations[] = "Assert\\NotBlank(message: 'Este campo é obrigatório')";
        } else {
          $validations[] = "Assert\\NotNull(message: 'Este campo é obrigatório')";
        }
      }

      if ($type === 'string') {
        $validations[] = "Assert\\Type('string')";
      } elseif ($type === 'int') {
        $validations[] = "Assert\\Type('integer')";
      } elseif ($type === 'bool') {
        $validations[] = "Assert\\Type('boolean')";
      }

      if ($unique) {
        // Unique validation is not a simple Assert constraint on scalar fields.
        // We'll add a comment marker for manual handling or class-level UniqueEntity if needed.
        $validations[] = "// unique";
      }

      // Gera atributo com validações
      if (!empty($validations)) {
        foreach ($validations as $validation) {
          // If validation is a comment marker, place it as a comment
          if (strpos($validation, '//') === 0) {
            $code .= "    " . $validation . "\n";
            continue;
          }

          // Emit attribute line like: #[Assert\NotBlank(message: '...')]
          $code .= "    #[" . $validation . "]\n";
        }
      }

      // Propriedade
      $nullableStr = $nullable ? '?' : '';
      $code .= "    public " . $nullableStr . $phpType . " $" . $fieldName;
      if ($nullable) {
        $code .= " = null";
      }
      $code .= ";\n\n";
    }

    $code .= "}\n";

    return $code;
  }

  /**
   * Retorna o tipo PHP equivalente ao tipo Doctrine
   */
  private function getPhpTypeFromDoctrineType(string $doctrineType): string
  {
    $typeMap = [
      'string' => 'string',
      'integer' => 'int',
      'boolean' => 'bool',
      'datetime' => '\DateTime',
      'text' => 'string',
    ];

    return $typeMap[$doctrineType] ?? 'mixed';
  }
}
