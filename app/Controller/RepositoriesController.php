<?php
App::import('Vendor', 'php-markdown', true, array(), 'php-markdown/Michelf/Markdown.php');
App::import('Vendor', 'php-markdown-extra', true, array(), 'php-markdown/Michelf/MarkdownExtra.php');

#https://github.com/hugomaiavieira/pygments-style-github
App::import('Vendor', 'pygments_for_php', true, array(), 'pygments_for_php/pygments_for_php/pygments_for_php.inc.php');

class RepositoriesController extends AppController {

	public $helpers = array('Time');

	public function index() {
		$conditions = array('active' => 1);

		if ($this->request->is('post') && isset($this->request->data['Repository']['search'])) {
			$conditions['OR'] = array(
				'name LIKE' => '%' . $this->request->data['Repository']['search'] . '%',
				'description LIKE' => '%' . $this->request->data['Repository']['search'] . '%',
			);
		}

		$repositories = $this->Repository->find('all', array(
			'conditions' => $conditions
		));

		$this->set('repositories', $repositories);
	}

	public function add() {
		if ($this->request->is('post')) {
			try {
				$this->Repository->create();

				$this->Repository->begin();

				$this->request->data['Repository']['user_id'] = $this->Auth->user('id');
				if ($this->Repository->save($this->request->data)) {
					/*$return = $this->Svn->mkdir($this->request->data['Repository']['name'], "Created repository '{$this->request->data['Repository']['name']}'");

					if ($return) {
						$this->Svn->initStructure($this->request->data['Repository']['name']);

						$this->Session->setFlash(__('svn_create_repo_success_message'), 'alert', array(
							'plugin' => 'BoostCake',
							'class' => 'alert-success'
						), 'success');

						$this->Repository->commit();

						$this->redirect(array('action' => 'index'));
					}*/
					$this->Repository->commit();

					$this->redirect(array('action' => 'index'));
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

	public function view($repoName, $username = null) {
		$info = $this->__checkAndGetRepoInformation($username, $repoName);
		$user = $info['user'];
		$repo = $info['repo'];

		if (Configure::read('SVNLab.user_mode')) {
			$this->Svn->changeDir($user['User']['username'] . '/' . $repo['Repository']['name']);
		} else {
			$this->Svn->changeDir($repo['Repository']['name']);
		}

		$files = $this->Svn->ls('trunk/');

		if (!empty($files)) {
			if ($readmeFile = $this->__hasReadme($files)) {
				$this->set('readme',\Michelf\MarkdownExtra::defaultTransform($this->Svn->cat('trunk/' . $readmeFile)));
			}

			$keys = array_keys($files);
			$length = count($keys);
			for ($i = 0;$i < $length;$i++) {
				$files[$keys[$i]]['latestLog'] = $this->Svn->log('trunk/' . $files[$keys[$i]]['name'], SVN_REVISION_HEAD, SVN_REVISION_INITIAL,1)[0];

				$path = '/' . $repo['Repository']['name'];

				if (Configure::read('SVNLab.user_mode')) {
					$path = '/' . $user['User']['username'] . $path;
				}

				//$files[$keys[$i]]['path'] = str_replace($path, '', $files[$keys[$i]]['latestLog']['paths'][0]['path']);
				$files[$keys[$i]]['path'] = '/trunk/' . $files[$keys[$i]]['name'];
			}

			$this->set('files', $files);

			$latestLog = $this->Svn->log('trunk/', SVN_REVISION_HEAD, SVN_REVISION_HEAD)[0];

			$time = strtotime($latestLog['date']);

			if ($time > $repo['Repository']['latest_update']) {
				$this->Repository->id = $repo['Repository']['id'];
				$this->Repository->saveField('latest_update', $time);
			}

			$this->set('latestLog', $latestLog);
		}

		$this->set('repoUrl', $this->Svn->fullRepoPath('trunk/'));

		$this->set('ownRepo', $this->Auth->user('id') == $repo['User']['id']);

		$this->set('amountOfCommits', $this->Svn->amountOfCommits('trunk/'));
		$this->set('amountOfBranches', $this->Svn->amountOfBranches());
		$this->set('amountOfTags', $this->Svn->amountOfTags());
	}

	private function __hasReadme($files) {
		foreach ($files as $file) {
			if (strtolower($file['name']) == 'readme.md') {
				return $file['name'];
			}
		}

		return false;
	}

	public function blob_user($username, $repoName, $blobPath) {
		$info = $this->__checkAndGetRepoInformation($username, $repoName);
		$user = $info['user'];
		$repo = $info['repo'];

		$this->set('blobPath', $blobPath);

		if (Configure::read('SVNLab.user_mode')) {
			$this->Svn->changeDir($user['User']['username'] . '/' . $repo['Repository']['name']);
		} else {
			$this->Svn->changeDir($repo['Repository']['name']);
		}

		$length = strlen($blobPath);
		if ($blobPath[$length - 1] == '/') {
			$blobPath = substr($blobPath, 0, $length - 1);
		}

		$file = $this->Svn->cat($blobPath);

		if ($file === false) {
			throw new NotFoundException();
		}

		$length = strlen($blobPath);
		if (substr(strtolower($blobPath),$length - 2) == 'mdd') {
			$this->set('fileContent', \Michelf\MarkdownExtra::defaultTransform($file, 'markdown'));
		} else {
			$this->set('fileContent', pygmentize($file, 'php'));
		}

		$blobExplode = explode('/',$blobPath);

		if (!end($blobExplode)) {
			array_pop($blobExplode);
		}

		array_pop($blobExplode);

		$this->set('parentTree', implode('/', $blobExplode));

		$this->render('blob');
	}

	public function blob($repoName, $blobPath) {
		$this->blob_user(null, $repoName, $blobPath);
	}

	public function tree_user($username, $repoName, $treePath) {
		$info = $this->__checkAndGetRepoInformation($username, $repoName);
		$user = $info['user'];
		$repo = $info['repo'];

		$this->set('treePath', $treePath);

		if (Configure::read('SVNLab.user_mode')) {
			$this->Svn->changeDir($user['User']['username'] . '/' . $repo['Repository']['name']);
		} else {
			$this->Svn->changeDir($repo['Repository']['name']);
		}

		$files = $this->Svn->ls($treePath);

		if ($files === false) {
			throw new NotFoundException();
		}

		if (!empty($files)) {
			if ($readmeFile = $this->__hasReadme($files)) {
				$this->set('readme',\Michelf\MarkdownExtra::defaultTransform($this->Svn->cat('trunk/' . $readmeFile)));
			}

			$keys = array_keys($files);
			$length = count($keys);
			for ($i = 0;$i < $length;$i++) {
				$files[$keys[$i]]['latestLog'] = $this->Svn->log($treePath . $files[$keys[$i]]['name'], SVN_REVISION_HEAD, SVN_REVISION_INITIAL,1)[0];

				$path = '/' . $repo['Repository']['name'];

				if (Configure::read('SVNLab.user_mode')) {
					$path = '/' . $user['User']['username'] . $path;
				}

				//$files[$keys[$i]]['path'] = str_replace($path, '', $files[$keys[$i]]['latestLog']['paths'][0]['path']);
				$files[$keys[$i]]['path'] = '/' . $treePath . $files[$keys[$i]]['name'];
			}

			$this->set('files', $files);

			$treeExplode = explode('/',$treePath);

			if (!end($treeExplode)) {
				array_pop($treeExplode);
			}

			array_pop($treeExplode);

			$this->set('parentTree', implode('/', $treeExplode));

			$this->set('latestLog', $this->Svn->log('trunk/', SVN_REVISION_HEAD, SVN_REVISION_HEAD)[0]);
		}

		$this->render('tree');
	}

	public function tree($repoName, $treePath) {
		$this->tree_user(null, $repoName, $treePath);
	}

	private function __checkAndGetRepoInformation($username, $repoName) {
		$user = $this->Repository->User->findByUsername($username, array('id', 'username'));

		if (empty($user) && Configure::read('SVNLab.user_mode')) {
			throw new NotFoundException();
		}

		$conditions = array('name' => $repoName);

		if (Configure::read('SVNLab.user_mode')) {
			$conditions['user_id'] = $user['User']['id'];
		}

		$repo = $this->Repository->find('first', array('conditions' => $conditions));

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