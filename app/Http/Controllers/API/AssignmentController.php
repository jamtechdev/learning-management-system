<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assignment;
use App\Http\Resources\AssignmentResource;

class AssignmentController extends Controller
{
    public function index()
    {
        $assignments = Assignment::all();
        return AssignmentResource::collection($assignments);
    }

    public function show($id)
    {
        $assignment = Assignment::find($id);
        if ($assignment) {
            return new AssignmentResource($assignment);
        }
        return response()->json([
            'message' => 'Assignment not found'
        ], 404);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'due_date' => 'required',
            'is_recurring' => 'boolean',
            'recurrence_rule' => 'nullable',
            'user_id' => 'required|exists:users,id',
        ]);
        $validated['recurrence_rule'] = $validated['recurrence_rule'] ?? null;

        $assignment = Assignment::create($validated);
        
        return new AssignmentResource($assignment);
    }

    public function update(Request $request, $id)
    {
        $assignment=Assignment::find($id);

        if (!$assignment) {
            return response()->json(['message' => 'Assignment not found'], 404);
        }

        $validated = $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'due_date' => 'required|date',
            'is_recurring' => 'boolean',
            'recurrence_rule' => 'nullable|json',
        ]);
        $assignment->update($validated);
        return new AssignmentResource($assignment);
    }

    public function destroy($id)
    {
        $assignment=Assignment::find($id);

        if (!$assignment){
            return response()->json(['message' => 'Assignment not found'], 404);
        }

        $assignment->delete();
        return response()->json(['message' => 'Assignment deleted successfully']);
    }
}
