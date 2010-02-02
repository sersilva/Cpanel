<?php
	/**
	* 
	*/
	class UsersController extends CpanelAppController
	{	
		var $uses = array('Cpanel.User');
		
		function setup() {
			if (!ClassRegistry::getObject('Cpanel')->setupMode) {
				$this->redirect(ClassRegistry::getObject('Cpanel')->loginRoute);
			}
			
			if (!empty($this->data)) {
				if ($this->User->setup($this->data)) {
					$this->Session->setFlash(__('The Root account has been created, now you can login.', true));
					$this->redirect(ClassRegistry::getObject('Cpanel')->loginRoute);
				}
					$this->Session->setFlash(__('The Root account can\'t be created. Check if you fill the form correctly.', true));
					$this->data['User']['password'] = $this->data['User']['repassword'] = '';
			}
		}
		
		function login() {
			if ($this->Session->read('Auth.User.id')) {
				$this->redirect(ClassRegistry::getObject('Cpanel')->dashboardRoute);
			}
		}
		
		function logout() {
			$this->redirect($this->Auth->logout());
		}
	}