<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PhpVersion extends Model
{
    protected $guarded = [];

    /**
     * Return the default version
     *
     * @return mixed
     */
    public static function defaultVersion()
    {
        return static::where('default', true)->first();
    }

    /**
     * Set the default PHP version
     *
     * @param $id
     */
    public static function setDefaultVersion($id)
    {
        static::where('default', true)->update(['default' => false]);
        static::find($id)->update(['default' => true]);
    }

    /**
     * Get a safe version of the version number to use in paths
     *
     * @return null|string
     */
    public function getSafeAttribute()
    {
        return static::cleanVersionNumber($this->version_number);
    }

    /**
     * Find by a user input version number
     *
     * @param $number
     * @return static|null
     */
    public static function findByDirtyVersionNumber($number)
    {
        return static::where('version_number', static::cleanVersionNumber($number, '.'))->first();
    }

    /**
     * Scope for the active PHP versions for porter - any used in sites plus default
     *
     * @param $scope
     * @return mixed
     */
    public function scopeActive($scope)
    {
        return $scope->where('default', true)->orWhereIn('id', Site::all()->pluck('php_version_id'));
    }


    /**
     * Clean php version number for file naming
     *
     * @param $number
     * @return null|string
     */
    public static function cleanVersionNumber($number, $separator = '-')
    {
        return preg_replace('/[^\d]/', $separator, $number);
    }
}
