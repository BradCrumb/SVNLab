<h1><?php echo $repo['User']['username'];?> / <?php echo $repo['Repository']['name'];?></h1>

<?php
if (!empty($repo['Repository']['description'])) {
	?>
	<p><?php echo $repo['Repository']['description'];?></p>
	<?php
}

?>
<div class="block">
	<div class="commit">
		<p class="commit-title"><?php echo $latestLog['msg'];?></p>
		<div class="commit-meta">
			<div class="authorship">
				<?php
				echo __('%s authored %s','<span class="author-name">' . $this->Html->link($latestLog['author'], '#') . '</span>', $this->Time->timeAgoInWords($latestLog['date'], array(
					'accuracy' => array(
						'hour' => 'hour'
				))));?>
			</div>
		</div>
	</div>
	<table class="files">
		<?php
		foreach ($files as $file) {
			?>
			<tr>
				<td class="icon"></td>
				<td class="content">
					<?php
					$url = array(
						'controller' => 'repositories',
						'action' => 'blob',
						'username' => $repo['User']['username'],
						'repo_name' => $repo['Repository']['name'],
					);

					echo $this->Html->link($file['name'], Router::url($url) . $file['path']);?>
				</td>
				<td class="message"><?php echo $file['latestLog']['msg'];?></td>
				<td class="age">
					<?php
					echo $this->Time->timeAgoInWords($file['latestLog']['date'], array(
						'accuracy' => array(
							'hour' => 'hour'
					)));?>
				</td>
			</tr>
			<?php
		}
		?>
	</table>
</div>

<?php
if ($readme) {
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