<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class StudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $avatarPath = $this?->avatar;

        // Check if the file exists in the public disk (storage/app/public)
        $avatarUrl = ($avatarPath && Storage::disk('public')->exists($avatarPath))
            ? asset("storage/$avatarPath")
            : asset('images/logo/default-avatar.png'); // fallback to default image in public/images

        return [
            'id' => $this?->id,
            'first_name' => $this?->first_name,
            'last_name' => $this?->last_name,
            'email' => $this?->email,
            'student_type' => $this?->student_type,
            'phone' => $this?->phone,
            'address' => $this?->address,
            'avatar' => $avatarUrl,
            'parent_id' => $this?->parent_id,
            'student_level' => $this?->student_level,
            'lock_code' => $this?->lock_code,
            'lock_code_enabled' => $this?->lock_code_enabled
        ];
    }
}
