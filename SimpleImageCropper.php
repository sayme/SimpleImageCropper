<?php

/**
 * SimpleImageCropper
 *
 * This is just a simple helper for cropping images in center.
 * Or you can just resize your image.
 *
 * @author		Sami Piirainen
 * @version		1.0
 * @copyright	(c) 2014, Sami Piirainen.
 * @license		http://opensource.org/licenses/MIT	MIT License
 * @link		https://github.com/sayme/SimpleImageCropper/
 */
class SimpleImageCropper
{
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
	public function __construct($filename)
	{
		list($this->width, $this->height, $this->type) = getimagesize($filename);

		$this->imageCreateFromAll($filename);
	}

	/**
	 * Imagecreatefrom*
	 *
	 * @param $file resource
	 * @param $type int
	 */
	private function imageCreateFromAll($filename)
	{
		switch ($this->type) {
			case IMAGETYPE_GIF:
				$this->image = imagecreatefromgif($filename);
				break;
			case IMAGETYPE_JPEG:
				$this->image = imagecreatefromjpeg($filename);
				break;
			case IMAGETYPEPNG:
				$this->image = imagecreatefrompng($filename);
				break;
		}
	}

	/**
	 * Do image*
	 *
	 * @param resource $image
	 * @param string $filename optional
	 * @param int $quality optional
	 * @return void
	 */
	private function imageAll($image, $filename = null, $quality = 75)
	{
		switch ($this->type) {
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
	 * @param int $thumbWidth Width of thumbnail
	 * @param int $thumn_height Height of thumnnail
	 * @param bool $data Set to true for image returned as string
	 * @param int $r
	 * @param int $g
	 * @param int $b
	 * @return string | SimpleImageCropper
	 */
	public function crop($thumbWidth, $thumbHeight, $data = false, $r = 255, $g = 255, $b = 255)
	{
		// Create the thumb template
		$temp = imagecreatetruecolor($thumbWidth, $thumbHeight);

		// Set background color of thumb template (RGB)
		$color = imagecolorallocate($temp, $r, $g, $b);
		// Fill thumb template
		imagefill($temp, 0, 0, $color);

		// Get image aspects
		// Get original image aspect
		$originalAspect = $this->width / $this->height;
		// Get thumb aspect
		$thumb_aspect = $thumbWidth / $thumbHeight;

		if ($originalAspect >= $thumb_aspect) {
			// If image is wider than thumbnail (in aspect ratio sense)
			$thumbHeight = $thumbHeight;
			$newWidth = $this->width / ($this->height / $thumbHeight);
		} else {
			// If the thumbnail is wider than the image
			$newWidth = $thumbWidth;
			$newHeight = $this->height / ($this->width / $thumbWidth);
		}

		imagecopyresampled($temp,
		                   $this->image,
		                   0 - ($newWidth - $thumbWidth) / 2, // Center the image horizontally
		                   0 - ($newHeight - $thumbHeight) / 2, // Center the image vertically
		                   0, 0,
		                   $newHeight, $newHeight,
		                   $this->width, $this->height);

		if ($data) {
			ob_start(); // start output buffer
			$this->imageAll($temp);
			$output = ob_get_clean(); // clean output buffer and set $output as output
			return $output;
		} else {
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
	public function save($filename, $quality = 75)
	{
		$this->imageAll($this->image, $filename, $quality);
	}
}
