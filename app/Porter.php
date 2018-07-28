<?php
namespace App;

use Illuminate\Support\Facades\Artisan;

class Porter
{
    protected $settings;

    public function __construct(string $settingsPath)
    {
        $this->settings = new Settings($settingsPath);
    }

    /**
     * @return Settings
     */
    public function getSettings()
    {
        return $this->settings;
    }

    public function changeSetting($key, $value)
    {
        app(Porter::class)->getSettings()->set($key, $value);

        Artisan::call('make-files');
    }

    public function updateProject($name, $merge = [])
    {
        $projects = collect(app(Porter::class)->getSettings()->get('projects'));

        $index = $projects->search(function ($p) use ($name) {
            return $p['name'] == $name;
        });

        if ($index === false) {
            $projects->push(array_merge(['name'=> $name], $merge));
        } else {
            $projects[$index] = array_merge($projects[$index], $merge);
        }

        app(Porter::class)->getSettings()->set('projects', $projects->toArray());

        Artisan::call('make-files');
    }

    public function isUp()
    {
        $output = [];
        exec(docker_compose("ps | grep porter_"), $output);

        return ! empty($output);
    }

    public function resolveProject()
    {
        return collect(settings("projects"))
            ->where('name', $this->resolveProjectName())
            ->first();
    }

    public function resolveProjectName()
    {
        $currentPath = getcwd();
        $home = settings('path');

        if (! str_start($currentPath, $home)) {
            return null;
        }

        $currentPath = trim(str_after($currentPath, $home), DIRECTORY_SEPARATOR);

        return str_before($currentPath, DIRECTORY_SEPARATOR);
    }
}
