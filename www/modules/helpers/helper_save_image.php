<?php class helper_save_image{

public $temp_dir;
	public $background;
	public $quality;
	
	public function __construct() {
		$this->temp_dir = ROOT_DIR . '/temp/';
		
		$this->set_background();
		
		$this->quality = 90;
	}

	// v 3.1
	// стандартный helper user_cms переделаный под мултиаплоад и под alfarielt.ru
	// переносит изображение в нужную папку, уменьшает, если требуется создает превьюшку
	// $input_name - name поля формы
	// $new_width - новая ширина
	// $new_width_thrumb - новая ширина превьюхи (0 - значит не надо)
	// $img_path - путь к изображению
	// $new_height - новая высота (обрезается если пропорционально должно быть больше)
	// $new_height_thrumb - новая высота превьюхи
	// возвращает имя изображения

	public function img_upload($image_name, $tmp_image, $new_width, $img_path = '', $new_width_thrumb=0, $watermark=false, $new_height_thrumb='auto', $new_height='auto') {
		$allowed_extensions=array('jpg', 'jpeg', 'gif', 'png');
		$debug=true;
		$error = '';
		if(!$img_path || !is_dir($img_path)) {
			$img_path = $this->temp_dir;
		}
		// проверка формата изображения
		if (!isset($image_name) ) {
			if($debug) {$error ='Изображение не загружено'; }
			else return '';
		}

		$final_extension = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
		// генерируем имя
		$img_name = $this->get_rand_name().'.'.$final_extension;
		// полный путь
		$img_name_full = $img_path . $img_name;

		// проверка формата изображения
		if (in_array($final_extension, $allowed_extensions)) {
			if($debug) {$error .='Неправильный формат фотографий, только JPG, PNG, GIF<br>';  }
			else return false;
		}
		// проверка папки на существование
		if(!is_dir($img_path)) {
			if($debug) {$error .='Папки "'.$img_path.'" не существует.<br>';  }
			else return false;
			//mkdir($img_path);
		}
		if ( !move_uploaded_file($tmp_image, $img_name_full) ) {
			if($debug) { $error .='Не удалось переместить изображение в папку '.$img_path.' (проверьте права на запись)<br>';  }
			else return false;
		} else {
			// делаем превьюху
			if($new_width_thrumb!=0) {
				$thrumb_name_full = $img_path .'mini/'. $img_name;  // полный путь до превьюхи
				// проверка папки на существование, если нет, создаем
				if(!is_dir($img_path .'mini/')) {
					if(!mkdir($img_path .'mini/') ) {
						if($debug) { $error .='Не удалось создать папку для превью: "'.$img_path .'mini/<br>';  }
						else return false;
					}
				}

				// генерация
				if (!$this->resize($img_name_full, $thrumb_name_full, $new_width_thrumb, $new_height_thrumb) ) {
					if($debug) {$error .='Не удалось уменьшить до превью<br>';  }
					else return false;
				}
			}
		
			// уменьшаем
			if (!$this->resize($img_name_full, $img_name_full, $new_width, $new_height)) {
				if($debug) {$error .='Не удалось уменьшить до макси<br>'; }
				else return false;
			};

			if($watermark){
				$this->stamp($img_path, $img_name, $margin_right=15, $margin_bottom=15, $stamp_image = $watermark);
			}

			return $img_name;
		}
		if(!empty($error)) {
			die ('<div class="notice error" style="font-size:30px;">'.$error.'</div>');
		}
	}
	
	public function get_rand_name(){
		return time() . '_' . rand();
	}


	public function resize($src, $dest, $w=0, $h=0) {
		$i = getimagesize($src);
		$src_w = $i[0];
		$src_h = $i[1];
		if (($w > 0 && $src_w < $w) || ($h > 0 && $src_h < $h)) return copy($src, $dest);
		$save_alpha = strtolower(pathinfo($dest, PATHINFO_EXTENSION)) == 'png';

		switch ($i['mime']) {
			case 'image/jpeg':
			$src_img = imagecreatefromjpeg($src);
			break;

			case 'image/gif':
			$src_img = imagecreatefromgif($src);
			break;

			case 'image/png':
			$src_img = imagecreatefrompng($src);
			break;

			default:
			$src_img = null;
		}

		if (!is_null($src_img)) {
			if ($h == 0 || !is_numeric($h)) {
				$dest_w = $w;
				$dest_h = round($w*$src_h/$src_w);
			} else {
				// Resize in two steps (through $pre_img)
				// 1 Crop part with needed aspect ratio (and maximum size) from center of source (part placed to $pre_img)
				// 2 Replace $src_img by $pre_img;
				$dest_w = $w;
				$dest_h = $h;
				$src_ar = $src_w/$src_h; //source image aspect ratio
				$dest_ar = $dest_w/$dest_h; //destination image aspect ratio
				// 1
	            if ($src_ar > $dest_ar) {
	            	$pre_h = $src_h;
	            	$pre_w = round($dest_ar*$pre_h);
	            	$src_x = round(($src_w-$pre_w)/2);
	            	$src_y = 0;
	            } else {
	                $pre_w = $src_w;
	                $pre_h = round($pre_w/$dest_ar);
	                $src_x = 0;
	                $src_y = round(($src_h-$pre_h)/2);
	            }
 	            $pre_img = imagecreatetruecolor($pre_w, $pre_h);
 	            imagealphablending($pre_img, true);
 	            $bg = imagecolorallocatealpha($pre_img, 0, 0, 0, 127); 
				imagefill($pre_img, 0, 0, $bg); 
	            imagecopy($pre_img, $src_img, 0, 0, $src_x, $src_y, $pre_w, $pre_h);
	            // 2
				imagedestroy($src_img);
				$src_img = $pre_img;
				$src_w = $pre_w;
				$src_h = $pre_h;
			}

			$dest_img = imagecreatetruecolor($dest_w, $dest_h);
			if ($save_alpha) {
				imagealphablending($dest_img, true);
 	            $bg = imagecolorallocatealpha($dest_img, 0, 0, 0, 127); 
				imagefill($dest_img, 0, 0, $bg); 
			} else {
				$bg = imagecolorallocate($dest_img, $this->background['r'], $this->background['g'], $this->background['b']);
				imagefill($dest_img, 0, 0, $bg);
			}
			imagecopyresampled($dest_img, $src_img, 0, 0, 0, 0, $dest_w, $dest_h, $src_w, $src_h);
			imagedestroy($src_img);

			$ext = strtolower(pathinfo($dest, PATHINFO_EXTENSION));
			switch ($ext) {
				case 'jpeg':
				case 'jpg':
				imagejpeg($dest_img, $dest, $this->quality);
				$ret = true;
				break;

				case 'png':
				imagealphablending($dest_img, false);
				imagesavealpha($dest_img, true);
				imagepng($dest_img, $dest);
				$ret = true;
				break;

				case 'gif':
				imagegif($dest_img, $dest);
				$ret = true;
				break;

				default:
				$ret = false;
			}
			return $ret;
		} else {
			return false;
		}
	}
	
	
	public function set_background($r = 255, $g = 255, $b = 255, $a = 127) {
		$this->background = array('r' => $r, 'g' => $g, 'b' => $b, 'a' => $a);
		//$this->background = array('r' => $r, 'g' => $g, 'b' => $b, 'a' => 127);
	}
	
	public function stamp($image_dir, $image_name, $margin_right=10, $margin_bottom=10, $stamp_image = 'stamp.png'){
		  $stamp = imagecreatefrompng($stamp_image);


		$image_path = $image_dir . $image_name;		  
		  $dir = $image_dir;
		  $file = $image_name;
		  $target = $image_dir;
	  
		  $exif_result=@exif_imagetype($image_path)?@exif_imagetype($image_path):0; 
		  if (($exif_result<1)or($exif_result>3)) return FALSE;

		  else if ($exif_result==1) $im = imagecreatefromgif($image_path);
		  else if ($exif_result==2) $im = imagecreatefromjpeg($image_path);
		  else if ($exif_result==3) $im = imagecreatefrompng($image_path);

		  
		
		  
		  $sx = imagesx($stamp);
		  $sy = imagesy($stamp);
		  /*imagecopy($im, $stamp, imagesx($im) - $sx - $margin_right, imagesy($im) - $sy - $margin_bottom, 0, 0, imagesx($stamp), imagesy($stamp));*/
		  imagecopyresampled($im, $stamp, imagesx($im) - 150 - $margin_right, imagesy($im) - 30 - $margin_bottom, 0, 0, 150, 35, imagesx($stamp), imagesy($stamp));


		 if ($exif_result==3) {		
			imagealphablending($im, false);
			imagesavealpha($im, true);
			$background = imagecolorallocatealpha($im, $this->background['r'], $this->background['g'], $this->background['b'], $this->background['a']);
			imagecolortransparent($im, $background);
		}
		  
		  $tmp_file = explode('.',$file);
		  if (($exif_result==1)and(end(explode('.',$file))!='gif')) $file.='.gif';
		  else if (($exif_result==2)and($tmp_file!='jpeg')) $file.='.jpeg';
		  else if (($exif_result==3)and($tmp_file!='png')) {
			// $file=substr($file,0,strLen($file)-1-strLen($tmp_file));
			$file.='.png';
		  }
		  
		  $file1=$file;
		  $i=0;
		  while (file_exists($target.'/'.$file1)) {
			$i++; $file1='('.$i.')'.$file;
		  }
		  
		  $file=$file1;
		  
		  unlink($image_path);
		  
		  $target_file=$image_path;
		  
		  if ($exif_result==1) {
			imagegif($im,$target_file); 
		  }
		  else if ($exif_result==2) {
			  imagejpeg($im,$target_file, 100); 
		  }
		  else if ($exif_result==3) {
			  imagepng($im,$target_file); 
		  }
		  
		  imagedestroy($im);
	}













}

?>