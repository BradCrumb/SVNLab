<?php
echo $this->Form->create('User', array('class' => 'well'));
?>
<fieldset>
	<legend>Signup</legend>
	<?php
	echo $this->Form->input('username', array('label' => __('Username')));
	echo $this->Form->input('email', array('label' => __('E-mail')));
	echo $this->Form->input('password', array('label' => __('Password')));
	echo $this->Form->input('password_confirm', array('label' => __('Confirm Password'), 'type' => 'password'));

	echo $this->Form->submit(__('Create an account'), array(
		'div' => false,
		'class' => 'btn'
	));
?>
</fieldset>
<?php
echo $this->Form->end();