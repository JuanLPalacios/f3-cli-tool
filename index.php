<?php
require('./vendor/autoload.php');

const DEFAULT_LICENCE = 'ISC';
$app = new Ahc\Cli\Application('f3-cli-tool', '0.1.0');

function generateAs($template, $file, $overwrite = null) {
  $dirPath = dirname($file);
  if(($overwrite==null) && file_exists($file)) 
    $overwrite = (strpos(strtolower(readline("{$file} alredy exixt. overwite? (y/n): ")), 'y') !== false);
  if(!file_exists($dirPath))
    mkdir($dirPath, 0777, true);
  $fs = fopen($file, "w") or die("Unable to write to {$file}!");
  fwrite($fs, \Template::instance()->render($template));
  fclose($fs);
}
function generate($file, $dir, $overwrite = false) {
  generateAs('./templates' . DIRECTORY_SEPARATOR . $file, $dir . DIRECTORY_SEPARATOR . $file, $overwrite);
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
      if((!$yesDefault)&&(!$name)) $name = readline("Project name (" . basename(realpath(getcwd())) . "): ");
      if((!$yesDefault)&&(!$description)) $description = readline('description: ');
      if((!$yesDefault)&&(!$license)) $license = readline("license ({DEFAULT_LICENCE}): ");
      $f3=Base::instance();
      $dirPath =  ($yesDefault || !$name) ? getcwd() : $name;
      $name =  basename(realpath($dirPath));
      $license =  ($yesDefault || !$license) ? DEFAULT_LICENCE : $license;
      $f3->set('username', trim(substr(`git config -l | grep user.name`,10)));
      $f3->set('name', $name);
      $f3->set('description',$description);
      $f3->set('license', $license);
      generate("composer.json", $dirPath, !$yesDefault);
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