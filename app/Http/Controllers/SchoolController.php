<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    //show
    public function show($id)
    {
        $school = School::find(1);
        return view('pages.school.show', compact('school'));
    }

    //edit
    public function edit($id)
    {
        $school = School::find($id);
        return view('pages.school.edit', compact('school'));
    }

    //update
    public function update(Request $request, School $school)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'address' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'radius_km' => 'required',
            'time_in' => 'required',
            'time_out' => 'required',
            'attendance_type' => 'required',
        ]);

        $school->update([
            'name' => $request->name,
            'email' => $request->email,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius_km' => $request->radius_km,
            'time_in' => $request->time_in,
            'time_out' => $request->time_out,
            'attendance_type' => $request->attendance_type,
        ]);

        return redirect()->route('school.show', $school->id)->with('success', 'Sekolah berhasil diperbarui.');
    }
}
