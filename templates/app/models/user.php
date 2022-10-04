<?php
namespace Model;

class User extends \Helper\Model {
	protected
		$fields = array(
			'id'      => ['type' => 'INT', 'nullable' => false, 'index' => true, 'unique' => true, 'default' => 0, 'default' => ''],
			'email'    => ['type' => 'VARCHAR128', 'nullable' => false, 'default' => ''],
			'username' => ['type' => 'VARCHAR128', 'nullable' => false, 'default' => ''],
			'password' => ['type' => 'VARCHAR512', 'nullable' => false, 'default' => ''],
			'rights'   => ['type' => 'JSON'],
			'created'  => ['type' => 'TIMESTAMP'],
			'updated'  => ['type' => 'TIMESTAMP'],
			'deleted'  => ['type' => 'BOOLEAN']
		),
		$table = 'user',
		$fluid = true,
		$primary = 'id';
}