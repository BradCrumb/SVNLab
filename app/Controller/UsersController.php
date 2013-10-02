<?php
class UsersController extends AppController {

	public function login() {
		if ($this->request->is('post')) {
			if ($this->Auth->login()) {
				return $this->redirect($this->Auth->redirectUrl());
				// Prior to 2.3 use `return $this->redirect($this->Auth->redirect());`
			} else {
				$this->Session->setFlash(__('user_login_failure_message'), 'alert', array(
					'plugin' => 'BoostCake',
					'class' => 'alert-error'
				), 'error');
			}
		}
	}

	public function logout() {
		$this->redirect($this->Auth->logout());
	}

	public function signup() {
		if ($this->request->is('post')) {
			try{
				$this->User->begin();
				$this->User->create();
				if ($this->User->save($this->request->data)) {
					/*$this->Svn->changeDir('');
					$return = $this->Svn->mkdir($this->request->data['User']['username'], "Created repository root for '{$this->request->data['User']['username']}'");

					if ($return) {
						$this->Session->setFlash(__('user_save_success_message'), 'alert', array(
							'plugin' => 'BoostCake',
							'class' => 'alert-success'
						), 'success');

						$this->User->commit();
					} else {
						throw new CakeException(__('svn_create_repo_error_message'));
					}*/
					$this->Session->setFlash(__('user_save_success_message'), 'alert', array(
							'plugin' => 'BoostCake',
							'class' => 'alert-success'
						), 'success');

					$this->User->commit();
				} else {
					throw new CakeException(__('user_save_error_message'));
				}
			} catch(CakeException $e) {
				$this->Session->setFlash($e->getMessage(), 'alert', array(
					'plugin' => 'BoostCake',
					'class' => 'alert-error'
				), 'error');

				$this->User->rollback();
			}
		}
	}

	public function index() {
		$this->set('users', $this->User->find('all'));
	}
}