<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = ['nombre', 'apellido', 'nacimiento', 'edad'];  

    public static function search($query='') {
        if (!$query) {
            return self::all();
        }
        return self::where('nombre', 'like', "%$query%")
            ->orWhere('apellido', 'like', "%$query%")
            ->orWhere('nacimiento', 'like', "%$query%")
            ->orWhere('edad', 'like', "%$query%")
            ->get();
    }
}
