<?php
	/**
	* 
	*/
	class CpanelMenu extends CpanelAppModel
	{
		var $useTable = 'cpanel_menu';
		
		var $validate = array(
			'name' => array('notempty'),
			//@todo Improve validation (ej: not white spaces)
			'match_route' => array('notempty')
		);
		
		var $actsAs = array('Tree');
		
		// function newItem($data) {
		// 	$this->set($data);
		// 	$success = false;
		// 
		// 	if ($success = $this->validates()) {
		// 		$tokens = String::tokenize($data[$this->name]['match_route']);
		// 		
		// 		foreach ($tokens as $token) {
		// 			// There are three different cases
		// 			// 1- :param => value
		// 			// 2- pass => array('value1', 'value2')
		// 			// 3- named => array('param1' => 'value1', 'param2' => 'value2')
		// 			switch (true) {
		// 				// Case 1 - :param => value
		// 				case count($param = explode('=>', $token)) > 1:
		// 					$param = MenuItemRoute::cleanParam($param);
		// 					MenuItemRoute::getInstance()->route[$param['name']] = $param['value'];
		// 					break;
		// 				// Case 2 - named => array('param1' => 'value1', 'param2' => 'value2', ...)
		// 				case count($param = explode(':', $token)) > 1:
		// 					$param = MenuItemRoute::cleanParam($param);
		// 					MenuItemRoute::getInstance()->route[] = $param['name'] . ':' . $param['value'];;
		// 					break;
		// 				// Case 3 - pass => array('value1', 'value2', ...)
		// 				default:
		// 					$param = MenuItemRoute::cleanParam($token);
		// 					MenuItemRoute::getInstance()->route[] = $param;
		// 					break;
		// 			}
		// 		}
		// 		
		// 		$this->data[$this->name]['match_route'] = serialize(MenuItemRoute::getInstance());
		// 		
		// 		$succes = $this->save(null, false);
		// 	}
		// 	
		// 	return $success;
		// }
		
		function newSection($data) {
			$this->set($data);
			$success = false;

			if ($success = $this->validates()) {
				$tokens = String::tokenize($data[$this->name]['match_route']);
				
				foreach ($tokens as $token) {
					// There are three different cases
					// 1- :param => value
					// 2- pass => array('value1', 'value2')
					// 3- named => array('param1' => 'value1', 'param2' => 'value2')
					switch (true) {
						// Case 1 - :param => value
						case count($param = explode('=>', $token)) > 1:
							$param = MenuItemRoute::cleanParam($param);
							MenuItemRoute::getInstance()->route[$param['name']] = $param['value'];
							break;
						// Case 2 - named => array('param1' => 'value1', 'param2' => 'value2', ...)
						case count($param = explode(':', $token)) > 1:
							$param = MenuItemRoute::cleanParam($param);
							MenuItemRoute::getInstance()->route[] = $param['name'] . ':' . $param['value'];;
							break;
						// Case 3 - pass => array('value1', 'value2', ...)
						default:
							$param = MenuItemRoute::cleanParam($token);
							MenuItemRoute::getInstance()->route[] = $param;
							break;
					}
				}
				
				$this->data[$this->name]['match_route'] = serialize(MenuItemRoute::getInstance());
				
				$succes = $this->save(null, false);
			}
			
			return $success;
		}
		
		function findSections() {
			$sections = $this->find('threaded', array('fields' => array('id', 'parent_id', 'name', 'match_route')));

			return $sections;
		}
		// Changed for getSections
		function getSections() {
			$items = $this->find('threaded', array('fields' => array('id', 'parent_id', 'name', 'match_route')));

			return $items;
		}
		// function populateMenu() {
		// 	$items = $this->find('threaded', array('fields' => array('id', 'parent_id', 'name', 'match_route')));
		// 
		// 	return $items;
		// }
	}
	
	
	/**
	* 
	*/
	class MenuItemRoute
	{
		static $_instance;
		
		public $route = array();
		
		private function __construct() {}
		private function __clone() {}
		
		public static function getInstance() {
			if (!(self::$_instance instanceof self)) {
				self::$_instance = new self();
			}
			
			return self::$_instance;
		}
		
		public static function cleanParam($params) {
			if (is_array($params)) {
				$param = trim($params[0], ':\t ');
				$value = trim($params[1]);
				
				$result = array('name' => $param, 'value' => $value);
			} else {
				$result = trim($params, '\t ');
			}
			
			return $result;
		}
	}
	