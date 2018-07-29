<?php

namespace App;

use App\Nginx\SiteConfBuilder;
use App\Ssl\CertificateBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;

class Site extends Model
{
    protected $guarded = [];

    /**
     * PHP Version
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function php_version()
    {
        return $this->belongsTo(PhpVersion::class);
    }

    /**
     * Resolve the site from the current working directory
     *
     * @return null
     */
    public function resolve()
    {
        if (! $name = site_from_cwd()) {
            return null;
        }

        return static::where('name', $name)->first();
    }

    /**
     * Get the url for this site
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return $this->name.'.'.setting('tld');
    }

    /**
     * Build the files for this site (e.g. nginx conf)
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function buildFiles()
    {
        app(SiteConfBuilder::class)->build($this);
    }

    /**
     * Secure the site. Build certs.
     */
    public function secure()
    {
        (new CertificateBuilder(storage_path('ssl')))->build($this->url);

        $this->update(['secure' => true]);

        Artisan::call('make-files');
    }

    /**
     * Unsecure this site
     */
    public function unsecure()
    {
        (new CertificateBuilder(storage_path('ssl')))->destroy($this->url);

        $this->update(['secure' => false]);

        Artisan::call('make-files');
    }

    /**
     * Remove this site and associated files
     *
     * @throws \Exception
     */
    public function remove()
    {
        (new CertificateBuilder(storage_path('ssl')))->destroy($this->url);
        app(SiteConfBuilder::class)->destroy($this);

        $this->delete();

        Artisan::call('make-files');
    }

    /**
     * Set the PHP version for the site
     *
     * @param $phpVersionId
     */
    public function setPhpVersion($phpVersionId)
    {
        $this->update(['php_version_id' => $phpVersionId ?: PhpVersion::defaultVersion()->id]);

        Artisan::call('make-files');
    }

    /**
     * Set the nginx type for the site (we have different template configs we can use)
     *
     * @param $type
     */
    public function setNginxType($type)
    {
        $this->update(['nginx_type' => $type ?? 'default']);

        Artisan::call('make-files');
    }

    /**
     * Get the first site based on name, or create a new record
     *
     * @param $name
     * @return mixed
     */
    public static function firstOrCreateForName($name)
    {
        $result = static::where('name', $name)->first();

        if ($result) {
            return $result;
        }

        return static::createForName($name);
    }

    /**
     * Create an new site based on the name
     *
     * @param $name
     * @return mixed
     */
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
