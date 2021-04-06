<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PhpVersion extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Return the default version.
     *
     * @return mixed
     */
    public static function defaultVersion()
    {
        return static::where('default', true)->first();
    }

    /**
     * Set the default PHP version.
     *
     * @param $id
     */
    public static function setDefaultVersion($id)
    {
        static::where('default', true)->update(['default' => false]);
        static::find($id)->update(['default' => true]);
    }

    /**
     * Return a list of PHP versions and optionally highlight one.
     *
     * @param string|null $highlight
     *
     * @return mixed
     */
    public static function getList($highlight = null)
    {
        return static::pluck('version_number', 'id')
            ->map(function ($version) use ($highlight) {
                return $version.($version == $highlight ? ' (current)' : '');
            })->toArray();
    }

    /**
     * Get a safe version of the version number to use in paths.
     *
     * @return null|string
     */
    public function getSafeAttribute()
    {
        return static::cleanVersionNumber($this->version_number);
    }

    /**
     * Get a major version number.
     *
     * @return null|string
     */
    public function getMajorAttribute()
    {
        return Str::before($this->version_number, '.');
    }

    /**
     * Get a short form version number with no separators.
     *
     * @return null|string
     */
    public function getShortFormAttribute()
    {
        return static::cleanVersionNumber($this->version_number, '');
    }

    /**
     * Get cli container name.
     *
     * @return null|string
     */
    public function getCliNameAttribute()
    {
        return 'php_cli_'.$this->safe;
    }

    /**
     * Get fpm container name.
     *
     * @return null|string
     */
    public function getFpmNameAttribute()
    {
        return 'php_fpm_'.$this->safe;
    }

    /**
     * Find by a user input version number.
     *
     * @param $number
     *
     * @return static|null
     */
    public static function findByDirtyVersionNumber($number)
    {
        return static::where('version_number', static::cleanVersionNumber($number, '.'))->first();
    }

    /**
     * Scope for the active PHP versions for porter - any used in sites plus default.
     *
     * @param $scope
     *
     * @return mixed
     */
    public function scopeActive($scope)
    {
        return $scope->where('default', true)->orWhereIn('id', Site::all()->pluck('php_version_id'));
    }

    /**
     * Clean php version number for file naming.
     *
     * @param $number
     * @param string $separator
     *
     * @return null|string
     */
    public static function cleanVersionNumber($number, $separator = '-')
    {
        return preg_replace('/[^\d]/', $separator, $number);
    }
}
