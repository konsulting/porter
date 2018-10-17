<?php

namespace Tests\Unit;

use App\Models\PhpVersion;
use App\Models\Site;
use App\Support\Nginx\AvailableConfigurations;
use Tests\BaseTestCase;

class AvailableConfigurationsTest extends BaseTestCase
{
    /** @test */
    public function it_retrieve_the_view_locations()
    {
        $configurations = new AvailableConfigurations;

        $this->assertContains(
            base_path('resources/views/nginx'),
            $configurations->getLocations()
        );
    }

    /** @test */
    public function it_returns_a_list_of_conf_files()
    {
        $configurations = new AvailableConfigurations([
            base_path('resources/views/nginx')
        ]);

        $this->assertEquals([
            'default' => 'default',
            'project_root' => 'project_root'
        ], $configurations->getList());
    }

    /** @test */
    public function it_will_highlight_the_current_item_in_the_list()
    {
        $configurations = new AvailableConfigurations([
            base_path('resources/views/nginx')
        ]);

        $this->assertEquals([
            'default' => 'default',
            'project_root' => 'project_root (current)'
        ], $configurations->getList('project_root'));
    }
}
