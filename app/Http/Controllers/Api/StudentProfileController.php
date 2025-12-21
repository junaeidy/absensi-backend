<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class StudentProfileController extends Controller
{
    /**
     * Get student profile
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        
        $student = Student::where('user_id', $user->id)
            ->with([
                'parents',
                'studentClasses.class.grade',
                'studentClasses.academicYear',
                'user'
            ])
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student data not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $student
        ]);
    }

    /**
     * Update student profile (limited fields)
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student data not found'
            ], 404);
        }

        // Students can only update specific fields
        $validator = Validator::make($request->all(), [
            'nickname' => 'nullable|max:255',
            'phone' => 'nullable|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $student->update($request->only(['nickname', 'phone', 'email', 'address']));

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $student
        ]);
    }

    /**
     * Upload profile photo
     */
    public function uploadPhoto(Request $request)
    {
        $user = $request->user();
        
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student data not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Delete old photo if exists
        if ($student->photo_url) {
            Storage::disk('public')->delete($student->photo_url);
        }

        // Store new photo
        $path = $request->file('photo')->store('students', 'public');
        
        $student->photo_url = $path;
        $student->save();

        return response()->json([
            'success' => true,
            'message' => 'Photo uploaded successfully',
            'data' => [
                'photo_url' => $path,
                'photo_full_url' => Storage::url($path)
            ]
        ]);
    }

    /**
     * Get student's current class
     */
    public function currentClass(Request $request)
    {
        $user = $request->user();
        
        $student = Student::where('user_id', $user->id)
            ->with(['studentClasses' => function($query) {
                $query->where('status', 'active')
                    ->with(['class.grade', 'academicYear']);
            }])
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student data not found'
            ], 404);
        }

        $currentClass = $student->studentClasses->first();

        if (!$currentClass) {
            return response()->json([
                'success' => false,
                'message' => 'No active class found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $currentClass
        ]);
    }

    /**
     * Get student's class history
     */
    public function classHistory(Request $request)
    {
        $user = $request->user();
        
        $student = Student::where('user_id', $user->id)
            ->with(['studentClasses.class.grade', 'studentClasses.academicYear'])
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student data not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $student->studentClasses
        ]);
    }

    /**
     * Get student's parents
     */
    public function parents(Request $request)
    {
        $user = $request->user();
        
        $student = Student::where('user_id', $user->id)
            ->with('parents')
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student data not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $student->parents
        ]);
    }

    /**
     * Get student dashboard data
     */
    public function dashboard(Request $request)
    {
        $user = $request->user();
        
        $student = Student::where('user_id', $user->id)
            ->with([
                'studentClasses' => function($query) {
                    $query->where('status', 'active')
                        ->with(['class.grade', 'academicYear']);
                }
            ])
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student data not found'
            ], 404);
        }

        $currentClass = $student->studentClasses->first();

        // Prepare dashboard data
        $dashboardData = [
            'student_info' => [
                'id' => $student->id,
                'nis' => $student->nis,
                'nisn' => $student->nisn,
                'name' => $student->name,
                'nickname' => $student->nickname,
                'gender' => $student->gender,
                'photo_url' => $student->photo_url ? Storage::url($student->photo_url) : null,
            ],
            'current_class' => $currentClass ? [
                'class_name' => $currentClass->class->name,
                'grade_name' => $currentClass->class->grade->name,
                'academic_year' => $currentClass->academicYear->name,
            ] : null,
            // Placeholder for future features (FASE 2+)
            'attendance_summary' => [
                'present' => 0,
                'sick' => 0,
                'permission' => 0,
                'absent' => 0,
            ],
            'schedule_today' => [],
            'upcoming_exams' => [],
        ];

        return response()->json([
            'success' => true,
            'data' => $dashboardData
        ]);
    }
}
