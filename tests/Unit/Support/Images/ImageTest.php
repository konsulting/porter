<?php

namespace Tests\Unit\Support\Images;

use App\Support\Images\Image;
use Tests\BaseTestCase;

class ImageTest extends BaseTestCase
{
    /** @test */
    public function an_image_returns_its_name_and_path()
    {
        $image = new Image('name', 'localPath');

        $this->assertEquals('name', $image->name);
        $this->assertEquals('localPath', $image->localPath);
    }

    /** @test */
    public function an_image_can_be_missing_a_local_path()
    {
        $image = new Image('name');

        $this->assertNull($image->localPath);
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function an_images_name_cannot_change()
    {
        $image = new Image('name', 'localPath');

        $image->name = 'new name';
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function an_images_local_path_cannot_change()
    {
        $image = new Image('name', 'localPath');

        $image->localPath = 'a new path';
    }
}
