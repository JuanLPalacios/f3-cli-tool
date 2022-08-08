{{'<?php'}}
namespace Model;

class {{@name | capitalize}} extends \DB\SQL\Mapper {
	function __construct($f3=\Base::instance()) {
		parent::__construct($f3->exists('sources.{{@source_name}}.instance', $source)?$source:new \DB\SQL($f3-get('sources.{{@source_name}}.dsn'), $f3-get('sources.{{@source_name}}.user'), $f3-get('sources.{{@source_name}}.pw')), '{{@table_name}}' );
		if(!isset($source))
			$f3->set($this->db);
	}
}