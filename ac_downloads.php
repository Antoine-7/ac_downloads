<?php
defined('ROOT') OR exit('No direct script access allowed');
define('AC_FOLDER','ac_downloads/');
define('AC_DATA_FILE','download.json');

## Fonction d'installation
function ac_downloadInstall(){
	if(!file_exists( DATA_PLUGIN . AC_FOLDER . AC_DATA_FILE)){
		@mkdir( UPLOAD . AC_FOLDER);
		$data = array();
		util::writeJsonFile( DATA_PLUGIN . AC_FOLDER . AC_DATA_FILE, $data);
	}
}

## Code relatif au plugin

include PLUGINS . AC_FOLDER . 'ac_downloadItem.php';

class ac_downloads {
	private $items;

	public function __construct(){
		$data = array();
		if(file_exists( DATA_PLUGIN . AC_FOLDER . AC_DATA_FILE)){
			$temp = util::readJsonFile( DATA_PLUGIN . AC_FOLDER . AC_DATA_FILE);
			//$temp = util::sort2DimArray($temp, 'date', 'desc');
			foreach($temp as $key=>$value){
				$data[] = new ac_downloadItem($value);
			}
		}
		$this->items = $data;
	}

	public function createItem($id){
		foreach($this->items as $obj){
			if($obj->getId() == $id) return $obj;
		}
		return false;
	}

	private function saveItems(){
		$data = array();
		foreach($this->items as $key=>$value){
			$data[] = array(
				'id' => $value->getId(),
				'title' => $value->getTitle(),
				'content' => $value->getContent(),
				'link' => $value->getLink()
			);
		}
		if(util::writeJsonFile(DATA_PLUGIN . AC_FOLDER . AC_DATA_FILE, $data)){
			return true;
		}
		return false;
	}

	public function saveItem($obj){
		$id = $obj->getId();
		if($id == ''){
			$obj->setId(uniqid());
			$this->items[] = $obj;
		}
		else{
			foreach($this->items as $key=>$value){
				if($id == $value->getId()){
					$this->items[$key] = $obj;
				}
			}
		}
		return $this->saveItems();
	}

	public function getItems(){
		return $this->items;
	}

	public function delItem($obj){
		foreach($this->items as $key=>$value){
			if($obj->getId() == $value->getId()){
				unset($this->items[$key]);
			}
		}
		return $this->saveItems();
	}
}