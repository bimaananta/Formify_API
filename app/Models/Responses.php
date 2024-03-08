<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Responses extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $hidden = [
        "id",
        "user_id",
        "form_id",
        "created_at",
        "updated_at"
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function answers(){
        return $this->hasMany(Answers::class, 'responses_id', 'id');
    }
}
