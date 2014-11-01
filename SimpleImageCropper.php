<?php

/**
 * SimpleImageCropper
 *
 * This is just a simple helper for cropping images in center.
 * Or you can just resize yuor image.
 * 
 * @author Sami Piirainen
 * @version 1.0
 * @copyright (c) 2014, Sami Piirainen.
 * @license	http://opensource.org/licenses/MIT	MIT License
 */

class SimpleImageCropper {
	/**
	 * @var int
	 */
	public $width;

	/**
	 * @var int
	 */
	public $height;

	/**
	 * @var int
	 */
	public $type;

	/**
	 * @var resource
	 */
	public $image;

	/**
	 * SimpleImageCropper constrctor
	 *
	 * @param resource $filename
	 * @return void
	 */
	public function __construct($filename) {

		list($this->width, $this->height, $this->type) = getimagesize($filename);

		$this->imagecreatefromall($filename);
	}

	/**
	 * imagecreatefrom*
	 *	
	 * @param $file resource
	 * @param $type int
	 */
	private function imagecreatefromall($filename) {

		switch($this->type) {
			case IMAGETYPE_GIF:
				$this->image = imagecreatefromgif($filename);
				break;
			case IMAGETYPE_JPEG:
				$this->image = imagecreatefromjpeg($filename);
				break;
			case IMAGETYPE_PNG:
				$this->image = imagecreatefrompng($filename);
				break;
		}
	}

	/**
	 * do image* 
	 * @param resource $image
	 * @param string $filename optional
	 * @param int $quality optional
	 * @return void
	 */
	private function imageall($image, $filename = null, $quality = 75) {

		switch($this->type) {
			case IMAGETYPE_GIF:
				imagegif($image, $filename, $quality);
				break;
			case IMAGETYPE_JPEG:
				imagejpeg($image, $filename, $quality);
				break;
			case IMAGETYPE_PNG:
				imagepng($image, $filename, ($quality / 10) - 1); // Quality range for png is 0-9
				break;
		}
	}

	/**
	 * Crop image in center
	 *
	 * @param int $thumb_width Width of thumbnail 
	 * @param int $thumn_height Height of thumnnail
	 * @param bool $data Set to true for image returned as string
	 * @param int $r
	 * @param int $g
	 * @param int $b
	 * @return string | SimpleImageCropper
	 */
	public function crop($thumb_width, $thumb_height, $data = false, $r = 255, $g = 255, $b = 255) {

		// Create the thumb template
		$temp = imagecreatetruecolor($thumb_width, $thumb_height);

		// Set background color of thumb template (RGB)
		$color = imagecolorallocate($temp, $r, $g, $b);
		// Fill thumb template
		imagefill($temp, 0, 0, $color);
		
		// Get image aspects
		// Get original image aspect
		$original_aspect = $this->width / $this->height;
		// Get thumb aspect
		$thumb_aspect = $thumb_width / $thumb_height;

		if ($original_aspect >= $thumb_aspect) {
		   // If image is wider than thumbnail (in aspect ratio sense)
		   $new_height = $thumb_height;
		   $new_width = $this->width / ($this->height / $thumb_height);
		}
		else {
		   // If the thumbnail is wider than the image
		   $new_width = $thumb_width;
		   $new_height = $this->height / ($this->width / $thumb_width);
		}

		imagecopyresampled($temp,
		                   $this->image,
		                   0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
		                   0 - ($new_height - $thumb_height) / 2, // Center the image vertically
		                   0, 0,
		                   $new_width, $new_height,
		                   $this->width, $this->height);

		if($data) {
			ob_start(); // start output buffer
		    $this->imageall($temp);
		    $output = ob_get_clean(); // clean output buffer and set $output as output
		    return $output;
		}
		else {
			$this->image = $temp;
			return $this;
		}
	}

	/**
	 * Save image on server
	 * 
	 * @param string $filename Set path/filename of the new image
	 * @param int $quality Set the quality of the saved image
	 * @return void
	 */
	public function save($filename, $quality = 75) {
		$this->imageall($this->image, $filename, $quality);
	}
}
