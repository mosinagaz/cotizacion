<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dolar extends Model
{
    public function getRouteKeyName()
    {
        return 'fecha';
    }

    protected $fillable = ['fecha', 'precio_compra', 'precio_venta'];
    
    protected $hidden = ['created_at', 'updated_at'];

}
