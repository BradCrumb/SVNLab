<h1><?php echo $repo['User']['username'];?> / <?php echo $repo['Repository']['name'];?></h1>

<?php
if (!empty($repo['Repository']['description'])) {
	?>
	<p><?php echo $repo['Repository']['description'];?></p>
	<?php
}

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