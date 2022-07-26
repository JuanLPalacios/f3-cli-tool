{{'<?php'}}
require('../vendor/autoload.php');
$f3=Base::instance();
$f3->config('../config/settings.ini');
$f3->config('../config/routes.ini');

$host = $_SERVER['HTTP_HOST'];
if (strpos($host, 'dev.') !== false) { 
	$f3->config('../config/config.dev.ini');	
} else if (strpos($host, 'test.') !== false) { 
	$f3->config('../config/config.test.ini');	
} else {
	$f3->config('../config/config.production.ini');	
}

$f3->run();