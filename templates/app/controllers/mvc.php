{{'<?php'}}
namespace Model;

class {{@name | capitalize}} extends MasteControllerMVC {

	function index($f3, $params){
		${{ @model | snake_case }} = new \Models\{{ @model }}();
		$f3->set('{{ @model | snake_case }}', ${{ @model | snake_case }}->find());
	}

	function create($f3, $params){
		${{ @model | snake_case }} = new \Models\{{ @model }}();
		$f3->set('{{ @model | snake_case }}', ${{ @model | snake_case }});
	}

	function delete($f3, $params){
		${{ @model | snake_case }} = new \Models\{{ @model }}();
		${{ @model | snake_case }}->erase(['id'=>$params['id']]);
	}

	function edit($f3, $params){
		${{ @model | snake_case }} = new \Models\{{ @model }}();
		$f3->set('{{ @model | snake_case }}', ${{ @model | snake_case }}->load(['id'=>$params['id']]));
	}

	function read($f3, $params){
		${{ @model | snake_case }} = new \Models\{{ @model }}();
		$f3->set('{{ @model | snake_case }}', ${{ @model | snake_case }}->load(['id'=>$params['id']]));
	}

	function update($f3, $params){
		${{ @model | snake_case }} = new \Models\{{ @model }}();
		$f3->set('{{ @model | snake_case }}', ${{ @model | snake_case }}->load(['id'=>$params['id']]));
		${{ @model | snake_case }}->copyFrom('POST');
		${{ @model | snake_case }}->save();
	}

	function beforeroute($f3,$args) {
		super->beforeroute($f3,$args);
		$f3->set('conntroller', '{{ @name }}');
	}

	function afterroute($f3) {
		super->afterroute($f3);
	}

	function __construct() {
		parent::__construct();
	}
}