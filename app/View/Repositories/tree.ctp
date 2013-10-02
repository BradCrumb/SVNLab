<?php
echo $this->Html->link(__('<< Back to repository overview'), array('controller' => 'repositories', 'action' => 'view', 'repo_name' => $repo['Repository']['name']));?>

<h1><?php echo $repo['User']['username'];?> / <?php echo $repo['Repository']['name']; echo str_replace('/', ' / ', str_replace('trunk', '', $treePath));?></h1>

<?php
if (!empty($repo['Repository']['description'])) {
	?>
	<p><?php echo $repo['Repository']['description'];?></p>
	<?php
}

if (!empty($files)) {
	echo $this->element('tree', array('files' => $files, 'latestLog' => $latestLog, 'parentTree' => $parentTree));
}