<?php echo __('Users');?>

<table class="table-striped">
	<thead>
		<tr>
			<th class="span4"><?php echo __('Username');?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($users as $user) {?>
			<tr>
				<td><?php echo $user['User']['username'];?></td>
			</tr>
			<?php
		}?>
	</tbody>
</table>