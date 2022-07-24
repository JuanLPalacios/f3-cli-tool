<?php
require('vendor/autoload.php');

const DEFAULT_LICENCE = 'ISC';
const COLOR = new Ahc\Cli\Output\Color;
$f3=Base::instance();
$f3->set('UI', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'templates');
$app = new Ahc\Cli\Application('f3-cli-tool', '0.1.0');

function generateAs($template, $file, $overwrite = null) {
  $interactor = new Ahc\Cli\IO\Interactor;
  $dirPath = dirname($file);
  if(is_null($overwrite) && file_exists($file)) 
    $overwrite = $interactor->confirm("{$file} alredy exixt. overwite?", 'n');
  if(!file_exists($dirPath))
    mkdir($dirPath, 0777, true);
  if(!$overwrite){
     echo sprintf("[%s]%s", COLOR->warn('skiped'), $file);
    return;
  }
  $message = file_exists($file) ? COLOR->ok('overwrite') : COLOR->ok('created');
  echo "[{$message}]{$file}\n";
  $fs = fopen($file, "w") or die("Unable to write to {$file}!");
  fwrite($fs, \Template::instance()->render(realpath($template)));
  fclose($fs);
}
function generate($file, $dir, $overwrite = null) {
  generateAs($file, $dir . DIRECTORY_SEPARATOR . $file, $overwrite);
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
      if((!$yesDefault)&&(!$name)) $name = $interactor->prompt("Project name", getcwd());
      if((!$yesDefault)&&(!$description)) $description = $interactor->prompt('description', '');
      if((!$yesDefault)&&(!$license)) $license = $interactor->prompt('license', DEFAULT_LICENCE);
      $f3=Base::instance();
      $dirPath =  (!$name) ? getcwd() : $name;
      $name =  basename(realpath($dirPath));
      $license =  (!$license) ? DEFAULT_LICENCE : $license;
      $f3->set('username', trim(substr(`git config -l | grep user.name`,10)));
      $f3->set('name', $name);
      $f3->set('description',$description);
      $f3->set('license', $license);
      generate("composer.json", $dirPath, $yesDefault);
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