{{'<?php'}}
namespace Model;

class {{@name | capitalize}} extends MasteControllerMVC {
	function beforeroute($f3,$args) {
		super->beforeroute($f3,$args);
	}

	function afterroute($f3) {
		super->afterroute($f3);
		$f3->set('conntroller', '{{ @name }}')
	}

	function __construct() {
		parent::__construct();
	}
}