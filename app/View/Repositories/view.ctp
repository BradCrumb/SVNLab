<?php
echo $this->Html->link(__('<< Back to repositories overview'), array('controller' => 'repositories', 'action' => 'index'));?>

<h1><?php echo $repo['User']['username'];?> / <?php echo $repo['Repository']['name'];?></h1>

<?php
if (!empty($repo['Repository']['description'])) {
	?>
	<p><?php echo $repo['Repository']['description'];?></p>
	<?php
}
/*
?>
<div class="overall-summary">
	<ul class="numbers-summary">
		<li class="commits">
			<?php echo $this->Html->link('<span class="num"><span class="octicon octicon-history"></span> ' . $amountOfCommits . '</span> ' . __('Commits'), '#', array('escape' => false));?>
		</li>
		<li>
			<?php echo $this->Html->link('<span class="num"><span class="octicon octicon-git-branch"></span> ' . $amountOfBranches . '</span> ' . __('Branches'), '#', array('escape' => false));?>
		</li>
		<li>
			<?php echo $this->Html->link('<span class="num"><span class="octicon octicon-tag"></span> ' . $amountOfTags . '</span> ' . __('Releases'), '#', array('escape' => false));?>
		</li>
	</ul>
</div>
<?php
*/
if (!empty($files)) {
	echo $this->element('tree', array('files' => $files, 'latestLog' => $latestLog));
}
elseif($ownRepo) {
	?>
	<div class="alert alert-warning">
		<h4><?php echo __('Checkout your repository and do your first commit');?></h4>

		<?php
		echo $this->Form->input('repo', array(
			'label' => false,
			'afterInput' => '<span class="add-on clip-copy" data-clipboard-target="repo"><i class="icon icon-tags"></i></span>',
			'value' => $repoUrl,
			'data-clipboard-text' => $repoUrl,
			'class' => 'js-url-field',
			'title' => __('Copy to clipboard'),
			'div' => 'control-group input-append',
			'wrap-input' => 'controls'));?>
	</div>
	<?php
}
else {
	?>
	<div class="alert alert-info alert-center">
		<strong><?php echo __('There is nothing to see here yet.');?></strong><br/>
		<?php echo __('Move along now.');?>
	</div>
	<?php
}
if (isset($readme) && $readme) {
	?>
	<div class="block">
		<div class="block-header">
			Readme.MD
		</div>
		<div class="block-content markdown-body">
			<?php echo $readme;?>
		</div>
	</div>
	<?php
}