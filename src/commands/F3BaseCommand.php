<?php

namespace F3CliTool\Comands;

const COLOR = new Ahc\Cli\Output\Color;

const TEMPLATE = \Template::instance();
TEMPLATE->filter('section','\Helpers\ConfigHelper::instance()->renderConfig');
TEMPLATE->filter('capitalize','ucfirst');
TEMPLATE->filter('pluralaize','\Helpers\Inflect::instance()->pluralize');
TEMPLATE->filter('singularize','\Helpers\Inflect::instance()->singularize');
TEMPLATE->filter('snake_case','\Helpers\StyleHelper::instance()->snakeCase');
TEMPLATE->filter('var','\Helpers\TemplateHelper::instance()->var');
TEMPLATE->filter('input','\Helpers\TemplateHelper::instance()->input');
TEMPLATE->filter('display','\Helpers\TemplateHelper::instance()->display');


abstract class F3BaseCommand extends Command
{
    public function __construct()
    {
        $this->$f3=Base::instance();
    }

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
      fwrite($fs, TEMPLATE->render($template));
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
}