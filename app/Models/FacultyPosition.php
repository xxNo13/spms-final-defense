<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacultyPosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'position_name',
        'target_per_function'
    ];

    public function user() {
        return $this->hasOne(User::class);
    }
}
