<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CourseMaterial;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CourseMaterialController extends Controller
{
    // Show all materials for a course
    // Show all materials for a course
    public function index($course_id)
    {
        $course = Course::findOrFail($course_id);
    
    // Use paginate() instead of get() to get a LengthAwarePaginator instance
        $materials = CourseMaterial::where('course_id', $course_id)
                              ->latest()
                              ->paginate(10); // 10 items per page
    
        return view('course_materials.index', compact('course', 'materials'));
    }

    // Show create form (admin only)
    public function create($course_id)
    {
        $course = Course::findOrFail($course_id);
        return view('course_materials.create', compact('course'));
    }

    // Store new material (admin only) - UPDATED VERSION
    public function store(Request $request, $course_id)
    {
        \Log::debug('=== UPLOAD ATTEMPT START ===');
        \Log::debug('Has file: ' . ($request->hasFile('file') ? 'YES' : 'NO'));
    
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            \Log::debug('File name: ' . $file->getClientOriginalName());
            \Log::debug('File size: ' . $file->getSize());
            \Log::debug('File mime: ' . $file->getMimeType());
        }
    
        \Log::debug('All form data: ', $request->all());
    
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'type' => 'required|in:video,pdf',
                'file' => 'required|file|mimes:mp4,avi,mov,pdf|max:51200'
            ]);

            $path = $request->file('file')->store('course_materials', 'public');
        
            \Log::debug('File stored at: ' . $path);
        
            CourseMaterial::create([
                'course_id' => $course_id,
                'type' => $request->type,
                'title' => $request->title,
                'file_path' => $path,
                'uploaded_by' => Auth::id(),
            ]);
        
            \Log::debug('=== UPLOAD SUCCESS ===');
            return back()->with('success', 'Material uploaded successfully!');
        
        } catch (\Exception $e) {
            \Log::error('Upload error: ' . $e->getMessage());
            \Log::error('File: ' . $e->getFile());
            \Log::error('Line: ' . $e->getLine());
            return back()->with('error', 'Upload failed: ' . $e->getMessage());
        }
    }

    // Delete material (admin only)
    public function destroy($material_id)
    {
        try {
        // Find the material
            $material = CourseMaterial::findOrFail($material_id);
        
        // Store course_id for redirect before deletion
            $course_id = $material->course_id;
        
            \Log::debug('=== DELETE ATTEMPT START ===');
            \Log::debug('Material ID: ' . $material_id);
            \Log::debug('File path: ' . $material->file_path);
            \Log::debug('Material type: ' . $material->type);
        
        // Delete the file from storage if it exists
            if ($material->file_path) {
                if (Storage::disk('public')->exists($material->file_path)) {
                    Storage::disk('public')->delete($material->file_path);
                    \Log::debug('File deleted from storage: ' . $material->file_path);
                } else {
                    \Log::debug('File not found in storage: ' . $material->file_path);
                }
            }
        
        // Delete the database record
            $material->delete();
            \Log::debug('Database record deleted');
        
            \Log::debug('=== DELETE SUCCESS ===');
        
            return redirect()->route('courses.materials.index', $course_id)
                            ->with('success', 'Material deleted successfully!');
        
        } catch (\Exception $e) {
            \Log::error('Delete error: ' . $e->getMessage());
            \Log::error('File: ' . $e->getFile());
            \Log::error('Line: ' . $e->getLine());
        
            return redirect()->back()->with('error', 'Error deleting material: ' . $e->getMessage());
        }
}
}