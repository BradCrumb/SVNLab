<?php
App::import('Vendor', 'php-markdown', true, array(), 'php-markdown/Michelf/Markdown.php');
App::import('Vendor', 'php-markdown-extra', true, array(), 'php-markdown/Michelf/MarkdownExtra.php');

class RepositoriesController extends AppController {

	public function index() {
		$this->set('repositories', $this->Repository->find('all', array(
			'conditions' => array(
				'active' => 1
			)
		)));
	}

	public function add() {
		if ($this->request->is('post')) {
			try {
				$this->Repository->create();

				$this->Repository->begin();

				$this->request->data['Repository']['user_id'] = $this->Auth->user('id');
				if ($this->Repository->save($this->request->data)) {
					$return = $this->Svn->mkdir($this->request->data['Repository']['name'], "Created repository '{$this->request->data['Repository']['name']}'");

					if ($return) {
						$this->Svn->initStructure($this->request->data['Repository']['name']);

						$this->Session->setFlash(__('svn_create_repo_success_message'), 'alert', array(
							'plugin' => 'BoostCake',
							'class' => 'alert-success'
						), 'success');

						$this->Repository->commit();

						$this->redirect(array('action' => 'index'));
					}
				} else {
					throw new CakeException(__('svn_create_repo_error_message'));
				}
			} catch(CakeException $e) {
				$this->Repository->rollback();

				if (!empty($this->request->data['Repository']['name'])) {
					$this->Svn->remove($this->request->data['Repository']['name'], "Rollback created repository '{$this->request->data['Repository']['name']}'");
				}

				$this->Session->setFlash($e->getMessage(), 'alert', array(
					'plugin' => 'BoostCake',
					'class' => 'alert-error'
				));
			}
		}
	}

	public function view($username, $repoName) {
		$user = $this->Repository->User->findByUsername($username, array('id', 'username'));

		if (empty($user)) {
			throw new NotFoundException();
		}

		$repo = $this->Repository->find('first', array('conditions' => array('user_id' => $user['User']['id'], 'name' => $repoName)));

		if (empty($repo)) {
			throw new NotFoundException();
		}

		$this->Svn->changeDir($user['User']['username'] . '/' . $repo['Repository']['name']);

		$this->set('readme',\Michelf\MarkdownExtra::defaultTransform($this->Svn->cat('trunk/README.MD')));

		$this->set('repo',$repo);
	}

	public function beforeFilter() {
		parent::beforeFilter();

		$this->Auth->deny('add');
	}
}