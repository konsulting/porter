<?php

namespace App;

use App\Nginx\SiteConfBuilder;
use App\Ssl\CertificateBuilder;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    public function resolve()
    {
        if (! $name = site_from_cwd()) {
            return null;
        }

        return static::where('name', $name)->first();
    }

    public function getUrlAttribute()
    {
        return $this->name.'.'.setting('tld');
    }

    public function buildFiles()
    {
        app(SiteConfBuilder::class)->build($this);
    }

    public function secure()
    {
        (new CertificateBuilder(storage_path('ssl')))->build($this->url);

        $this->update(['secure' => true]);
    }

    public function unsecure()
    {
        (new CertificateBuilder(storage_path('ssl')))->destroy();

        $this->update(['secure' => false]);
    }

    public static function firstOrCreateForName($name)
    {
        $result = static::where('name', $name)->first();

        if ($result) {
            return $result;
        }

        return static::createForName($name);
    }

    public static function createForName($name)
    {
        return static::create([
            'name' => $name,
            'php_version_id' => PhpVersion::defaultVersion()->id,
            'secure' => false,
        ]);
    }
}
