{{'<?php'}}
namespace Controllers;

class MasterControllerMVC extends MasterController {

	function beforeroute($f3,$args) {
		super->beforeroute($f3,$args);
	}

	function afterroute($f3) {
		super->afterroute($f3);
		echo \View::instance()->render('_layouts/' . ($f3->exists('layout', $layout) ? $layout : 'default') . '.htm');
	}

	function __construct() {
		parent::__construct();
	}
}
