<?php
namespace Model;

class Value extends \Helper\Model {
	protected
		$fields = array(
			'id'		=> ['type' => 'INT', 'nullable' => false, 'index' => true, 'unique' => true, 'default' => 0],
			'type'		=> ['type' => 'JSON', 'nullable' => false, 'default' => '{"name":"","type":"string"}'],
			'value'		=> ['type' => 'JSON', 'nullable' => false, 'default' => '{"value":""}'],
			'expires'	=> ['type' => 'DATETIME'],
			'module' => array(
				'belongs-to-one' => '\Model\Module',
			),
			'states' => array(
				'has-many' => array('\Model\History','value'),
			),
		),
		$table = 'value',
		$fluid = true,
		$primary = 'id';
}