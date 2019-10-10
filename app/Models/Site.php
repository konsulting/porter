<?php

namespace App\Models;

use App\Porter;
use App\PorterLibrary;
use App\Support\Contracts\Cli;
use App\Support\Nginx\SiteConfBuilder;
use App\Support\Ssl\CertificateBuilder;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $guarded = [];
    protected $casts = ['secure' => 'boolean'];

    /**
     * PHP Version.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function php_version()
    {
        return $this->belongsTo(PhpVersion::class);
    }

    /**
     * Resolve the site from the current working directory.
     *
     * @param string|null $path
     *
     * @return Site|null
     */
    public static function resolveFromPathOrCurrentWorkingDirectory($path = null)
    {
        $name = static::nameFromPath($path ?: app(Cli::class)->currentWorkingDirectory());

        if (!$name) {
            return;
        }

        return static::where('name', $name)->first();
    }

    /**
     * Resolve the site from the current working directory
     * Fail if not found.
     *
     * @param string|null $path
     *
     * @throws \Exception
     *
     * @return Site|null
     */
    public static function resolveFromPathOrCurrentWorkingDirectoryOrFail($path = null)
    {
        $site = static::resolveFromPathOrCurrentWorkingDirectory($path);

        if (!$site) {
            throw new \Exception('Site not found.');
        }

        return $site;
    }

    /**
     * Get the url for this site.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return $this->name.'.'.setting('domain');
    }

    /**
     * Get the scheme for this site.
     *
     * @return string
     */
    public function getSchemeAttribute()
    {
        return ($this->secure ? 'https' : 'http').'://';
    }

    /**
     * Return the path for the NGiNX config file.
     *
     * @return string
     */
    public function getNginxConfPathAttribute()
    {
        return $this->getPorterLibrary()->configPath()."/nginx/conf.d/{$this->name}.conf";
    }

    /**
     * Return the full NGiNX template to use.
     *
     * @return string
     */
    public function getNginxConfTemplateAttribute()
    {
        $type = $this->nginx_conf ?? 'default';

        return "nginx.{$type}.domain".(($this->secure ?? false) ? '_secure' : '');
    }

    /**
     * Build the files for this site (e.g. nginx conf).
     *
     * @throws \Throwable
     */
    public function buildFiles()
    {
        $this->getSiteConfigBuilder()->build($this);
    }

    /**
     * Destroy the files for this site (e.g. NGiNX conf).
     */
    public function destroyFiles()
    {
        $this->getSiteConfigBuilder()->destroy($this);
    }

    /**
     * Secure the site. Build certs.
     */
    public function secure()
    {
        $this->buildCertificate();

        $this->update(['secure' => true]);

        $this->buildFiles();

        $this->getPorter()->restartServing();
    }

    /**
     * Unsecure this site.
     */
    public function unsecure()
    {
        $this->destroyCertificate();

        $this->update(['secure' => false]);

        $this->buildFiles();

        $this->getPorter()->restartServing();
    }

    /**
     * Remove this site and associated files.
     *
     * @throws \Exception
     * @throws \Throwable
     */
    public function remove()
    {
        $this->destroyCertificate();

        $this->getSiteConfigBuilder()->destroy($this);

        $this->delete();

        $this->getPorter()->restartServing();
    }

    /**
     * Set the PHP version for the site.
     *
     * @param int|null $phpVersionId
     *
     * @throws \Throwable
     */
    public function setPhpVersion($phpVersionId = null)
    {
        $this->update(['php_version_id' => $phpVersionId ?: PhpVersion::defaultVersion()->id]);

        // The php_version relation needs to be refreshed, so get a whole new representation of this
        $this->refresh();

        $this->buildFiles();

        $this->getPorter()->restartServing();
    }

    /**
     * Set the nginx type for the site (we have different template configs we can use).
     *
     * @param $type
     *
     * @throws \Throwable
     */
    public function setNginxType($type)
    {
        $this->update(['nginx_conf' => $type ?? 'default']);

        $this->buildFiles();

        $this->getPorter()->restartServing();
    }

    /**
     * Get the first site based on name, or create a new record.
     *
     *
     * @param $name
     *
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
     * Create an new site based on the name.
     *
     * @param $name
     *
     * @return mixed
     */
    public static function createForName($name)
    {
        return static::create([
            'name'           => $name,
            'nginx_conf'     => 'default',
            'php_version_id' => PhpVersion::defaultVersion()->id,
            'secure'         => false,
        ]);
    }

    /**
     * Get Certificate builder.
     *
     * @return CertificateBuilder
     */
    protected function getCertificateBuilder()
    {
        return app(CertificateBuilder::class);
    }

    /**
     * Get Site Config Builder.
     *
     * @return \App\Support\Nginx\SiteConfBuilder
     */
    protected function getSiteConfigBuilder()
    {
        return app(SiteConfBuilder::class);
    }

    /**
     * Get Porter.
     *
     * @return Porter
     */
    protected function getPorter()
    {
        return app(Porter::class);
    }

    /**
     * Get PorterLibrary.
     *
     * @return PorterLibrary
     */
    protected function getPorterLibrary()
    {
        return app(PorterLibrary::class);
    }

    /**
     * Build Certificate for site.
     */
    public function buildCertificate()
    {
        $this->getCertificateBuilder()->build($this->url);
    }

    /**
     * Destroy Certificate for site.
     */
    public function destroyCertificate()
    {
        $this->getCertificateBuilder()->destroy($this->url);
    }

    /**
     * Return a site directory name from a path, after checking it is within the
     * home directory.
     *
     * @param $path
     *
     * @return null|string
     */
    public static function nameFromPath($path)
    {
        $home = setting('home');

        if (strpos($path, DIRECTORY_SEPARATOR) === false) {
            return $path;
        }

        if (strpos($path, $home) !== 0) {
            return;
        }

        $path = trim(str_after($path, $home), DIRECTORY_SEPARATOR);

        return str_before($path, DIRECTORY_SEPARATOR);
    }
}
