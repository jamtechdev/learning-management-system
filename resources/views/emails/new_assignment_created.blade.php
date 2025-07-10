@extends('emails.layout')

@section('title', 'New Assignment Created')

@section('content')
    <div style="background-color: #e1f7d5; padding: 20px; border: 1px solid #a0e5a5; border-radius: 10px;">
        <p style="color: #2ecc71; font-size: 18px;">Dear {{ $parentName }},</p>

        <p style="font-size: 18px; color: #333;">
            A new assignment titled "<strong>{{ $assignmentTitle }}</strong>" has been created for your child,
            <strong>{{ $childName }}</strong>. The due date for this assignment is
            {{ \Carbon\Carbon::parse($dueDate)->toFormattedDateString() }}.
        </p>

        <p style="font-size: 18px; color: #333;">
            Please encourage your child to complete the assignment on time.
        </p>

        <p style="font-size: 16px; color: #777;">
            Thank you,<br />
            Learning Management System Team
        </p>
    </div>
@endsection
