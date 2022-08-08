{{'<?php'}}
namespace Model;

class {{@name | capitalize}} extends \DB\SQL\Mapper {
	function __construct() {
		parent::__construct(new DB\SQL({{'{{'}}@sources['{{@source_name}}']['dsn']{{'}}'}}, {{'{{'}}@sources['{{@source_name}}']['user']{{'}}'}}, {{'{{'}}@sources['{{@source_name}}']['pw']{{'}}'}}), '{{@name}}' );
	}
}