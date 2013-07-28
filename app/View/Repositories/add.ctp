<?php
echo $this->Form->create('Repository');

echo $this->Form->input('name', array('label' => __('Repository name')));
?>
<p><?php echo __('Great repository names are short and memorable. Need inspiration? How about turbo-octo-robot.');?></p>

<?php
echo $this->Form->input('description', array('label' => __('Description')));

echo $this->Form->input('initialize_readme', array('label' => __('Initialize this repository with a README'), 'type' => 'checkbox'));

echo $this->Form->submit(__('Create repository'), array('class' => 'btn btn-primary'));

echo $this->Form->end();