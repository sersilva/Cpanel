<?php
	/**
	* 
	*/
	class CpanelHelper extends Helper
	{
		var $helpers = array('Html', 'Form', 'Tree');
		
		function __contruct() {}
		
		function gotoLogin($msg = '') {
			$msg || $msg = __('Go to login page', true);
			
			return $this->Html->link($msg, ClassRegistry::getObject('Cpanel')->loginRoute);
		}
		
		function gotoHome($msg = '') {
			$msg || $msg = __('Go to home page', true);
			
			return $this->Html->link($msg, '/');
		}
		
		function appCredentials() {
			$output = '';
			$output .= $this->Html->image(Configure::read('credentials.logo'));
			$output .= '<h1>' . Configure::read('credentials.title') . '</h1>';

			return $output;
		}
		
		function dashboard($text = null) {
			if (null === $text) {
				$text = __('Dashboard', true);
			}
			return $this->Html->link($text, ClassRegistry::getObject('Cpanel')->dashboardRoute, array('id' => 'dashboard'));
		}
		
		function account() {
			return $this->Html->link(__('My Account', true), '', array('id' => 'my-account'));
		}
		
		function logout() {
			return $this->Html->link(__('Logout', true), array('controller' => 'users', 'action' => 'logout'), array('id' => 'logout'));
		}
		
		function levelUp()
		{
			
		}
		
		function sectionTabs()
		{
			
		}
		
		function subsectionTabs()
		{
			
		}
		
		function sectionTitle($title = null) {
			if (null !== $title) {
				$this->sectionTitle = $title;
				return;
			}
			
			return @$this->sectionTitle;
		}
		
		function newSectionLink($message = '', $options = array()) {
			return $this->Html->link($message, ClassRegistry::init('Cpanel')->newMenuSectionRoute, $options);
		}
		
		function sections() {
			$output = '';
			
			$output .= $this->_adminMenuTools();
			
			// Fetch sections from database
			// @todo Caching
			$sections = ClassRegistry::init('CpanelMenu')->getSections();
			
			// $output .= $this->_organize($items, 2); - I found the tree helper does this probably more efficient
			$output .= $this->Tree->generate($sections, array('element' => 'menu'));
			
			return $output;
		}
		
		function _adminMenuTools() {
			$output = '';
			if (Configure::read('debug')) {
				$output .= '<li>';
				$output .= $this->Html->link(__('Menu', true), ClassRegistry::getObject('Cpanel')->listMenuSectionsRoute);
				// $output .= '<ul>';
				// $output .= '<li>' . $this->Html->link(__('List Sections', true), ClassRegistry::getObject('Cpanel')->listMenuSectionsRoute) . '</li>';
				// $output .= '<li>' . $this->Html->link(__('Add Section', true), ClassRegistry::getObject('Cpanel')->newMenuSectionRoute) . '</li>';
				// $output .= '<li>' . $this->Html->link(__('Edit Sections', true), ClassRegistry::getObject('Cpanel')->editMenuSectionsRoute) . '</li>';
				// $output .= '</ul>';
				$output .= '</li>';
			}
			
			return $output;
		}
		
		// function _organize(&$items, $header) {
		// 	$output = '';
		// 	foreach ($items as &$item) {				
		// 		$route = unserialize($item['CpanelMenuItem']['match_route'])->route;
		// 		// Enforce items not to use cpanel plugin routes
		// 		$route['plugin'] = '';
		// 		if (!empty($item['children'])) {
		// 			$output .= "<h$header>" . $this->Html->link($item['CpanelMenuItem']['name'], $route) . "</h$header>\n" . $this->_organize($item['children'], $header + 1);
		// 		} else {
		// 			$output .= "<h$header>" . $this->Html->link($item['CpanelMenuItem']['name'], $route) . "</h$header>\n";
		// 		}
		// 	}
		// 	
		// 	return $output;
		// }
	}
	