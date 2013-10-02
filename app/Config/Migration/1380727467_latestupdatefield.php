<?php
class LatestUpdateField extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 * @access public
 */
	public $description = '';

/**
 * Actions to be performed
 *
 * @var array $migration
 * @access public
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'repositories' => array(
					'latest_update' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'after' => 'active'),
				),
			),
			'alter_field' => array(
				'repositories' => array(
					'user_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'key' => 'index'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'repositories' => array('latest_update',),
			),
			'alter_field' => array(
				'repositories' => array(
					'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
				),
			),
		),
	);

/**
 * Before migration callback
 *
 * @param string $direction, up or down direction of migration process
 * @return boolean Should process continue
 * @access public
 */
	public function before($direction) {
		return true;
	}

/**
 * After migration callback
 *
 * @param string $direction, up or down direction of migration process
 * @return boolean Should process continue
 * @access public
 */
	public function after($direction) {
		return true;
	}
}
