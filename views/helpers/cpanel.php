<?php
	/**
	* 
	*/
	class CpanelHelper extends Helper {
		var $helpers = array('Html', 'Form', 'Javascript', 'Tree');
		
		function __construct() {
			$this->Cpanel =& Cpanel::getInstance();
			$this->Menu   =& ClassRegistry::init('Cpanel.CpanelMenu');
		}
		
		// General
		/**
		 * 
		 */
		function appCredentials() {
			$output = '';
			$output .= $this->Html->image(Configure::read('credentials.logo'));
			$output .= '<h1>' . Configure::read('credentials.title') . '</h1>';

			return $output;
		}
		
		/**
		 * 
		 */
		function dashboard($text = null) {
			if (null === $text) {
				$text = __('Dashboard', true);
			}
			
			return $this->Html->link($text, $this->Cpanel->dashboardRoute, array('id' => 'dashboard', 'class' => 'top-options'));
		}
		
		/**
		 * 
		 */
		function account($text = null) {
			if (null === $text) {
				$text = __('My Account', true);
			}
			
			return $this->Html->link($text, /*$this->Cpanel->accountRoute*/'Account', array('id' => 'my-account', 'class' => 'top-options'));
		}
		
		/**
		 * 
		 */
		function logout($text = null) {
			if (null === $text) {
				$text = __('Logout', true);
			}
			
			return $this->Html->link($text, $this->Cpanel->logoutRoute, array('id' => 'logout'));
		}
		
		// Navigation
		/**
		 * 
		 */
		function navigationList() {
			$output = '';
			
			$output .= $this->_adminMenuTools();
			
			$autoPath = array();
			$sectionId = null;
			
			$section = $this->Menu->findSectionPathFromUrl($this->_getSection());
			
			if (!empty($section)) {
				$autoPath = array($section['CpanelMenu']['lft'], $section['CpanelMenu']['rght']);
				$sectionId = $section['CpanelMenu']['id'];
			}
			
			// sectionPath is the path (tree) generated when a
			// nested node selected
			$sectionPath = $this->Menu->findSectionPath($sectionId);
			
			if (!empty($sectionPath)) {
				$output .= $this->Tree->generate($sectionPath, array('model' => 'CpanelMenu', 'element' => 'menu/cpanel_menu', 'autoPath' => $autoPath));
			}
			
			return $output;
		}
		
		// Content Navigation
		/**
		 * 
		 */
		function crumbs($separator = ' > ') {
			$this->Html->addCrumb(__('Control Panel', true), $this->Cpanel->dashboardRoute);
			
			if (isset($this->params['section'])) {
				$section = $this->Menu->findSectionPathFromUrl($this->params['section']);
				
				$branch = $this->Tree->generate(
							$section['path'],
							array(
								'model' => 'CpanelMenu',
								'element' => 'menu/cpanel_menu_crumbs',
								'autoPath' => array($section['CpanelMenu']['lft'], $section['CpanelMenu']['rght'])
							)
						);
			} else {
				// Crumbs only for plugin's controllers
				if ($this->params['controller'] != 'control_panel') {
					$controller = Inflector::humanize($this->params['controller']);
					
					if ($this->params['action'] == 'index') {
						$this->Html->addCrumb($controller);
					} else {
						$action = Inflector::humanize($this->params['action']);
						$this->Html->addCrumb($controller, array('action' => 'index'));
						$this->Html->addCrumb(Inflector::humanize($this->params['action']));
					}
				} else {
					$this->Html->addCrumb(Inflector::humanize($this->params['action']));
				}
			}
			
			return $this->Html->getCrumbs($separator);
		}
		
		/**
		 * @todo Implement method
		 */
		function levelUp() {
			
		}
		
		/**
		 * 
		 */
		function sectionTabs() {
			if ($this->isCpanel()) {
				return;
			}
			
			$output = '';
			
			$section = $this->Menu->findSectionFromUrl($this->params['section']);
			$path = $this->Menu->getpath($section['CpanelMenu']['id'], 'id');
			$firstLevel = $this->Menu->findAllByParentId($path[0]['CpanelMenu']['id']);
			
			$output .= $this->Tree->generate($firstLevel, array('model' => 'CpanelMenu', 'element' => 'menu/cpanel_menu', 'autoPath' => array($section['CpanelMenu']['lft'], $section['CpanelMenu']['rght'])));
			
			return $output;
		}
		
		
		function _findControllerActions($section) {
			$route = MenuItemRoute::unserializeRoute($section['CpanelMenu']['match_route'], 'asArray');
			
			$controller = isset($route['controller']) ? $route['controller'] : $this->params['controller'];
			
			$actions = array();
			
			// Find all Admin public actions for this controller
			App::import('Controller', $route['controller']);
			
			try {
				$className = Inflector::camelize($route['controller'] . '_controller');
				$ClassInstance = new ReflectionClass($className);
				$methodCollection = $ClassInstance->getMethods();
				
				$routingAdmin = Configure::read('Routing.admin');
				$section = $this->params['section'];
				
				foreach ($methodCollection as $MethodObject) {
					// Filter only prefixed methods
					if (!preg_match("/^$routingAdmin\_.*/", $MethodObject->name)) {
						continue;
					}
					
					$action = trim(r("$routingAdmin", '', $MethodObject->name), '_ ');
					$actionName = $action == 'index' ? __('List', true) : Inflector::humanize($action);
					$actions[] = array(
						'name' => $actionName,
						'route' => array('controller' => $controller, 'action' => $action, 'section' => $section)
					);
					
				}
			} catch (Exception $e) {
				$this->log('ExceptionRaised in CpanelHelper.php:124 with message: ' . $e->getMessage());
				return;
			}
			
			return $actions;
		}
		
		function sectionSubTabs() {
			if ($this->isCpanel()) {
				return;
			}
			
			$output = '';
			
			$section = $this->Menu->findSectionFromUrl($this->params['section']);
			$path = $this->Menu->getpath($section['CpanelMenu']['id'], 'id');
			$firstLevel = $this->Menu->findAllByParentId($path[1]['CpanelMenu']['id']);
			
			$output .= $this->Tree->generate($firstLevel, array('model' => 'CpanelMenu', 'element' => 'menu/cpanel_menu', 'autoPath' => array($section['CpanelMenu']['lft'], $section['CpanelMenu']['rght'])));
			
			return $output;
		}
		
		function sectionActions() {
			if ($this->isCpanel()) {
				return;
			}
			
			$output = '';
			
			$actions = $this->_findControllerActions($this->Menu->findSectionFromUrl($this->params['section']));
			$links = array();
			
			foreach ($actions as $action) {
				$link = $this->Html->link($action['name'], $action['route']);
				$links[] = sprintf($this->Html->tags['li'], '', $link);
			}
			
			$output .= sprintf($this->Html->tags['ul'], '', implode("\n", $links));
			
			return $output;
		}
		
		function sectionTitle($title = null) {
			return Inflector::humanize($this->params['controller']);
		}
		
		function newSectionLink($message = '', $options = array()) {
			return $this->Html->link($message, $this->Cpanel->newMenuSectionRoute, $options);
		}
		
		function setSection(&$route, $value) {
			// Enforces not route through the plugin
			$route['plugin'] = null;
			$route['section'] = $value;
		}
		
		
		// Private
		/**
		 * 
		 */
		function _adminMenuTools() {
			$output = '';
			if (Configure::read('debug')) {
				$output .= '<li>';
				$output .= $this->Html->link(__('Menu', true), $this->Cpanel->listMenuSectionsRoute);
				$output .= '</li>';
			}
			
			return $output;
		}
		
		/**
		 * 
		 */
		function _getSection() {
			$section = isset($this->params['section']) ? $this->params['section'] : null;
			
			return $section;
		}
		/**
		 * 
		 */
		function _buildMenu($nodes, $options = array()) {
			$nodes = (array) $nodes;
			
			$links = array();
			
			foreach ($nodes as $node) {
				$nodeName = $node['CpanelMenu']['name'];
				$nodeRoute = MenuItemRoute::unserializeRoute($node['CpanelMenu']['match_route'], true);
				$this->setSection($nodeRoute, $node['CpanelMenu']['id']);
				
				$links[] = sprintf($this->Html->tags['li'], '', $this->Html->link($nodeName, $nodeRoute));
			}
			
			return sprintf($this->Html->tags['ul'], $this->_parseAttributes($options), implode("\n", $links));
		}
		
		function isCpanel() {
			return $this->params['plugin'] == $this->Cpanel->pluginName ? true : false;
		}
	}
	