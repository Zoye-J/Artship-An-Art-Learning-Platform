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
    public function materials()
    {
        return $this->hasMany(CourseMaterial::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'enrollments');
    }

    




}

