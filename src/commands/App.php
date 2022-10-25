<?php

namespace F3CliTool\Comands;

abstract class App extends F3BaseCommand {

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

}