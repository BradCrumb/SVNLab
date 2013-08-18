<?php
if (!empty($files)) {
	?>
	<div class="block">
		<div class="commit">
			<p class="commit-title"><?php echo $latestLog['msg'];?></p>
			<div class="commit-meta">
				<div class="authorship">
					<?php
					echo __('%s authored %s','<span class="author-name">' . $this->Html->link($latestLog['author'], '#') . '</span>', $this->Time->timeAgoInWords($latestLog['date'], array(
						'accuracy' => array(
							'hour' => 'hour',
							'day' => 'day'
					))));?>
				</div>
			</div>
		</div>
		<table class="files">
			<?php
			if (isset($parentTree) && $parentTree) {
				?>
				<tr>
					<td class="icon"></td>
					<td class="content" colspan="3">
						<?php
						$url = array(
							'controller' => 'repositories',
							'action' => 'tree',
							'username' => $repo['User']['username'],
							'repo_name' => $repo['Repository']['name'],
						);

						echo $this->Html->link('..', Router::url($url) . '/' . $parentTree . '/');?>
					</td>
				</tr>
				<?php
			}

			foreach ($files as $file) {
				$icon = $file['type'] == 'dir' ? 'octicon-file-directory' : 'octicon-file-text';
				?>
				<tr>
					<td class="icon"><span class="octicon <?php echo $icon;?>"></span></td>
					<td class="content">
						<?php
						$url = array(
							'controller' => 'repositories',
							'action' => 'blob',
							'username' => $repo['User']['username'],
							'repo_name' => $repo['Repository']['name'],
						);

						if ($file['type'] == 'dir') {
							$url['action'] = 'tree';
						}

						echo $this->Html->link($file['name'], Router::url($url) . $file['path'] . '/');?>
					</td>
					<td class="message"><?php echo $file['latestLog']['msg'];?></td>
					<td class="age">
						<?php
						echo $this->Time->timeAgoInWords($file['latestLog']['date'], array(
							'accuracy' => array(
								'hour' => 'hour',
								'day' => 'day'
						)));?>
					</td>
				</tr>
				<?php
			}
			?>
		</table>
	</div>
	<?php
}