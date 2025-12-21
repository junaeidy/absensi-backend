<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class StudentAuthController extends Controller
{
    /**
     * Student login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Find user with role siswa
        $user = User::where('email', $request->email)
            ->where('role', 'siswa')
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials or not a student account'
            ], 401);
        }

        // Get student data
        $student = Student::where('user_id', $user->id)
            ->with(['parents', 'studentClasses.class.grade'])
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student data not found'
            ], 404);
        }

        // Check if student is active
        if ($student->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Student account is not active. Status: ' . $student->status
            ], 403);
        }

        // Create token
        $token = $user->createToken('student-app', ['student'])->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
                'student' => $student
            ]
        ]);
    }

    /**
     * Student logout
     */
    public function logout(Request $request)
    {
        // Revoke current token
        $token = $request->user()->currentAccessToken();
        
        if ($token) {
            $token->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logout successful'
        ]);
    }

    /**
     * Get current authenticated student
     */
    public function me(Request $request)
    {
        $user = $request->user();
        
        $student = Student::where('user_id', $user->id)
            ->with(['parents', 'studentClasses.class.grade', 'studentClasses.academicYear'])
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student data not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'student' => $student
            ]
        ]);
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 401);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully'
        ]);
    }
}
