<?php
App::import('Vendor', 'php-markdown', true, array(), 'php-markdown/Michelf/Markdown.php');
App::import('Vendor', 'php-markdown-extra', true, array(), 'php-markdown/Michelf/MarkdownExtra.php');

App::import('Vendor', 'pygments_for_php', true, array(), 'pygments_for_php/pygments_for_php/pygments_for_php.inc.php');

class RepositoriesController extends AppController {

	public $helpers = array('Time');

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
		$info = $this->__checkAndGetRepoInformation($username, $repoName);
		$user = $info['user'];
		$repo = $info['repo'];

		$this->Svn->changeDir($user['User']['username'] . '/' . $repo['Repository']['name']);

		$this->set('readme',\Michelf\MarkdownExtra::defaultTransform($this->Svn->cat('trunk/README.MD')));

		$files = $this->Svn->ls('trunk/');

		$keys = array_keys($files);
		$length = count($keys);
		for ($i = 0;$i < $length;$i++) {
			$files[$keys[$i]]['latestLog'] = $this->Svn->log('trunk/' . $files[$keys[$i]]['name'], SVN_REVISION_HEAD, SVN_REVISION_HEAD)[0];

			$files[$keys[$i]]['path'] = str_replace('/' . $user['User']['username'] . '/' . $repo['Repository']['name'], '', $files[$keys[$i]]['latestLog']['paths'][0]['path']);
		}

		$this->set('files', $files);

		$this->set('latestLog', $this->Svn->log('trunk/', SVN_REVISION_HEAD, SVN_REVISION_HEAD)[0]);
	}

	public function blob($username, $repoName, $blobPath) {
		$info = $this->__checkAndGetRepoInformation($username, $repoName);
		$user = $info['user'];
		$repo = $info['repo'];

		$this->Svn->changeDir($user['User']['username'] . '/' . $repo['Repository']['name']);

		$file = $this->Svn->cat($blobPath);

		if ($file === false) {
			throw new NotFoundException();
		}

		$length = strlen($blobPath);
		if (substr(strtolower($blobPath),$length - 2) == 'mdd') {
			$this->set('fileContent', \Michelf\MarkdownExtra::defaultTransform($file, 'markdown'));
		} else {
			$this->set('fileContent', pygmentize($file, 'php', 'monokai'));
		}
	}

	private function __checkAndGetRepoInformation($username, $repoName) {
		$user = $this->Repository->User->findByUsername($username, array('id', 'username'));

		if (empty($user)) {
			throw new NotFoundException();
		}

		$repo = $this->Repository->find('first', array('conditions' => array('user_id' => $user['User']['id'], 'name' => $repoName)));

		if (empty($repo)) {
			throw new NotFoundException();
		}

		$this->set('repo',$repo);

		return array('repo' => $repo, 'user' => $user);
	}

	public function beforeFilter() {
		parent::beforeFilter();

		$this->Auth->deny('add');
	}
}