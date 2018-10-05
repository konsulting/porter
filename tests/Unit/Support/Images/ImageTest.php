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

        $this->assertEquals('name', $image->getName());
        $this->assertEquals('localPath', $image->getLocalPath());
    }

    /** @test */
    public function an_image_can_be_missing_a_local_path()
    {
        $image = new Image('name');

        $this->assertNull($image->getLocalPath());
    }

    /** @test */
    public function it_returns_an_unversioned_image_name()
    {
        $image = new Image('name:1.0.0');

        $this->assertEquals('name', $image->getUnVersionedName());
    }
}
