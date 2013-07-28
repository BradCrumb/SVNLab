<?php

echo $this->Form->create('User');

echo $this->Form->input('username', array('label' => __('Username')));
echo $this->Form->input('password', array('label' => __('Password')));

echo $this->Form->submit(__('Login'), array('class' => __('btn btn-primary')));

echo $this->Form->end();