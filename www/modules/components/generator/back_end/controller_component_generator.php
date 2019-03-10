<?php


class controller_component_generator extends component  {

	function action_index() {

		if(isset($_POST['submit'])) {
			if(isset($_POST['type']) && !empty($_POST['name'])) {
				$dir = ROOT_DIR . '/modules/'.$_POST['type'].'/' . $_POST['name'];
				$_POST['type'] = substr($_POST['type'],0,-1);
				//echo $_POST['type'];
				if( file_exists($dir) && is_dir($dir) ) {
					$this->data['error'] = 'Такой модуль ('.$_POST['name'].') уже есть!';

				}
				elseif ($_POST['type']=='component') {

					mkdir($dir);
					mkdir($dir.'/front_end');
					mkdir($dir.'/front_end/views');
					mkdir($dir.'/back_end');
					mkdir($dir.'/back_end/views');

					file_put_contents($dir.'/back_end/controller_'.$_POST['type'].'_' . $_POST['name'] . '.php','<?php class controller_'.$_POST['type'].'_' . $_POST['name'] . ' extends '. $_POST['type'] . ' {
						function action_index() {
							$this->page[\'title\'] = \''. $_POST['type']. ' '. $_POST['name'] . '\';

							$this->page[\'html\'] = $this->load_view();
							return $this->page;
						}

					} ');
					file_put_contents($dir.'/back_end/model_'.$_POST['type'].'_' . $_POST['name'] . '.php','<?php class model_'.$_POST['type'].'_' . $_POST['name'] . ' extends model {} ');
					file_put_contents($dir.'/back_end/views/index.tpl','<h1>'.$_POST['type'].' ' . $_POST['name'] . '</h1> <em>текст</em> ');

					file_put_contents($dir.'/front_end/controller_'.$_POST['type'].'_' . $_POST['name'] . '.php','<?php class controller_'.$_POST['type'].'_' . $_POST['name'] . ' extends '. $_POST['type'] . ' {
						function action_index() {
							$this->page[\'title\'] = \''. $_POST['type']. ' '. $_POST['name'] . '\';

							$this->page[\'html\'] = $this->load_view();
							return $this->page;
						}

					} ');
					
					file_put_contents($dir.'/front_end/model_'.$_POST['type'].'_' . $_POST['name'] . '.php','<?php class model_'.$_POST['type'].'_' . $_POST['name'] . ' extends model {} ');
					file_put_contents($dir.'/front_end/views/index.tpl','<h1>'.$_POST['type'].' ' . $_POST['name'] . '</h1> <em>текст</em> ');
					$this->data['message'] = $_POST['type'].' сгенерирован';

				}elseif ($_POST['type']=='plugin'){

					mkdir($dir);
					mkdir($dir.'/views');

					file_put_contents($dir.'/controller_'.$_POST['type'].'_' . $_POST['name'] . '.php','<?php class controller_'.$_POST['type'].'_' . $_POST['name'] . ' extends '. $_POST['type'] . ' {
						function action_index() {

							$this->page[\'html\'] = $this->load_view();
							return $this->page;
						}

					} ');
					file_put_contents($dir.'/views/index.tpl','<h1>'.$_POST['type'].' ' . $_POST['name'] . '</h1> <em>текст</em> ');

					$sqls = "INSERT INTO installed_modules ('name', 'type', 'dir', 'version', 'date_add', 'description') VALUES ('".$_POST['name']."', '".$_POST['type']."', '".$_POST['name']."', '1.0', '" . time() . "', '')";
					$this->dbh->exec($sqls);
					$this->data['message'] = $_POST['type'].' сгенерирован';
				} else if ($_POST['type']=='block'){

					mkdir($dir);
					mkdir($dir.'/views');

					file_put_contents($dir.'/controller_'.$_POST['type'].'_' . $_POST['name'] . '.php','<?php class controller_'.$_POST['type'].'_' . $_POST['name'] . ' extends '. $_POST['type'] . ' {
						function action_index() {

							$this->page[\'html\'] = $this->load_view();
							return $this->page;
						}

					} ');
					file_put_contents($dir.'/views/index.tpl','<h1>'.$_POST['type'].' ' . $_POST['name'] . '</h1> <em>текст</em> ');

					$sqls = "INSERT INTO installed_modules ('name', 'type', 'dir', 'version', 'date_add', 'description') VALUES ('".$_POST['name']."', '".$_POST['type']."', '".$_POST['name']."', '1.0', '" . time() . "', '')";
					$this->dbh->exec($sqls);
					$this->data['message'] = $_POST['type'].' сгенерирован';
				} else if ($_POST['type']=='addon'){
					mkdir($dir);

					file_put_contents($dir.'/controller_'.$_POST['type'].'_' . $_POST['name'] . '.php','<?php class controller_'.$_POST['type'].'_' . $_POST['name'] . ' extends '. $_POST['type'] . ' {
						function action_index() {

							$this->page[\'html\'] = $this->load_view();
							return $this->page;
						}

					} ');

					$sqls = "INSERT INTO installed_modules ('name', 'type', 'dir', 'version', 'date_add', 'description') VALUES ('".$_POST['name']."', '".$_POST['type']."', '".$_POST['name']."', '1.0', '" . time() . "', '')";
					$this->dbh->exec($sqls);
					$this->data['message'] = $_POST['type'].' сгенерирован';
				} else if ($_POST['type']=='helper'){
					mkdir($dir);
					file_put_contents($dir.'/'.$_POST['name'] . '.php', '<?php class ' . $_POST['name'] . '{}');
					$this->data['message'] = $_POST['type'].' сгенерирован';
				}


			}elseif(empty($_POST['name'])){
				$this->data['error'] = 'Введите название модуля!';
				
			}
			
		}
		$this->page['title'] = 'Генератор модулей';

		$this->page['html'] = $this->load_view();
		return $this->page;
	}
}