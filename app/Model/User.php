<?php
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');

class User extends AppModel {
	public $validate = array(
		'username' => array(
			'alphaNumeric' => array(
				'rule' => 'alphaNumeric',
				'message' => 'required_field_error'
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
		)
	);

	public $hasMany = array('Repository');

	public function beforeSave($options = array()) {
        if (!$this->id) {
            $passwordHasher = new SimplePasswordHasher();
            $this->data[$this->alias]['password'] = $passwordHasher->hash($this->data[$this->alias]['password']);
        }
        return true;
    }
}