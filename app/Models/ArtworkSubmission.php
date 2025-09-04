<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArtworkSubmission extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'course_id', 'title', 'description', 'image_path', 'is_featured'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    
    public function likes()
    {
        return $this->hasMany(ArtworkLike::class, 'artwork_id');
    }

    public function isLikedByUser()
    {
        if (!auth()->check()) {
            return false;
        }
        
        return $this->likes()->where('user_id', auth()->id())->exists();
    }
    
}