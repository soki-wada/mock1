<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'condition_id',
        'name',
        'price',
        'description',
        'image',
        'brand',
        'is_purchased'
    ];


    public function condition()
    {
        return $this->belongsTo(Condition::class);
    }

    public function comments(){
        return $this->hasMany(Comment::class);
    }

    public function categories(){
        return $this->belongsToMany(Category::class);
    }

    public function favorites(){
        return $this->hasMany(Favorite::class);
    }

    public function purchase(){
        return $this->hasOne(Purchase::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function scopeKeywordSearch($query, $keyword)
    {
        if (!empty($keyword)) {
            $query->where('name', 'like', '%' . $keyword . '%');
        }

        return $query;
    }
}
