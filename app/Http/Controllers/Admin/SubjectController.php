<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionSubject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{

    public function index()
    {
        // Use paginate directly on the query builder
        $subjects = QuestionSubject::with('level')->paginate(10);

        return view('admin.question.subject.index', compact('subjects'));
    }
}
