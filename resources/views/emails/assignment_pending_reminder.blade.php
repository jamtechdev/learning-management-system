@extends('emails.layout')

@section('title', 'Pending Assignment Reminder')

@section('content')
    <div style="background-color: #fff4e5; padding: 20px; border: 1px solid #f7c4a1; border-radius: 10px;">
        <p style="color: #e74c3c; font-size: 18px;">Dear {{ $parentName }},</p>

        <p style="font-size: 18px; color: #333;">
            This is a reminder that your child, <strong>{{ $childName }}</strong>, has a pending assignment titled
            "<strong>{{ $assignmentTitle }}</strong>" which is due on
            {{ \Carbon\Carbon::parse($dueDate)->toFormattedDateString() }}.
        </p>

        <p style="color: #e74c3c; font-size: 18px;">
            Please ensure that the assignment is completed as soon as possible.
        </p>

        <p style="font-size: 16px; color: #777;">
            Thank you,<br />
            Learning Management System Team
        </p>
    </div>
@endsection
