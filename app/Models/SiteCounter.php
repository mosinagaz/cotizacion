<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SiteCounter extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    public static function visits(): int
    {
        $row = static::query()->firstOrCreate(
            ['key' => 'visits'],
            ['value' => 0],
        );

        return (int) $row->value;
    }

    public static function incrementVisits(): int
    {
        static::query()->firstOrCreate(
            ['key' => 'visits'],
            ['value' => 0],
        );

        static::query()->where('key', 'visits')->update([
            'value' => DB::raw('value + 1'),
            'updated_at' => now(),
        ]);

        return static::visits();
    }
}
