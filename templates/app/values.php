<?php 

class Values extends \Helper\Controller {

	function update($f3,$params){
		$values = new \Model\Value();
		unset($params[0]);
		if(key_exists('id',$params)) {
			$values->load(['id = :id',':id'=>$params['id']]);
			if($values->dry()) $f3->error(404);
			$now = new DateTime("now");
			if($value['expires']<$now){
				$next = $value->states->getBy('start')[$value['expires']];
				$value['value'] = $next['record'];
				$value->states->orderBy('start DESC');
				$dates = $value->states->getAll('record');
				$i = array_search($value['expires'], $dates);
				if($i==0) $value['expires'] = NULL;
				else $value['expires'] = $dates[$i-1];
				$value->save();
			}
			$f3->set('data',$values->cast());
		}
	}

	function get($f3,$params){
		$values = new \Model\Value();
		unset($params[0]);
		if(key_exists('id',$params)) {
			$values->load(['id = :id',':id'=>$params['id']]);
			if($values->dry()) $f3->error(404);
			$f3->set('data',$values->cast());
		}
		else {
			$f3->set('data',$values->afind(implode(" AND ", array_map(fn($key): string => "{$key}=@{$key}", array_keys($params))), $params));
		}
	}

	function post($f3,$params){
		$value = new \Model\Value();
		$params = $value->clean($params);
		$value->copyfrom($params);
		$data = json_decode($f3->get('BODY'),true);
		$data = $value->clean($data);
		$value->copyfrom($data);
		$value['id'] = time();
		$history = new \Model\History();
		$history['id'] = time();
		$history['record'] = $data['value'];
		$history['start'] = new \Datetime();
		$history->save();
		$value['states'] = [$history];
		$value['expires'] = NULL;
		$value->save();
		$value->copyto('data');
	}

	function put($f3,$params){
		$value = new \Model\Value();
		if(key_exists('id',$params)) {
			$value->load(['id = :id',':id'=>$params['id']]);
			if($value->dry()) $f3->error(404);
			$data = json_decode($f3->get('BODY'),true);
			$start =  new \Datetime($data['start']);
			$now = new DateTime("now");
			$interval = $now->diff($start);
			$isValid = $interval->i >= 0;
			if ($isValid) {
				if($value['expires'] == NULL){
					$value['expires'] = $start;
				}
				else if($start < $value['expires']){
					$value['expires'] = $start;
				}
				$history = new \Model\History();
				$history['id'] = time();
				$history['record'] = $data['value'];
				$history->save();
				$value['states'][] = $history;
			}
			$data = $value->clean($data);
			$value->copyfrom($data);
			$value->save();
			$value->copyto('data');
		}
		else $f3->error(403);
	}

	function delete($f3,$params){
		$value = new \Model\Value();
		if(key_exists('id',$params)) {
			$value->erase(['id = :id',':id'=>$params['id']]);
		}
		else $f3->error(403);
	}

}