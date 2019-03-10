<div id="content">
	<h1 id="page_name"><?php echo $page_name; ?></h1>
	<?=$breadcrumbs;?>
	<?php 
	if(isset($errors)) { ?>
	<div class="error">
		<?php foreach($errors as $error) { 
		 echo $error . '<br>';
		 } ?>
	</div>
	<?php 
	}
	if(isset($message)) { ?>
	<div class="success">
		<?php echo $message; ?>
	</div>
	<?php 
	} ?>
  
	<form method="post" action="" enctype="multipart/form-data" >
		<label for="image_text">Описание:</label>
		<input type="text" name="text" value="<?=!empty($_POST['text'])?$_POST['text']:(!empty($category['text'])?$category['text']:'' );?>">
		<label for="page_name">Выберите файл(ы) загрузки (макс 20шт.) или архив ZIP c изображениями внутри (максимальный размер файла: <b><?=$max_file_size?>МБ</b>)</label>
		<input type="file"  name="images[]" multiple="multiple">
		<input type="checkbox" name="stamp" /><label for="image_stamp">Применить водяной знак</label><br>
		<input type="submit" name="submit" value="Закачать/Сохранить">
	</form>

  <?php if($items) { ?>
  	<style>
  		#sortable { overflow: hidden; list-style-type: none; margin: 0; padding: 0; width: 700px; }
  		#sortable li { width:120px; height:130px; text-align: center; margin: 5px; padding: 5px; float: left;background: #FFFFFF; border-radius: 4px; box-shadow: 0 0 2px #999999;}
	</style>
	  <p>Перетаскивайте картинку для изменения порядка</p>
	  <ul id="sortable" >
	  <?php foreach($items as $item) { ?>
	    <li id="id_<?=$item['id'];?>" class="ui-state-default">
	      <img width="100" src="<?php echo $category['path'] . '/mini/' . $item['image']; ?>"><br>
	      <a href="<?php echo SITE_URL . '/admin/gallery/delete_img/' . $item['id']; ?>">Удалить</a>
	    </li> 
	  <?php } ?>
	  </ul>
  <?php } ?>
</div>
  <script>
  	$(document).ready(function() {
	    $('#sortable').sortable({
	    	placeholder: 'ui-state-highlight',
	    	delay: 100,
	    	items: 'li',
	    	stop:function(event, ui){
	    		$.ajax({
				    type:'POST',
				    url: '<?=SITE_URL?>/admin/gallery/sort_images',
				    data: $('#sortable').sortable("serialize"),
				    success: function(data){
				    	var result = JSON.parse(data);
				    	if(result['error'])alert(result['error']);
				    	console.log(result['success']);
				    },
				    error: function(data){
				    	alert('Ошибка при сортировке изображений.');
				    	console.log(data);
				    }
				  });
	    	}
	    });
	    // $('#sortable').disableSelection();
    });
  </script>
