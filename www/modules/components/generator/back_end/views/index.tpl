<h1>Генератор модулей</h1>
<?php
	if(isset($error)) { ?>
	<div class="error"><?=$error;?></div>
	<?php }
?>
<?php
	if(isset($message)) { ?>
	<div class="success"><?=$message;?></div>
	<?php }
?>
<form action="<?=SITE_URL;?>/admin/generator" method="post">
	Название: <br>
	<input name="name"> <br>
	Тип: <br>
	<input type="radio" name="type" value="components" checked> Component<br>
	<input type="radio" name="type" value="blocks"> Block<br>
	<input type="radio" name="type" value="plugins"> Plugin<br>
	<input type="radio" name="type" value="addons"> Addon<br>
	<input type="radio" name="type" value="helpers"> Helper<br>

	
	<input type="submit" name="submit" value="Сгенерировать"> <br>
</form>