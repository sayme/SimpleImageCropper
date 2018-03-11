SimpleImageCropper
==================

A light weight php library for cropping and resizing images.

[![Build Status](https://travis-ci.org/sayme/SimpleImageCropper.svg?branch=master)](https://travis-ci.org/sayme/SimpleImageCropper)

## Usage

### Download and install SimpleImageCropper

To install SimpleImageCropper run the following command:

``` bash
$ composer require sayme/simple-image-cropper
```

### Initialize

Initialize SimpleImageCropper with URL as image source.

```php
use SimpleImageCropper\Cropper;

$cropper = new Cropper('http://example.com/your-image.png');
```

Or you can use `$_FILES['filename']['tmp_name']` as source.

```php
$cropper = new Cropper($_FILE['filename']['tmp_name']);
```

When initializing the Cropper you will have access to some of the original image meta.

* Image width `$cropper->getWidth()`
* Image height `$cropper->getHeight()`
* Image type  `$cropper->getType()`

### Crop and save image

Cropping the image in center and saving the new image.

```php
$width = 150;
$height = 150;
// This will crop the image in center with the new width and height
$cropper->crop($width, $height);

// This will save your new image as mynewimage.png in the current directory
$cropper->save('mynewimage.png');

// You can also set the quality of the image to be saved in the second parameter.
// The quality is by default 75, you can set it to a quality between 0-100
$cropper->save('mynewumage.png', 50);
```

### Output image as BLOB

You can also output the image as BLOB for saving it in your database or just outputting it directly.

```php
echo $cropper->getData();
```

### Color png backgrounds

You can also set the background color of pngs (RGB)

```php
// set the color
$color = [
    'r' => 150,
    'g' => 150,
    'b' => 150
];

$cropper->crop($width, $height, $color['r'], $color['g'], $color['b']);
```

## Examples

### Save image
```php
use SimpleImageCropper\Cropper;

$cropper = new Cropper('http://example.com/your-image.png');

// Crop the image by 200x200
$cropper->crop(200, 200);

// Save the image as mynewimage.png
$cropper->save('mynewimage.png');
```

### Output image (BLOB)

```php
// Set header
header('Content-Type: image/png');

use SimpleImageCropper\Cropper;

$cropper = new Cropper('http://example.com/your-image.png');

// Crop the image by 200x200 and output it.
echo $cropper->crop(200, 200)->getData();
```

### Resize image and keep the proportions

```php
$cropper = new Cropper($_FILES['filename']['tmp_name']);

// Set the new width
$newWidth = 306;

// Check proportions
$proportion = $newWidth / $img->width;

// Set the new height
$newHeight = $img->height * $proportion;

// Crop the image by its new width and height
$cropper->crop($newWidth, $newHeight);

// Save the image with a 50% quality
$cropper->save('mynewimage.png', 50);
```