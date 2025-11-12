<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'target_user_id',
        'rating'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }
}
