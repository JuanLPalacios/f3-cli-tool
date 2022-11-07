<?php

namespace Commands;

class Scaffolding extends BaseCommand
{


  public function __construct()
  {
    parent::__construct('scaffolding', 'Add controller model scaffold');
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
        $cli_classes = get_declared_classes();
        foreach (glob('./app/models/*.php') as $file) {
          require($file);
        }
        $models = array_values(array_map(fn ($x) => substr($x, 6), preg_grep('/^Model\\\\/', array_diff(get_declared_classes(), $cli_classes))));
        $model = $interactor->choice('Controller base model', $models, key_exists(0, $models) ? $models[0] : NULL);
        $name = $interactor->prompt("Model name", \Helpers\Inflect::instance()->pluralize($model));
        $class_name = "Model\\${model}";
        $f3->set('model_name', $model);
        $f3->set('model', new $class_name());
        $f3->set('name', $name);
        echo json_encode($f3->get('model')->schema());
        $this->generateFilesAs([
          ['app\controllers\mvc.php', "app\controllers\\${name}.php"],
          ['app\views\_templates\_form.htm', "app\\views\\${name}\\_form.htm"],
          ['app\views\_templates\_form.htm', "app\\views\\${name}\\_info.htm"],
          ['app\views\_templates\_form.htm', "app\\views\\${name}\\index.htm"],
          ['app\views\_templates\_form.htm', "app\\views\\${name}\\edit.htm"],
          ['app\views\_templates\_form.htm', "app\\views\\${name}\\create.htm"],
          ['app\views\_templates\_form.htm', "app\\views\\${name}\\delete.htm"],
          ['app\views\_templates\_form.htm', "app\\views\\${name}\\view.htm"]
        ], '.');
        $f3->config('./config/routes.ini');
        $f3->route("GET \\${name}", "\\Controllers\\${name}->index");
        $f3->route("GET \\${name}\\new", "\\Controllers\\${name}->new");
        $f3->route("GET \\${name}\\@id", "\\Controllers\\${name}->view");
        $f3->route("GET \\${name}\\@id\\view", "\\Controllers\\${name}->view");
        $f3->route("GET \\${name}\\@id\\edit", "\\Controllers\\${name}->view");
        $f3->route("GET \\${name}\\@id\\delete", "\\Controllers\\${name}->view");
        $f3->route("POST \\${name}", "\\Controllers\\${name}->create");
        $f3->route("PUT|PATCH \\${name}\\@id", "\\Controllers\\${name}->update");
        $f3->route("DELETE \\${name}\\@id", "\\Controllers\\${name}->delete");
        echo json_encode($f3->get('ROUTES'));
        $this->generateFiles([
          'config\routes.ini',
        ], '.', true);
        $this->deleteTmp();
      });
  }
}
