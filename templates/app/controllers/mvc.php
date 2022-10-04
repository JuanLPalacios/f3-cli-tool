{{'<?php'}}
<set singular="{{ @model_name | snake_case }}" plural="{{ @name | snake_case }}" />
namespace Model;

class {{@name | capitalize}} extends MasteControllerMVC {

	function index($f3, $params){
		${{ @plural }} = new \Models\{{ @model_name }}();
		$f3->set('{{ @plural }}', ${{ @plural }}->find());
	}

	function create($f3, $params){
		${{ @singular }} = new \Models\{{ @model_name }}();
		$f3->set('{{ @singular }}', ${{ @singular }});
	}

	function delete($f3, $params){
		${{ @singular }} = new \Models\{{ @model_name }}();
		${{ @singular }}->erase([
			<repeat group="{{ @model->schema() }}" key="{{ @field_name }}" value="{{ @field }}">
				<check if="{{ $field['pkey'] }}">'{{ @field_name }}'=>$params['id'],</check>
			</repeat>

		{{ ']);' }}
	}

	function edit($f3, $params){
		${{ @singular }} = new \Models\{{ @model_name }}();
		$f3->set('{{ @singular }}', ${{ @singular }}->load([
			<repeat group="{{ @model->schema() }}" key="{{ @field_name }}" value="{{ @field }}">
				<check if="{{ $field['pkey'] }}">'{{ @field_name }}'=>$params['id'],</check>
			</repeat>

		{{ ']));' }}
	}

	function read($f3, $params){
		${{ @singular }} = new \Models\{{ @model_name }}();
		$f3->set('{{ @singular }}', ${{ @singular }}->load([
			<repeat group="{{ @model->schema() }}" key="{{ @field_name }}" value="{{ @field }}">
				<check if="{{ $field['pkey'] }}">'{{ @field_name }}'=>$params['id'],</check>
			</repeat>

		{{ ']));' }}
	}

	function update($f3, $params){
		${{ @singular }} = new \Models\{{ @model_name }}();
		$f3->set('{{ @singular }}', ${{ @singular }}->load([
			<repeat group="{{ @model->schema() }}" key="{{ @field_name }}" value="{{ @field }}">
				<check if="{{ $field['pkey'] }}">'{{ @field_name }}'=>$params['id'],</check>
			</repeat>

		{{ ']));' }}
		${{ @singular }}->copyFrom('POST');
		${{ @singular }}->save();
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