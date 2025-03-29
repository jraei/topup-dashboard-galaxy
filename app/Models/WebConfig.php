<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebConfig extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public static function get($key, $default = null)
    {
        $config = self::where('key', $key)->first();
        
        if (!$config) {
            return $default;
        }
        
        return $config->value;
    }

    public static function set($key, $value, $description = null, $type = 'text')
    {
        $config = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'description' => $description,
                'type' => $type
            ]
        );
        
        return $config;
    }
}