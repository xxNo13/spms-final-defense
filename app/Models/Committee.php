<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Committee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'committee_type',
        'committee_institute',
        'user_id'
    ];

    public function user (){
        return $this->belongsTo(User::class);
    }

    public function institute() {
        return $this->belongsTo(Office::class, 'committee_institute');
    }
}
