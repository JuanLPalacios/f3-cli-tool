<?php

namespace Commands;

class Model extends BaseCommand
{


  public function __construct()
  {
    parent::__construct('model', 'Add model from datasource');
  }

  /**
   * Configure the command options/arguments.
   *
   * @return void
   */
  protected function onConstruct()
  {
    $this
      ->action(function ($name, $description, $license, $yesDefault) {
        $interactor = new \Ahc\Cli\IO\Interactor;
        $f3 = \Base::instance();
        $f3->config('./config/config.dev.ini');
        $sources = $f3->get('sources');
        $source_names = array_keys($sources);
        $source_name = $interactor->choice('Select the source', $source_names, key_exists(0, $source_names) ? $source_names[0] : NULL);
        $source = $sources[$source_name];
        switch ($source['client']) {
          case 'sql':
            $db = new \DB\SQL($source['dsn'], $source['user'], $source['pw']);
            $tb_names = array_map(fn ($val) => $val[array_keys($val)[0]], $db->exec('SHOW TABLES'));
            $table_name = $interactor->choice('Table to base model', $tb_names, key_exists(0, $tb_names) ? $tb_names[0] : NULL);
            $name = $interactor->prompt("Model name", \Helpers\Inflect::instance()->singularize($table_name));
            break;

          default:
            $table_name = $interactor->prompt("Resource name");
            $name = $interactor->prompt("Model name", \Helpers\Inflect::instance()->singularize($table_name));
            break;
        }
        $f3->set('source_name', $source_name);
        $f3->set('table_name', $table_name);
        $f3->set('name', $name);
        $this
          ->generateFilesAs([
            ['app\models\sql.php', "app\models\\${name}.php"]
          ], '.');
        $this
          ->deleteTmp();
      });
  }
}
