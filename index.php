<?php
require __DIR__ . '/vendor/autoload.php';

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
  $templat->filter('capitalize','ucfirst');
  $templat->filter('pluralaize','\Helpers\Inflect::instance()->pluralize');
  $templat->filter('singularize','\Helpers\Inflect::instance()->singularize');
  $templat->filter('snake_case','\Helpers\StyleHelper::instance()->snakeCase');
  $templat->filter('var','\Helpers\TemplateHelper::instance()->var');
  $templat->filter('input','\Helpers\TemplateHelper::instance()->input');
  $templat->filter('display','\Helpers\TemplateHelper::instance()->display');
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

function generateFilesAs($files, $dir, $overwrite = null) {
  foreach ($files as &$file) {
    generateAs($file[0], $dir . DIRECTORY_SEPARATOR . $file[1], $overwrite);
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
      $original = $f3->hive();
      $f3->set('routes', []);
      $f3->set('sources', []);
      $f3->set('globals', array_map('unserialize', array_diff(array_map('serialize',$f3->hive()), array_map('serialize',$original))));
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
            $source_name = $interactor->choice('Select the source', $source_names, key_exists(0, $source_names)?$source_names[0]:NULL);
            $source = $sources[$source_name];
            switch ($source['client']) {
              case 'sql':
                $db=new DB\SQL($source['dsn'], $source['user'], $source['pw']);
                $tb_names = array_map(fn($val) => $val[array_keys($val)[0]], $db->exec('SHOW TABLES'));
                $table_name = $interactor->choice('Table to base model', $tb_names, key_exists(0, $tb_names)?$tb_names[0]:NULL);
                $name = $interactor->prompt("Model name",Helpers\Inflect::instance()->singularize($table_name));
                break;
              
              default:
                $table_name = $interactor->prompt("Resource name");
                $name = $interactor->prompt("Model name", Helpers\Inflect::instance()->singularize($table_name));
                break;
            }
            $f3->set('source_name', $source_name);
            $f3->set('table_name', $table_name);
            $f3->set('name', $name);
            generateFilesAs([
              ['app\models\sql.php',"app\models\\${name}.php"]
            ], '.');
            deleteTmp();
            }));
      
            $app
              ->add((new Ahc\Cli\Input\Command('scaffolding', 'Add controller model scaffold'))
                ->action(function ($name, $description, $license, $yesDefault) {
                  $interactor = new Ahc\Cli\IO\Interactor;
                  $f3=Base::instance();
                  $f3->config('./config/config.dev.ini');
                  $cli_classes = get_declared_classes();
                  foreach (glob('./app/models/*.php') as $file) {
                      require($file);   
                  }
                  $models = array_values(array_map(fn($x) => substr($x, 6), preg_grep('/^Model\\\\/', array_diff(get_declared_classes(), $cli_classes))));
                  $model = $interactor->choice('Controller base model', $models, key_exists(0, $models)?$models[0]:NULL);
                  $name = $interactor->prompt("Model name", Helpers\Inflect::instance()->pluralize($model));
                  $class_name = "Model\\${model}";
                  $f3->set('model_name', $model);
                  $f3->set('model', new $class_name());
                  $f3->set('name', $name);
                  echo json_encode($f3->get('model')->schema());
                  generateFilesAs([
                    ['app\controllers\mvc.php',"app\controllers\\${name}.php"],
                    ['app\views\_templates\_form.htm',"app\\views\\${name}\\_form.htm"],
                    ['app\views\_templates\_form.htm',"app\\views\\${name}\\_info.htm"],
                    ['app\views\_templates\_form.htm',"app\\views\\${name}\\index.htm"],
                    ['app\views\_templates\_form.htm',"app\\views\\${name}\\edit.htm"],
                    ['app\views\_templates\_form.htm',"app\\views\\${name}\\create.htm"],
                    ['app\views\_templates\_form.htm',"app\\views\\${name}\\delete.htm"],
                    ['app\views\_templates\_form.htm',"app\\views\\${name}\\view.htm"]
                  ], '.');
                  $f3->config('./config/routes.ini');
                  $f3->route("GET \\${name}","\\Controllers\\${name}->index");
                  $f3->route("GET \\${name}\\new","\\Controllers\\${name}->new");
                  $f3->route("GET \\${name}\\@id","\\Controllers\\${name}->view");
                  $f3->route("GET \\${name}\\@id\\view","\\Controllers\\${name}->view");
                  $f3->route("GET \\${name}\\@id\\edit","\\Controllers\\${name}->view");
                  $f3->route("GET \\${name}\\@id\\delete","\\Controllers\\${name}->view");
                  $f3->route("POST \\${name}","\\Controllers\\${name}->create");
                  $f3->route("PUT|PATCH \\${name}\\@id","\\Controllers\\${name}->update");
                  $f3->route("DELETE \\${name}\\@id","\\Controllers\\${name}->delete");
                  echo json_encode($f3->get('ROUTES'));
                  generateFiles([
                    'config\routes.ini',
                  ], '.', true);
                  deleteTmp();
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
                deleteTmp();
                }));
      
$app->handle($_SERVER['argv']);