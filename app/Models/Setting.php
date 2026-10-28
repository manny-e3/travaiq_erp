<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group'];

    protected static function booted()
    {
        static::saved(function ($setting) {
            cache()->forget("setting.{$setting->key}");
        });

        static::deleted(function ($setting) {
            cache()->forget("setting.{$setting->key}");
        });
    }

    public static function get($key, $default = null)
    {
        return cache()->rememberForever("setting.{$key}", function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    public static function currencySymbol()
    {
        return self::get('currency_symbol', '$');
    }

    public static function currencyCode()
    {
        return self::get('currency_code', 'USD');
    }

    public static function format($amount)
    {
        return self::currencySymbol() . number_format((float) $amount, 2);
    }

    public static function loginEnabled()
    {
        return self::get('login_enabled', '1') == '1';
    }
}
