<?php

class Todo {

	private $filename = 'todos.json';
	private $todos = [];

	public $config;

	/**
	 * json構造
	 * {
	 *
	 * '0' => {
	 * 		'serverid'  => '',
	 * 		'name' 		=> '',
	 * 		'assign'	=> '',
	 * 		'data' 		=> '',
	 * 		'created' 	=> '',
	 * 		'modified' 	=> ''
	 * }
	 * 
	 * 
	 * 
	 * }
	 */


	public function __construct(){

		$this->config = [
			'help' => false,
			'admin' => 333924812386861063,
		];

		if(file_exists($this->filename)){
			$json = file_get_contents($this->filename);
			$json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
			$this->todos = json_decode($json,true);
		}
	}

	public function getDefaultValue(){
		return [
			'serverid' => '',
			'name' => '',
			'assign' => '',
			'data' => '',
			'created' => date('Y:m:d H:i:s'),
			'modified' => date('Y:m:d H:i:s')
		];
	}

	public function setUrl($filename, $reload = false){
		$this->filename = $filename;
		if($reload){
			$this->reload();
		}
	}

	public function reload(){
		$this->__construct();
	}

	public function write(){
		file_put_contents($this->filename , json_encode($this->todos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
		$this->reload();
	}

	public function reset(){
		$this->todos = [];
	}

	public function add($data, $option = []){
		$_data = $this->getDefaultValue();
		if(isset($option)){
			$flg = true;
			$_data['name'] = $option['user']['name'];
		}
		$_data['data'] = $data;
		$this->todos[] = $_data;
		if($flg){
			if($option['save']){
				$this->write();
				$this->reload();
			}
		}
	}

	public function find($mode, $option = []){

		$result = [];
		if($option && $option['conditions']){
			$conditions = $option['conditions'];
			foreach($conditions as $ckey => $condition){
				foreach($this->todos as $tkey => $todo){
					if($ckey == 'id' && $this->todos[$condition]){//id検索
						$result[] = $this->todos[$condition];
						break;
					}
					if($todo[$ckey] === $condition){//その他の検索
						
						$result[] = $todo[$ckey];
						if($mode === 'first'){
							break;
						}
					}
				}
			}

			return $result;

		}else{

			if($mode === 'first'){
				return $this->todo[0];
			}else if($mode === 'all'){
				return $this->todos;
			}
		}
	}

	public function update($id, $data, $option = []){
		$this->todos[$id]['data'] = $data;
		$this->todos[$id]['modified'] = date('Y/m/d H:i:s');
	}

	public function remove($id, $option = []){
		unset($this->todos[$id]);
		if(isset($option)){
			if($option['save']){
				$this->write();
				$this->reload();
			}
		}
	}
}