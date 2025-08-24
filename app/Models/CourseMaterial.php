<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'type',
        'title',
        'file_path',
        'uploaded_by',
    ];

    // Relation to Course
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

}

