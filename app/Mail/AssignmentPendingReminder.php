<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AssignmentPendingReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $assignment;
    public $child;
    public $parent;

    /**
     * Create a new message instance.
     *
     * @param  mixed  $assignment
     * @param  mixed  $child
     * @param  mixed  $parent
     * @return void
     */
    public function __construct($assignment, $child, $parent)
    {
        $this->assignment = $assignment;
        $this->child = $child;
        $this->parent = $parent;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $childFullName = trim($this->child->first_name . ' ' . ($this->child->last_name ?? ''));
        $parentFullName = trim($this->parent->first_name . ' ' . ($this->parent->last_name ?? ''));

        return $this->subject('Pending Assignment Reminder')
                    ->view('emails.assignment_pending_reminder')
                    ->with([
                        'assignmentTitle' => $this->assignment->title,
                        'dueDate' => $this->assignment->due_date,
                        'childName' => $childFullName,
                        'parentName' => $parentFullName,
                    ]);
    }
}
