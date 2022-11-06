<?php

namespace Commands;

abstract class BaseCommand extends \Ahc\Cli\Input\Command
{
  /** @var string Current working dir */
  protected $_workDir;

  public function __construct()
  {
    $this->f3 = \Base::instance();
    $this->_workDir  = \realpath(\getcwd());
    $this->color = new \Ahc\Cli\Output\Color;
    $this->template = \Template::instance();
    $this->template->filter('section', '\Helpers\ConfigHelper::instance()->renderConfig');
    $this->template->filter('capitalize', 'ucfirst');
    $this->template->filter('pluralaize', '\Helpers\Inflect::instance()->pluralize');
    $this->template->filter('singularize', '\Helpers\Inflect::instance()->singularize');
    $this->template->filter('snake_case', '\Helpers\StyleHelper::instance()->snakeCase');
    $this->template->filter('var', '\Helpers\TemplateHelper::instance()->var');
    $this->template->filter('input', '\Helpers\TemplateHelper::instance()->input');
    $this->template->filter('display', '\Helpers\TemplateHelper::instance()->display');
    $this->defaults();
    $this->onConstruct();
  }

  protected function onConstruct()
  {
    // ;)
  }

  protected function getTemplatePaths(array $parameters): array
  {
    // Phint provided path.
    $templatePaths = [__DIR__ . '/../../resources'];
    $userPath      = $parameters['template'] ?? null;

    if (empty($userPath)) {
      return $templatePaths;
    }

    $userPath = $this->_pathUtil->expand($userPath, $this->_workDir);

    // User supplied path comes first.
    \array_unshift($templatePaths, $userPath);

    return $templatePaths;
  }



  function generateAs($template, $file, $overwrite = null)
  {
    $interactor = new \Ahc\Cli\IO\Interactor;
    $dirPath = dirname($file);
    if (is_null($overwrite) && file_exists($file))
      $overwrite = $interactor->confirm("{$file} alredy exixt. overwrite?", 'y');
    if (!file_exists($dirPath))
      mkdir($dirPath, 0777, true);
    if ((!$overwrite) && file_exists($file)) {
      echo sprintf("[%s]%s\n", $this->color->warn('skiped'), $file);
      return;
    }
    $message = file_exists($file) ? $this->color->ok('overwrite') : $this->color->ok('created');
    $fs = fopen($file, "w") or die("Unable to write to {$file}!");
    echo "[{$message}]{$file}\n";
    //echo sprintf("%s\n",realpath($file));
    fwrite($fs, $this->template->render($template));
    fclose($fs);
  }

  function generate($file, $dir, $overwrite = null)
  {
    $this->generateAs($file, $dir . DIRECTORY_SEPARATOR . $file, $overwrite);
  }

  function generateFiles($files, $dir, $overwrite = null)
  {
    foreach ($files as &$file) {
      $this->generate($file, $dir, $overwrite);
    }
  }

  function generateFilesAs($files, $dir, $overwrite = null)
  {
    foreach ($files as &$file) {
      $this->generateAs($file[0], $dir . DIRECTORY_SEPARATOR . $file[1], $overwrite);
    }
  }

  function deleteTmp()
  {
    array_map('unlink', glob("tmp/*.*"));
    rmdir('tmp');
  }
}
