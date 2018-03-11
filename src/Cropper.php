<?php

/*
 * This file is part of the SimpleImageCropper package.
 *
 * (c) Sami Piirainen <sami.spp@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimpleImageCropper;

class Cropper
{
    /**
     * @var int
     */
    private $width;

    /**
     * @var int
     */
    private $height;

    /**
     * @var int
     */
    private $type;

    /**
     * @var resource
     */
    private $image;

    /**
     * Cropper constrctor.
     *
     * @param resource $filename
     *
     * @throws \SimpleImageCropper\Exception\InvalidImageException
     */
    public function __construct(string $filename)
    {
        list($this->width, $this->height, $this->type) = getimagesize($filename);

        $this->imageCreateFromAll($filename);

        if (!is_resource($this->image)) {
            throw new \SimpleImageCropper\Exception\InvalidImageException(
                sprintf('%s is not a valid resource.', $filename)
            );
        }
    }

    /**
     * Crop image in center.
     *
     * @param int $thumbWidth  Width of thumbnail
     * @param int $thumbHeight Height of thumnnail
     * @param int $r
     * @param int $g
     * @param int $b
     *
     * @return self|string
     */
    public function crop(int $thumbWidth, int $thumbHeight, int $r = 255, int $g = 255, int $b = 255): self
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
        $thumbAspect = $thumbWidth / $thumbHeight;

        if ($originalAspect >= $thumbAspect) {
            // If image is wider than thumbnail (in aspect ratio sense)
            $newHeight = $thumbHeight;
            $newWidth = $this->width / ($this->height / $thumbHeight);
        } else {
            // If the thumbnail is wider than the image
            $newWidth = $thumbWidth;
            $newHeight = $this->height / ($this->width / $thumbWidth);
        }

        imagecopyresampled(
            $temp,
            $this->image,
            0 - ($newWidth - $thumbWidth) / 2, // Center the image horizontally
            0 - ($newHeight - $thumbHeight) / 2, // Center the image vertically
            0,
            0,
            $newHeight,
            $newHeight,
            $this->width,
            $this->height
        );

        $this->image = $temp;

        return $this;
    }

    /**
     * Get image data.
     *
     * @return null|string
     */
    public function getData(): string
    {
        if (!$this->image) {
            return null;
        }

        ob_start();
        $this->imageAll($this->image);
        $data = ob_get_clean();

        return $data;
    }

    /**
     * Save image on server.
     *
     * @param string $filename Set path/filename of the new image
     * @param int    $quality  Set the quality of the saved image
     */
    public function save(string $filename, int $quality = 75): void
    {
        $this->imageAll($this->image, $filename, $quality);

        imagedestroy($this->image);
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getType(): int
    {
        return $this->type;
    }

    /**
     * Imagecreatefrom*.
     *
     * @param string $filename
     */
    private function imageCreateFromAll(string $filename): void
    {
        switch ($this->type) {
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
     * Do image*.
     *
     * @param resource $image
     * @param string   $filename
     * @param int      $quality
     */
    private function imageAll($image, string $filename = null, int $quality = 75): void
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
}
