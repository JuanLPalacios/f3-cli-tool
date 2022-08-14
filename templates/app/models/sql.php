{{'<?php'}}
namespace Model;

class {{@name | capitalize}} extends \DB\SQL\Mapper {
	function __construct(\DB\SQL $db = NULL) {
		if(!$db){
			$f3 = \Base::instance();
			$db = $f3->exists('sources.{{@source_name}}.instance', $source) ? $source : new \DB\SQL($f3->get('sources.{{@source_name}}.dsn'), $f3->get('sources.{{@source_name}}.user'), $f3->get('sources.{{@source_name}}.pw'));
			if(!isset($source))
				$f3->set('sources.{{@source_name}}.instance', $db);
		}
		parent::__construct( $db, '{{@table_name}}' );
	}
}