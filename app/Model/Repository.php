<?php
class Repository extends AppModel {

	public $validate = array(
		'name' => array(
			'alphaNumeric' => array(
				'rule' => 'alphaNumeric',
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