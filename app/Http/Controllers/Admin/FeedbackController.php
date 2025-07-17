<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\FeedbackDataTable;
use App\Models\Feedback;
use App\Models\Question;

class FeedbackController extends Controller
{
    public function index(FeedbackDataTable $dataTable)
    {
        return $dataTable->render('admin.feedback.index');
    }
}
