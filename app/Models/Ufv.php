<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ufv extends Model
{
    public function getRouteKeyName()
    {
        return 'fecha';
    }
    
    protected $fillable = ['fecha', 'valor'];
    
    protected $hidden = ['created_at', 'updated_at'];
}
