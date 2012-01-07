<?php
/**
 */

class Tg_File_Image
{
	public function __construct($file) {

		if(!is_file($file)) {
			throw new Zend_Exception('Attempted to manipulate a non existent file');
		}
		else {
			if($image_info = getimagesize($file)) {
				if($image_info[2] < 1 || $image_info[2] > 3) {
					throw new Zend_Exception('Image type not recognised');
				}
			}
			else {
				//dump($image_info, $file);
				//die();
				throw new Zend_Exception('File is not an image file');
			}
		}

		$this->_file = $file;
		$this->_info = $image_info;
	}

	function resize($max_width, $max_height, $destination, $options = null) {
		echo $destination;
		$source = $this->_file;
		$orig_width = $this->_info[0];
		$orig_height = $this->_info[1];

		if(is_array($options) && (isset($options['force']) || isset($options['strip']))) {
			// we're forcing this bad boy!
		}
		else {
			if($max_width >= $orig_width && $max_height >= $orig_height) {
				copy($source, $destination);
				return true;
			}
		}

		// strip pixels off the image
		$temp_destination = null;
		if(is_array($options) && isset($options['strip'])) {
			$temp_destination = $destination.'_temp';
			$orig_width = $orig_width - ((int)$options['strip'] * 2);
			$orig_height = $orig_height - ((int)$options['strip'] *2);
			$this->crop((int)$options['strip'], (int)$options['strip'], $orig_width, $orig_height, $temp_destination);
			$source = $temp_destination;
		}

		/*
		    1 => 'GIF',
	        2 => 'JPG',
	        3 => 'PNG',
		*/
		switch($this->_info[2]) {
			case(1):
				$src = imagecreatefromgif($source);
				$imageType = 'gif';

				// convert transparencies pixel by pixel
				$original_image_width = imagesx( $src );
				$original_image_height = imagesy( $src );
				if ( !imageistruecolor($src) ) {
					# detects if its non-true color image
					if ( imagecolortransparent($src) >= 0 ) {
						# detects if any of the color palette has been set to transparent
						$truecolor = imagecreatetruecolor( $original_image_width, $original_image_height );
						for ($x = 0; $x < $original_image_width; $x++) {
							for ($y = 0; $y < $original_image_height; $y++) {
								$color_index = ImageColorAt($src, $x, $y);
								if ( !$color_palette[$color_index] ) {
									$rgb = imagecolorsforindex($src, $color_index);
									$color_to_set = imagecolorallocate($truecolor, $rgb['red'], $rgb['green'], $rgb['blue']);
									$color_palette[$color_index] = $color_to_set;
								} else {
									$color_to_set = $color_palette[$color_index];
								}
								imagesetpixel($truecolor, $x, $y, $color_to_set);
							}
						}
						imagedestroy($src);
						$src = $truecolor;
					}
				}

			break;

			case(2):
				$src = imagecreatefromjpeg($source);
				$imageType = 'jpg';
			break;

			case(3):
				$src = imagecreatefrompng($source);
				$imageType = 'png';
			break;
		}

		if(is_array($options) && isset($options['crop']) && $options['crop'] == true) {
			$x_ratio = $max_width / $orig_width;
			$y_ratio = $max_height / $orig_height;

	       	$output_height = ceil($x_ratio * $orig_height);
	        $output_width = $max_width;

			if ($output_height < $max_height) {
			    $output_width = ceil($y_ratio * $orig_width);
			    $output_height = $max_height;
			}

			$old_orig_width = $orig_width;
			$old_orig_height = $orig_height;

			$orig_width = ceil(($max_width / $output_width) * $orig_width);
			$orig_height = ceil(($max_height / $output_height) * $orig_height);

			$cropLeft = ($old_orig_width/2) - ($orig_width/2);
			$cropTop =  ($old_orig_height/2) - ($orig_height/2);

			$output_width = $max_width;
			$output_height = $max_height;

			/*
			echo 'orig_width: '.$orig_width.'<br />';
			echo 'orig_height: '.$orig_height.'<br />';
			echo 'output_width: '.$output_width.'<br />';
			echo 'output_height: '.$output_height.'<br />';
			echo 'cropLeft: '.$cropLeft.'<br />';
			echo 'cropTop: '.$cropTop.'<br />';
			*/
		}
		else {

			$x_ratio = $max_width / $orig_width;
			$y_ratio = $max_height / $orig_height;

			if( ($orig_width <= $max_width) && ($orig_height <= $max_height) ){
				// force a resize
			    $output_width = $orig_width - 1;
			    $output_height = $orig_height - 1;
			}
			else if (($x_ratio * $orig_height) < $max_height) {
		       	$output_height = ceil($x_ratio * $orig_height);
		        $output_width = $max_width;
			}
			else {
			    $output_width = ceil($y_ratio * $orig_width);
			    $output_height = $max_height;
			}

			$cropLeft = 0;
			$cropTop = 0;
		}


		$this->_tmp = imagecreatetruecolor($output_width,$output_height);

		// imagecolortransparent($this->_tmp, imagecolorallocate($this->_tmp, 0, 0, 0));
		//
		// imagealphablending($this->_tmp, false);
		// imagesavealpha($this->_tmp, true);
		// // imagetruecolortopalette($this->_tmp, true, 256);

		imagecopyresampled($this->_tmp,$src,0,0,$cropLeft,$cropTop,$output_width, $output_height, $orig_width, $orig_height);




		/*
		$colour = imagecolorallocate($this->_tmp, 0, 0, 0);
		$colour2 = imagecolorallocate($this->_tmp, 255, 255, 255);
		$time = date('H:i:s');
		imagestring($this->_tmp, 5, 5, 5, $time, $colour);
		imagestring($this->_tmp, 5, 6, 6, $time, $colour2);
		*/

		if($imageType == 'gif') {
			imagegif($this->_tmp, $destination);
		}
		else {
			imagejpeg($this->_tmp, $destination, 95);
		}
		imagedestroy($src);
		imagedestroy($this->_tmp);

		if(isset($temp_destination) && is_file($temp_destination)) {
			@unlink($temp_destination);
		}
	}

	function crop($x, $y, $width, $height, $destination = null) {

		$source = $this->_file;
		$orig_width = $this->_info[0];
		$orig_height = $this->_info[1];

		/*
		    1 => 'GIF',
	        2 => 'JPG',
	        3 => 'PNG',
		*/
		switch($this->_info[2]) {
			case(1):
				$src = imagecreatefromgif($source);
				$imageType = 'gif';
				
				// convert transparencies pixel by pixel
				$original_image_width = imagesx( $src );
				$original_image_height = imagesy( $src );
				if ( !imageistruecolor($src) ) {
					# detects if its non-true color image
					if ( imagecolortransparent($src) >= 0 ) {
						# detects if any of the color palette has been set to transparent
						$truecolor = imagecreatetruecolor( $original_image_width, $original_image_height );
						for ($x = 0; $x < $original_image_width; $x++) {
							for ($y = 0; $y < $original_image_height; $y++) {
								$color_index = ImageColorAt($src, $x, $y);
								if ( !$color_palette[$color_index] ) {
									$rgb = imagecolorsforindex($src, $color_index);
									$color_to_set = imagecolorallocate($truecolor, $rgb['red'], $rgb['green'], $rgb['blue']);
									$color_palette[$color_index] = $color_to_set;
								} else {
									$color_to_set = $color_palette[$color_index];
								}
								imagesetpixel($truecolor, $x, $y, $color_to_set);
							}
						}
						imagedestroy($src);
						$src = $truecolor;
					}
				}				
			break;

			case(2):
				$src = imagecreatefromjpeg($source);
				$imageType = 'jpg';
			break;

			case(3):
				$src = imagecreatefrompng($source);
				$imageType = 'png';
			break;
		}

        $output_width = $width;
       	$output_height = $height;

		$cropLeft = $x;
		$cropTop =  $y;

		$this->_tmp = imagecreatetruecolor($output_width,$output_height);
		imagecopyresampled($this->_tmp,$src,0,0,$cropLeft, $cropTop, $output_width, $output_height, $output_width, $output_height);

		if(!$destination) {
			$destination = $source;
		}

		if($imageType == 'gif') {
			imagegif($this->_tmp, $destination);
		}
		else if($imageType == 'png') {
			imagepng($this->_tmp, $destination);
		}
		else {
			imagejpeg($this->_tmp, $destination, 95);
		}
		imagedestroy($this->_tmp);
	}
}
