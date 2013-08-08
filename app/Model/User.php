<?php
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');

class User extends AppModel {
	public $validate = array(
		'username' => array(
			'alphaNumericWithDash' => array(
				'rule' => array('custom', '/[a-zA-z0-9-_]/'),
				'message' => 'not_alpha_numeric_field_error'
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'unique_username_field_error'
			)
		),
		'email' => array(
			'email' => array(
				'rule' => 'email',
				'message' => 'email_field_error'
			)
		),
		'password' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'password_field_error'
			),
		),
		'password_confirm' => array(
			'validPasswordConfirm' => array(
				'rule' => 'validPasswordConfirm',
				'message' => 'passwords_dont_match_field_error'
			),
		)
	);

	public $hasMany = array('Repository');

	public function validPasswordConfirm($value) {
		if ($this->data[$this->alias]['password'] != $this->data[$this->alias]['password_confirm']) {
			return false;
		}
		return true;
	}

	public function beforeSave($options = array()) {
		if (!$this->id) {
			$passwordHasher = new SimplePasswordHasher();
			$this->data[$this->alias]['password'] = $passwordHasher->hash($this->data[$this->alias]['password']);
		}
		return true;
	}
}