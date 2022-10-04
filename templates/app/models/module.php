<?php
namespace Model;

class Module extends \Helper\Model {
	protected
		$fields = array(
			'id'			=> ['type' => 'INT', 'nullable' => false, 'index' => true, 'unique' => true, 'default' => 0],
			'name'			=> ['type' => 'VARCHAR128', 'nullable' => false, 'default' => ''],
			'description'	=> ['type' => 'VARCHAR512', 'nullable' => false, 'default' => ''],
			'icon'			=> ['type' => 'VARCHAR128', 'nullable' => false, 'default' => ''],
			'template'		=> ['type' => 'TEXT', 'default' => '<></>'],
			'values' => array(
				'has-many' => array('\Model\Value','module'),
			),
			'submodules' => array(
				'has-many' => array('\Model\Module','parent'),
			),
			'parent' => array(
				'belongs-to-one' => '\Model\Module',
			),
		),
		$table = 'module',//table name
		$fluid = true,      // Update table model to match def
		$primary = 'id';
}