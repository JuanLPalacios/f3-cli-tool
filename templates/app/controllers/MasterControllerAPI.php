{{'<?php'}}
namespace Controllers;

class MasterControllerAPI extends MasterController {

	function beforeroute($f3,$args) {
		super->beforeroute($f3,$args);
	}

	function afterroute($f3) {
		super->afterroute($f3);
		header('Content-Type: application/json');
		echo \View::instance()->render('_layouts/' . ($f3->exists('layout', $layout) ? $layout : 'default') . '.htm');
	}

	function jsonResponse($data, $status = 'success') {
		$f3 = \Base::instance();
		header('Content-Type: application/json');
		echo $f3->stringify([
			"status" => $status,
			"data" => $data
		]);
	}
	
	function __construct() {
		parent::__construct();
	}
}
