<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    protected $fillable = ['group', 'key', 'value', 'type', 'description'];

    // cast value retrieval
    public function getDecodedValueAttribute()
    {
        $v = $this->value;
        // try decode json
        $json = json_decode($v, true);
        if (json_last_error() === JSON_ERROR_NONE) return $json;

        // boolean text
        if ($this->type === 'boolean') {
            return filter_var($v, FILTER_VALIDATE_BOOLEAN);
        }

        if (in_array($this->type, ['integer','int'])) {
            return (int)$v;
        }

        if (in_array($this->type, ['float','double'])) {
            return (float)$v;
        }

        return $v;
    }

    // convenience getter: SystemSetting::get('group.key', $default)
    public static function get(string $key, $default = null)
    {
        // key format: group.key
        $parts = explode('.', $key, 2);
        if (count($parts) !== 2) return $default;
        [$group, $k] = $parts;

        // cache per key for 60 seconds to reduce DB reads
        return Cache::remember("system_setting:{$group}.{$k}", 60, function () use ($group, $k, $default) {
            $s = self::where('group', $group)->where('key', $k)->first();
            return $s ? $s->decoded_value : $default;
        });
    }

    // set helper: SystemSetting::set('group.key', $value, 'type')
    public static function set(string $key, $value, string $type = 'string')
    {
        $parts = explode('.', $key, 2);
        if (count($parts) !== 2) return false;
        [$group, $k] = $parts;

        $v = $value;
        if (is_array($value) || is_object($value)) $v = json_encode($value);
        if (is_bool($value) && $type === 'boolean') $v = $value ? '1' : '0';

        $setting = self::updateOrCreate(
            ['group' => $group, 'key' => $k],
            ['value' => $v, 'type' => $type]
        );

        // clear cache for this key
        \Cache::forget("system_setting:{$group}.{$k}");
        return $setting;
    }
}
