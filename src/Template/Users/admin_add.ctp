<div class="users form">
<?php echo $this->Form->create('User');?>
	<fieldset>
 		<legend><?php echo __('Add {0}', __('User'));?></legend>
	<?php
		echo $this->Form->input('username');
		echo $this->Form->input('email');
		echo $this->Form->input('active');
		echo $this->Form->input('logins');
		echo $this->Form->input('role_id');
		echo $this->Form->input('password');
	?>
	</fieldset>
<?php echo $this->Form->submit(__('Submit')); echo $this->Form->end();?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $this->Html->link(__('List {0}', __('Users')), ['action' => 'index']);?></li>
	</ul>
</div>