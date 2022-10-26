<?php

namespace F3CliTool\Comands;

abstract class DataSource extends F3BaseCommand {

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

}