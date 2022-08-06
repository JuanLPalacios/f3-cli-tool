<?php
require('vendor/autoload.php');

const DEFAULT_LICENCE = 'ISC';
const COLOR = new Ahc\Cli\Output\Color;
$f3=Base::instance();
$f3->set('UI', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR);
$f3->set('AUTOLOAD', dirname(__FILE__) . DIRECTORY_SEPARATOR );
$app = new Ahc\Cli\Application('f3-cli-tool', '0.1.0');

function generateAs($template, $file, $overwrite = null) {
  $interactor = new Ahc\Cli\IO\Interactor;
  $dirPath = dirname($file);
  if(is_null($overwrite) && file_exists($file)) 
    $overwrite = $interactor->confirm("{$file} alredy exixt. overwrite?", 'y');
  if(!file_exists($dirPath))
    mkdir($dirPath, 0777, true);
  if((!$overwrite) && file_exists($file)){
     echo sprintf("[%s]%s\n", COLOR->warn('skiped'), $file);
    return;
  }
  $message = file_exists($file) ? COLOR->ok('overwrite') : COLOR->ok('created');
  $fs = fopen($file, "w") or die("Unable to write to {$file}!");
  echo "[{$message}]{$file}\n";
  //echo sprintf("%s\n",realpath($file));
  $templat = \Template::instance();
  $templat->filter('section','\Helpers\ConfigHelper::instance()->renderConfig');
  fwrite($fs, $templat->render($template));
  fclose($fs);
}
function generate($file, $dir, $overwrite = null) {
  generateAs($file, $dir . DIRECTORY_SEPARATOR . $file, $overwrite);
}

function generateFiles($files, $dir, $overwrite = null) {
  foreach ($files as &$file) {
    generate($file, $dir, $overwrite);
  }
}

function deleteTmp() {
  array_map('unlink', glob("tmp/*.*"));
  rmdir('tmp');
}

$app
  ->logo('non-oficial CLI tool for Fat-Free Framework MVC scaffolding');
  
$app
  ->add((new Ahc\Cli\Input\Command('app', 'create a new project'))
    ->argument('[name]', 'project name to be created')
    ->argument('[description]', 'project\'s description')
    ->argument('[license]', 'project\'s license')
    ->option('-y, --yes-default', 'yes to defaults')
    ->action(function ($name, $description, $license, $yesDefault) {
      $interactor = new Ahc\Cli\IO\Interactor;
      $dirPath =  $name ?: '.';
      $name = $name ?: basename(realpath($dirPath));
      if(!$yesDefault) $name = $interactor->prompt("Project name", $name);
      if(!$yesDefault) $description = $interactor->prompt('description', $description);
      if(!$yesDefault) $license = $interactor->prompt('license', DEFAULT_LICENCE);
      $license =  (!$license) ? DEFAULT_LICENCE : $license;
      $f3=Base::instance();
      $f3->set('username', trim(substr(`git config -l | grep user.name`,10)));
      $f3->set('name', $name);
      $f3->set('description',$description);
      $f3->set('license', $license);
      generateFiles([
        'composer.json',
        'public_html\.htaccess',
        'public_html\index.php',
        'config\config.dev.ini',
        'config\config.production.ini',
        'config\config.test.ini',
        'config\routes.ini',
        'config\settings.ini'
      ], $dirPath, $yesDefault);
      deleteTmp();
      }));
      
      $app
        ->add((new Ahc\Cli\Input\Command('model', 'Add model from datasource'))
          ->action(function ($name, $description, $license, $yesDefault) {
            $interactor = new Ahc\Cli\IO\Interactor;
            $f3=Base::instance();
            $f3->config('./config/config.dev.ini');
            $sources = $f3->get('sources');
            $source_names = array_keys($sources);
            $name = $interactor->choice('Select the source', $source_names, key_exists(0, $source_names)?$source_names[0]:NULL);
            echo $name;
            //deleteTmp();
            }));

      
            $app
            ->add((new Ahc\Cli\Input\Command('datasource', 'Add datasource'))
              ->action(function ($name, $description, $license, $yesDefault) {
                $interactor = new Ahc\Cli\IO\Interactor;
                $name = $interactor->prompt("Data source name");
                $client = $interactor->choice('Select the client', ['Jig', 'Mongo', 'SQL'], 'Jig');
                switch ($client) {
                  case 'Jig':
                    $params = [
                      "dir" => $interactor->prompt("Storage directory", "../data/{$name}/"),
                      "format" => $interactor->choice('Format', ["JSON" => \DB\Jig::FORMAT_JSON , "Serialized" => \DB\Jig::FORMAT_Serialized], 'JSON')
                    ];
                    break;
                  
                  case 'Mongo':
                    $params = [
                      "dsn" => $interactor->prompt("Server", 'mongodb://localhost:27017'),
                      "dbname" => $interactor->prompt("Database name"),
                      "options" => []
                    ];
                    break;
                    
                  default:
                    $engine = $interactor->choice('Engine', [
                      "mysql" => 'MySQL 5.x',
                      "sqlite" => 'SQLite 3 and SQLite 2',
                      "pgsql" => 'PostgreSQL',
                      "sqlsrv" => 'Microsoft SQL Server / SQL Azure',
                      "mssql" => 'Microsoft SQL Server',
                      "dblib" => 'FreeTDS',
                      "sybase" => 'Sybase',
                      "odbc" => 'ODBC v3',
                      "oci" => 'Oracle'
                    ], 'mysql');
                    switch ($engine) {
                      case 'mysql':
                        $host = $interactor->prompt('Host', 'localhost');
                        $port = $interactor->prompt('Port', 3307);
                        $dbname = $interactor->prompt("DB Name", $name);
                        $dsn = "mysql:host={$host};port={$port};dbname={$dbname}";
                        break;
                      case 'sqlite':
                        $file = $interactor->prompt('File', ':memory:');
                        $dsn = "sqlite:{$file}";
                        break;
                      case 'pgsql':
                        $host = $interactor->prompt('Host', 'localhost');
                        $port = $interactor->prompt('Port', 5432);
                        $dbname = $interactor->prompt("DB Name", $name);
                        $dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";
                        break;
                      case 'sqlsrv':
                        $server = $interactor->prompt('Server', 'localhost,1521');
                        $database = $interactor->prompt("Database", $name);
                        $dsn = "sqlsrv:Server={$server};Database={$database}";
                        break;
                      case 'odbc':
                        $dbname = $interactor->prompt('Name', $name);
                        $dsn = "odbc:{$dbname}";
                        break;
                      case 'oci':
                        $dbname = $interactor->prompt('Server', "//localhost:1521/{$name}");
                        $dsn = "oci:dbname={$dbname}";
                        break;
                      default:
                        $host = $interactor->prompt('Host', 'localhost');
                        $dbname = $interactor->prompt("DB Name", $name);
                        $dsn = "{$engine}:host={$host};dbname={$dbname}";
                        break;
                    }
                    $params = [
                      "dsn" => $dsn,
                      "user" => $interactor->prompt('User'),
                      "pw" => $interactor->promptHidden('Password'),
                      "options" => []
                    ];
                    break;
                }
                $f3=Base::instance();
                $original = $f3->hive();
                $f3->config('./config/config.dev.ini');
                
                $f3->set('sources.' . $name, array_merge(["client" => $client], $params));
                $f3->set('globals', array_map('unserialize', array_diff(array_map('serialize',$f3->hive()), array_map('serialize',$original))));
                generateFiles([
                  'config\config.dev.ini',
                  'config\config.production.ini',
                  'config\config.test.ini'
                ], '.', true);
                //deleteTmp();
                }));
      /*
    ->command('add', 'Stage changed files', 'a') // alias a
        // Set options and arguments for this command
        ->arguments('<path> [paths...]')
        ->option('-f --force', 'Force add ignored file', 'boolval', false)
        ->option('-N --intent-to-add', 'Add content later but index now', 'boolval', false)
        // Handler for this command: param names should match but order can be anything :)
        ->action(function ($path, $paths, $force, $intentToAdd) {
            array_unshift($paths, $path);

            echo ($intentToAdd ? 'Intent to add ' : 'Add ')
                . implode(', ', $paths)
                . ($force ? ' with force' : '');

            // If you return integer from here, that will be taken as exit error code
        })
        // Done setting up this command for now, tap() to retreat back so we can add another command
        ->tap()
    ->command('checkout', 'Switch branches', 'co') // alias co
        ->arguments('<branch>')
        ->option('-b --new-branch', 'Create a new branch and switch to it', false)
        ->option('-f --force', 'Checkout even if index differs', 'boolval', false)
        ->action(function ($branch, $newBranch, $force) {
            echo 'Checkout to '
                . ($newBranch ? 'new ' . $branch : $branch)
                . ($force ? ' with force' : '');
        })
;


program.command('datasource')
    .description('generate scaffold for model')
    .argument('[name]', 'project name to be created')
    .argument('[engine]', 'project engine to be created')
    .argument('[login]', 'project login to be created')
    .argument('[actions...]', 'model to scaffold', routeParse, ['GET @{controler | snake}_list: /{controler | snake}/@id => {controler}->index', 'new', 'edit', 'create', 'read', 'update', 'delete'])
    .option('-s, --separator <char>', 'separator character', ',')
    .action((model, actions, options) => {
      console.log('scaffold', model, actions, options);
    });
    
program.command('controller')
    .description('generate scaffold for model')
    .argument('<type>', 'project type to be created')
    .argument('[model/name]', 'project model/name to be created')
    .argument('[actions...]', 'model to scaffold', routeParse, ['GET @{controler | snake}_list: /{controler | snake}/@id => {controler}->index', 'new', 'edit', 'create', 'read', 'update', 'delete'])
    .option('-s, --separator <char>', 'separator character', ',')
    .action((model, actions, options) => {
        console.log('scaffold', model, actions, options);
    });

$app->parse();
*/
$app->handle($_SERVER['argv']);