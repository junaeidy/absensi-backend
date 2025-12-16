<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\School;

class SchoolController extends Controller
{
    //show
    public function show()
    {
        $school = School::first();
        return response(['school' => $school], 200);
    }
}
