<?php

namespace Tests\Unit;

use App\Porter;
use Tests\TestCase;

class HelpersTest extends TestCase
{
    /** @test */
    public function docker_compose_retains_the_original_command_and_includes_the_yaml_path()
    {
        $this->assertContains('[command]', docker_compose('[command]'));
        $this->assertContains('.yaml', docker_compose());
    }
}
