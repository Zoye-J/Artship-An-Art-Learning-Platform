<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;


class Course extends Model
{
 
    protected $fillable = ['title', 'description', 'category', 'thumbnail'];


    use HasFactory;
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function wishlistedBy()
    {
        return $this->hasMany(Wishlist::class);
    }



}

