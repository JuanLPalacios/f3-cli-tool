<?php 

class Modules extends \Helper\Controller {

	function get($f3,$params){
		$modules = new \Model\Module();
		unset($params[0]);
		if(key_exists('id',$params)) {
			$modules->load(['id = :id',':id'=>$params['id']]);
			if($modules->dry()) $f3->error(404);
			$f3->set('data',$modules->cast());
		}
		else $f3->set('data',$modules->afind(implode(" AND ", array_map(fn($key): string => "{$key}=@{$key}", array_keys($params))), $params));
	}

	function post($f3,$params){
		$module = new \Model\Module();
		$params = $module->clean($params);
		$module->copyfrom($params);
		$data = json_decode($f3->get('BODY'),true);
		$data = $module->clean($data);
		$module->copyfrom($data);
		$module['id'] = time();
		$module->save();
		$module->copyto('data');
	}

	function put($f3,$params){
		$module = new \Model\Module();
		if(key_exists('id',$params)) {
			$module->load(['id = :id',':id'=>$params['id']]);
			if($module->dry()) $f3->error(404);
			$data = json_decode($f3->get('BODY'),true);
			$data = $module->clean($data);
			$module->copyfrom($data);
			$module->save();
			$module->copyto('data');
		}
		else $f3->error(403);
	}

	function delete($f3,$params){
		$module = new \Model\Module();
		if(key_exists('id',$params)) {
			$module->erase(['id = :id',':id'=>$params['id']]);
		}
		else $f3->error(403);
	}

}