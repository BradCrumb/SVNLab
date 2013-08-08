<?php
class Repository extends AppModel {

	public $validate = array(
		'name' => array(
			'alphaNumericWithDash' => array(
				'rule' => array('custom', '/[a-zA-z0-9-_]/'),
				'message' => 'not_alpha_numeric_field_error'
			),
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'required_field_error'
			)
		)
	);

	public $belongsTo = array('User');
}