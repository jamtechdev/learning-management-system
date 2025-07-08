<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionLevel;
use Illuminate\Http\Request;
use App\DataTables\SubscriptionPlanDataTable;
use App\Models\SubscriptionPlan;
use App\Models\QuestionSubject;
use Symfony\Component\CssSelector\Node\FunctionNode;

class SubscriptionController extends Controller
{


    public function plans(SubscriptionPlanDataTable $dataTable)
    {
        return $dataTable->render('admin.subscription.index');
    }


    public function create()
    {
        $subjects = QuestionSubject::all();
        return view('admin.subscription.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'description' => 'nullable|string',

        ]);

        $plan = SubscriptionPlan::create($validated);


        return redirect()->route('admin.subscriptions.index')->with('success', 'Subscription plan created successfully.');
    }

    public function edit(SubscriptionPlan $plan)
    {
        $subjects = QuestionSubject::all();
        return view('admin.subscription.edit', compact('plan', 'subjects'));
    }

    public function update(Request $request, SubscriptionPlan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'description' => 'nullable|string',

        ]);

        $plan->update($validated);

        return redirect()->route('admin.subscriptions.index')->with('success', 'Subscription plan updated successfully.');
    }

    public function destroy(SubscriptionPlan $plan)
    {
        $plan->delete();

        return redirect()->route('admin.subscriptions.index')->with('success', 'Subscription plan deleted successfully.');
    }

    public function showAssignSubjectsForm(SubscriptionPlan $plan)
    {
        $subjects = QuestionSubject::with('level')->get();
        $levels = QuestionLevel::all();

        return view('admin.subscription.assign-subjects', compact('plan', 'subjects', 'levels'));
    }


    public function assignSubjects(Request $request, SubscriptionPlan $plan)
    {
        $validated = $request->validate([
            'subjects' => 'array',
            'subjects.*' => 'exists:question_subjects,id',
        ]);

        if (isset($validated['subjects'])) {
            $plan->subjects()->sync($validated['subjects']);
        } else {
            $plan->subjects()->detach();
        }

        return redirect()->route('admin.subscriptions.index')->with('success', 'Subjects assigned successfully.');
    }
}
