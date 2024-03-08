<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllowedDomains extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function form(){
        return $this->belongsTo(Forms::class, 'form_id', 'id');
    }
}
