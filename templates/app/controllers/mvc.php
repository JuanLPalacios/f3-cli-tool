{{'<?php'}}
namespace Model;

class {{@name | capitalize}} extends MasteControllerMVC {

	function index($f3, $params){
		${{ @model_name | snake_case }} = new \Models\{{ @model_name }}();
		$f3->set('{{ @model_name | snake_case }}', ${{ @model_name | snake_case }}->find());
	}

	function create($f3, $params){
		${{ @model_name | snake_case }} = new \Models\{{ @model_name }}();
		$f3->set('{{ @model_name | snake_case }}', ${{ @model_name | snake_case }});
	}

	function delete($f3, $params){
		${{ @model_name | snake_case }} = new \Models\{{ @model_name }}();
		${{ @model_name | snake_case }}->erase([
			<repeat group="{{ @model->schema() }}" key="{{ @field_name }}" value="{{ @field }}">
				<check if="{{ $field['pkey'] }}">'{{ @field_name }}'=>$params['id'],</check>
			</repeat>

		{{ ']);' }}
	}

	function edit($f3, $params){
		${{ @model_name | snake_case }} = new \Models\{{ @model_name }}();
		$f3->set('{{ @model_name | snake_case }}', ${{ @model_name | snake_case }}->load([
			<repeat group="{{ @model->schema() }}" key="{{ @field_name }}" value="{{ @field }}">
				<check if="{{ $field['pkey'] }}">'{{ @field_name }}'=>$params['id'],</check>
			</repeat>

		{{ ']));' }}
	}

	function read($f3, $params){
		${{ @model_name | snake_case }} = new \Models\{{ @model_name }}();
		$f3->set('{{ @model_name | snake_case }}', ${{ @model_name | snake_case }}->load([
			<repeat group="{{ @model->schema() }}" key="{{ @field_name }}" value="{{ @field }}">
				<check if="{{ $field['pkey'] }}">'{{ @field_name }}'=>$params['id'],</check>
			</repeat>

		{{ ']));' }}
	}

	function update($f3, $params){
		${{ @model_name | snake_case }} = new \Models\{{ @model_name }}();
		$f3->set('{{ @model_name | snake_case }}', ${{ @model_name | snake_case }}->load([
			<repeat group="{{ @model->schema() }}" key="{{ @field_name }}" value="{{ @field }}">
				<check if="{{ $field['pkey'] }}">'{{ @field_name }}'=>$params['id'],</check>
			</repeat>

		{{ ']));' }}
		${{ @model_name | snake_case }}->copyFrom('POST');
		${{ @model_name | snake_case }}->save();
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