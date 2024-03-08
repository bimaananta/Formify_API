<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answers extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $hidden = [
        "id",
        "created_at",
        "updated_at",
        "questions_id",
        "responses_id"
    ];

    public function questions(){
        return $this->belongsTo(Questions::class, 'questions_id', 'id');
    }
}
