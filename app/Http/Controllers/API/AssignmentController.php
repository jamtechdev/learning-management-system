<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assignment;
use App\Http\Resources\AssignmentResource;
use App\Traits\ApiResponseTrait;

class AssignmentController extends Controller
{
    use ApiResponseTrait;
    public function index()
    {
        $assignments = Assignment::all();
        return $this->successHandler(AssignmentResource::collection($assignments));
    }

    public function show($id)
    {
        $assignment = Assignment::find($id);
        if ($assignment) {
            return $this->successHandler(new AssignmentResource($assignment));
        }
        return $this->notFoundHandler('Assignment not found');
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

        return $this->successHandler(new AssignmentResource($assignment));
    }

    public function update(Request $request, $id)
    {
        $assignment = Assignment::find($id);

        if (!$assignment) {
            return $this->notFoundHandler('Assignment not found');
        }

        $validated = $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'due_date' => 'required|date',
            'is_recurring' => 'boolean',
            'recurrence_rule' => 'nullable|json',
        ]);
        $assignment->update($validated);
        return $this->successHandler(new AssignmentResource($assignment));
    }

    public function destroy($id)
    {
        $assignment = Assignment::find($id);

        if (!$assignment) {
            return $this->notFoundHandler('Assignment not found');
        }

        $assignment->delete();
        return $this->successHandler(null, 200, 'Assignment deleted successfully');
    }
}
