<h1><?php echo __('Repositories');?></h1>

<?php
if (empty($repositories)) {
	?>
	<p><?php echo __('There are no repositories yet');?></p>
	<?php
} else {
	?>
	<div class="search-form clearfix">
		<?php
		echo $this->Form->create();
			echo $this->Form->input('search', array('label' => false, 'wrapInput' => false, 'placeholder' => __('Find a repository...'), 'after' => $this->Form->submit(__('Search'), array('label' => __('Search'), 'class' => 'btn', 'div' => false))));
		echo $this->Form->end();
		?>
	</div>

	<ul class="repolist">
		<?php
		foreach ($repositories as $repo) {?>
			<li>
				<h3 class="repolist-name"><?php echo $this->Html->link($repo['Repository']['name'],array('action' => 'view', 'repo_name' => $repo['Repository']['name']));?></h3>
				<div class="body">
					<p class="description"><?php echo $repo['Repository']['description'];?></p>
					<?php
					if ($repo['Repository']['latest_update']) {?>
						<p class="updated-at"><?php echo __('Last updated %s', $this->Time->timeAgoInWords($repo['Repository']['latest_update'], array(
							'accuracy' => array(
								'hour' => 'hour',
								'day' => 'day'
						))));?></p>
						<?php
					}
					?>
				</div>
			</li>
			<?php
		}?>
	</ul>
	<?php
}