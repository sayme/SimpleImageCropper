<?php

/*
 * This file is part of the SimpleImageCropper package.
 *
 * (c) Sami Piirainen <sami.spp@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests;

use PHPUnit\Framework\TestCase;
use SimpleImageCropper\Cropper;

class CropperTest extends TestCase
{
    /**
     * @dataProvider getImages
     */
    public function testCropper(string $source, string $filename, int $type): void
    {
        $image = new Cropper($source);

        $this->assertSame($image->getWidth(), 300);
        $this->assertSame($image->getHeight(), 150);
        $this->assertSame($image->getType(), $type);

        $image->crop(50, 50);
        $this->assertNotNull($image->getData());

        $image->save($filename);
        $this->assertTrue(file_exists($filename));

        list($width, $height) = getimagesize($filename);
        $this->assertSame($width, 50);
        $this->assertSame($height, 50);

        unlink($filename);
    }

    public function getImages(): \Iterator
    {
        yield [__DIR__.'/images/test.jpg', __DIR__.'/tmp/test.jpg', IMAGETYPE_JPEG];
        yield [__DIR__.'/images/test.png', __DIR__.'/tmp/test.png', IMAGETYPE_PNG];
        yield [__DIR__.'/images/test.gif', __DIR__.'/tmp/test.gif', IMAGETYPE_GIF];
    }
}
