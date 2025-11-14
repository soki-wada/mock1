<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    use HasFactory;

    protected $fillable = [
        'selling_user_id',
        'purchasing_user_id',
        'product_id',
        'is_deal'
    ];

    public function sellingUser(){
        return $this->belongsTo(User::class);
    }

    public function purchasingUser(){
        return $this->belongsTo(User::class);
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function chats(){
        return $this->hasMany(Chat::class);
    }

    public function unreadMessages(){
        return $this->hasMany(Chat::class)->where('is_read', false);
    }

    public function evaluations(){
        return $this->hasMany(Evaluation::class);
    }
}
