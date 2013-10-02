<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

$cakeDescription = __d('cake_dev', 'CakePHP: the rapid development php framework');
?>
<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $cakeDescription ?>:
		<?php echo $title_for_layout; ?>
	</title>
	<?php
		echo $this->Html->meta('icon');

		echo $this->Html->css('screen');

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
</head>
<body>
	<div class="navbar navbar-static-top">
		<div class="navbar-inner">
			<div class="container">
				<?php
				echo $this->Html->link(Configure::read('SVNLab.title'), '/', array('class' => 'title'));

				?>
				<ul class="top-nav">
					<li><?php echo $this->Html->link(__('Repositories'), array('controller' => 'repositories', 'action' => 'index'), array('class' => 'name'));?></li>
				</ul>

				<ul class="user-links">
					<?php
					if ($this->Session->read('Auth.User.id')) { ?>
						<li><?php echo $this->Html->link($this->Session->read('Auth.User.username'), '#', array('class' => 'name'));?></li>
						<li><?php echo $this->Html->link('<span class="octicon octicon-repo-create"></span>', array('controller' => 'repositories', 'action' => 'add'), array('escape' => false));?></li>
						<li><?php echo $this->Html->link('<span class="octicon octicon-log-out"></span>', array('controller' => 'users', 'action' => 'logout'), array('escape' => false));?></li>
						<?php
					} else {
						?>
						<li><?php echo $this->Html->link(__('Login'), array('controller' => 'users', 'action' => 'login'), array('class' => 'name'));?></li>
						<?php
					}?>
				</ul>
			</div>
		</div>
	</div>
	<div class="container">
		<div id="content">

			<?php echo $this->Session->flash(); ?>

			<?php echo $this->fetch('content'); ?>
		</div>
		<div id="footer">
			<?php echo $this->Html->link(
					$this->Html->image('cake.power.gif', array('alt' => $cakeDescription, 'border' => '0')),
					'http://www.cakephp.org/',
					array('target' => '_blank', 'escape' => false)
				);
			?>
		</div>
	</div>
	<?php echo $this->element('sql_dump'); ?>

	<?php echo $this->Html->script(array('vendor/ZeroClipboard/ZeroClipboard.min','vendor/jquery', 'main'));?>
</body>
</html>
