<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    /**
     * Display a listing of students.
     */
    public function index(Request $request)
    {
        $query = Student::query()->with(['parents', 'user']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by gender
        if ($request->has('gender')) {
            $query->where('gender', $request->gender);
        }

        // Search by name, NIS, or NISN
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        // Order by
        $orderBy = $request->get('order_by', 'created_at');
        $orderDir = $request->get('order_dir', 'desc');
        $query->orderBy($orderBy, $orderDir);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $students = $query->paginate($perPage);

        return response()->json($students);
    }

    /**
     * Store a newly created student.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nis' => 'required|unique:students,nis|max:20',
            'nisn' => 'nullable|unique:students,nisn|max:20',
            'name' => 'required|max:255',
            'nickname' => 'nullable|max:255',
            'gender' => 'required|in:L,P',
            'birth_place' => 'nullable|max:255',
            'birth_date' => 'nullable|date',
            'religion' => 'nullable|max:255',
            'address' => 'nullable',
            'phone' => 'nullable|max:20',
            'email' => 'nullable|email|max:255',
            'status' => 'sometimes|in:active,alumni,moved,dropped_out',
            'admission_date' => 'nullable|date',
            'graduation_date' => 'nullable|date',
            'previous_school' => 'nullable|max:255',
            'notes' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $student = Student::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Student created successfully',
            'data' => $student
        ], 201);
    }

    /**
     * Display the specified student.
     */
    public function show(string $id)
    {
        $student = Student::with(['parents', 'user', 'studentClasses.class.grade'])
            ->find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $student
        ]);
    }

    /**
     * Update the specified student.
     */
    public function update(Request $request, string $id)
    {
        $student = Student::find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nis' => 'sometimes|unique:students,nis,'.$id.'|max:20',
            'nisn' => 'nullable|unique:students,nisn,'.$id.'|max:20',
            'name' => 'sometimes|max:255',
            'nickname' => 'nullable|max:255',
            'gender' => 'sometimes|in:L,P',
            'birth_place' => 'nullable|max:255',
            'birth_date' => 'nullable|date',
            'religion' => 'nullable|max:255',
            'address' => 'nullable',
            'phone' => 'nullable|max:20',
            'email' => 'nullable|email|max:255',
            'status' => 'sometimes|in:active,alumni,moved,dropped_out',
            'admission_date' => 'nullable|date',
            'graduation_date' => 'nullable|date',
            'previous_school' => 'nullable|max:255',
            'notes' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $student->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Student updated successfully',
            'data' => $student
        ]);
    }

    /**
     * Remove the specified student.
     */
    public function destroy(string $id)
    {
        $student = Student::find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        }

        $student->delete();

        return response()->json([
            'success' => true,
            'message' => 'Student deleted successfully'
        ]);
    }

    /**
     * Get student's parents.
     */
    public function parents(string $id)
    {
        $student = Student::with('parents')->find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'student' => $student->only(['id', 'nis', 'name']),
                'parents' => $student->parents
            ]
        ]);
    }

    /**
     * Get student's classes.
     */
    public function classes(string $id)
    {
        $student = Student::with(['studentClasses.class.grade', 'studentClasses.academicYear'])
            ->find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'student' => $student->only(['id', 'nis', 'name']),
                'classes' => $student->studentClasses
            ]
        ]);
    }
}
