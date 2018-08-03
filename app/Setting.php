<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $guarded = [];

    public static function updateOrCreate($name, $value)
    {
        $setting = static::where('name', $name)->first();

        if ($setting) {
            return  $setting->update(['value' => $value]);
        }

        return static::create(['name' => $name, 'value' => $value]);
    }
}
