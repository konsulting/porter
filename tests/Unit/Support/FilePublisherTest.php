<?php

namespace Tests\Unit\Support;

use App\Support\FilePublisher;
use Illuminate\Filesystem\Filesystem;
use Tests\BaseTestCase;

class FilePublisherTest extends BaseTestCase
{
    /** @test */
    public function it_publishes_the_config_files_to_the_library()
    {
        $publisher = new FilePublisher($files = new Filesystem());

        $files->deleteDirectory($to = storage_path('test_library/publish_config_test'));

        $publisher->publish(
            $from = resource_path('stubs/config'),
            $to
        );

        $this->assertDirectoryExists($to);

        foreach ($files->allFiles($from) as $file) {
            $this->assertFileExists($to.'/'.$file->getRelativePath());
        }
    }
}
