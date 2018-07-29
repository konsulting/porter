<?php

namespace App;

use App\Nginx\SiteConfBuilder;
use App\Ssl\CertificateBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;

class Site extends Model
{
    protected $guarded = [];

    public function php_version()
    {
        return $this->belongsTo(PhpVersion::class);
    }

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

        Artisan::call('make-files');
    }

    public function unsecure()
    {
        (new CertificateBuilder(storage_path('ssl')))->destroy($this->url);

        $this->update(['secure' => false]);

        Artisan::call('make-files');
    }

    public function remove()
    {
        (new CertificateBuilder(storage_path('ssl')))->destroy($this->url);
        app(SiteConfBuilder::class)->destroy($this);

        $this->delete();

        Artisan::call('make-files');
    }

    public function setPhpVersion($phpVersionId)
    {
        $this->update(['php_version_id' => $phpVersionId ?: PhpVersion::defaultVersion()->id]);

        Artisan::call('make-files');
    }

    public function setNginxType($type)
    {
        $this->update(['nginx_type' => $type ?? 'default']);

        Artisan::call('make-files');
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
            'nginx_type' => 'default',
            'php_version_id' => PhpVersion::defaultVersion()->id,
            'secure' => false,
        ]);
    }
}
