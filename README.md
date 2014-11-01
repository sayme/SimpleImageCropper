SimpleImageCropper
==================

SimpleImageCropper (v1.0)

A very light weight php library for cropping and resizing images.



## Usage

### Initialize

Initialize SimpleImageCropper with URL as image source.

```php

require 'path/to/SimpleImageCropper.php';

$img = new SimpleImageCropper('http://upload.wikimedia.org/wikipedia/commons/b/b5/Navionics_Apple_Team.png');

```

Or you can use `$_FILES['filename']['tmp_name']` as source.

```php
$img = new SimpleImageCropper($_FILE['filename']['tmp_name']);
```

When initializing the class you will have access to some of the image properties.

Properties:

* Image width (int) `$img->width`
* Image height (int) `$img->height`
* Image type (string) `$img->type`
* Image resource (resource) `$img->resource`

### Crop and save image

Cropping the image in center and saving the new image.

```php
$width = 150;
$height = 150;
// This will crop the image in center with the new width and height
$img->crop($width, $height);

// This will save your new image as mynewimage.png in the current directory
$img->save('mynewimage.png');

// You can also set the quality of the image to be saved in the second parameter.
// The quality is by default 75, you can set it to a quality between 0-100
$img->save('mynewumage.png', 50); 
```

### Output image as BLOB

You can also output your image as BLOB for saving it in your database or just outputing it directly.

```php
// By setting the third parameter as true, it will output the image as BLOB
print_r($img->crop($width, $height, true));

```

### Color .png backgrounds

You can also set the background color of you pngs (RGB)

```php

// set the color
$color = array(
	'r' => 150,
	'g' => 150,
	'b' => 150
);
// Set the third paramter to false if you dont want it as a BLOB
$img->crop($width, $height, false, $color['r'], $color['g'], $color['b']);
```

## Examples

### Save image
```php
require 'SimpleImageCropper.php';

$img = new SimpleImageCropper('http://upload.wikimedia.org/wikipedia/commons/b/b5/Navionics_Apple_Team.png');

// Crop the image by 200x200
$img->crop(200, 200);

// Save the image as mynewimage.png
$img->save('mynewimage.png');
```

### Output image (BLOB)

```php
// Set header
header('Content-Type: image/png');

require 'SimpleImageCropper.php';

$img = new SimpleImageCropper('http://upload.wikimedia.org/wikipedia/commons/b/b5/Navionics_Apple_Team.png');

// Crop the image by 200x200 and output it.
print_r($img->crop(200, 200, true));

```

### Resize image and keep the proportions

```php
$img = new SimpleImageCropper($_FILES['filename']['tmp_name']);

// Set the new width
$new_width = 306;

// Check proportions
$proportion = $new_width / $img->width;

// Set the new height
$new_height = $img->height * $proportion;

// Crop the image by its new width and height
$img->crop($new_width, $new_height);

// Save the image with a 50% quality
$img->save('mynewimage.png', 50);
```
