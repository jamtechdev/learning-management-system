<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index()
    {
        $assessments = Assessment::with('user')->latest()->paginate(10);
        return view('admin.assignments.index', compact('assessments'));
    }
}
