<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuestionLevel;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function dashboard()
    {

        $totalUsers = \App\Models\User::count();
        $parentCount = \App\Models\User::role('parent')->count();
        $childCount = \App\Models\User::role('child')->count();
        $totalQuestions = \App\Models\Question::count();
        $levelCount = \App\Models\QuestionLevel::count(); // Assuming you have a Level model
        $subjectCount = \App\Models\QuestionSubject::count(); // Assuming Subject model

        return view('dashboard', compact(
            'totalUsers',
            'parentCount',
            'childCount',
            'totalQuestions',
            'levelCount',
            'subjectCount'
        ));
    }
}
