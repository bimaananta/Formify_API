<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Forms extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }

    public function allowed_domains(){
        return $this->hasMany(AllowedDomains::class, 'form_id', 'id');
    }

    public function questions(){
        return $this->hasMany(Questions::class, 'form_id', 'id');
    }
}
