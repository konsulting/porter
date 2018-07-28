<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PhpVersion extends Model
{
    public static function defaultVersion()
    {
        return static::where('default', true)->first();
    }

    public function getSafeAttribute()
    {
        return preg_replace('/[^\d]/', '-', $this->version_number);
    }

    public function getPortAttribute()
    {
        return 9000+$this->id;
    }

    public function scopeActive($scope)
    {
        return $scope->where('default', true)->orWhereIn('id', Site::all()->pluck('php_version_id'));
    }
}
