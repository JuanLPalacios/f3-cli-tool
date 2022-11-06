<?php

if (\file_exists(__DIR__ . '/../../../autoload.php')) {
    require __DIR__ . '/../../../autoload.php';
} elseif (\file_exists(__DIR__ . '/../../autoload.php')) {
    require __DIR__ . '/../../autoload.php';
} else {
    require __DIR__ . '/../vendor/autoload.php';
}

$logo = "
F3 Project Scaffolding Tool
============================
";

$f3=Base::instance();
$f3->set('UI', __DIR__  . '/../templates/');
$f3->set('AUTOLOAD', __DIR__  . '/../src/' );
$app = new Ahc\Cli\Application('f3-cli-tool', '0.1.0');

$app->add(new Commands\App, 'a');
      
$app->logo($logo)->handle($_SERVER['argv']);