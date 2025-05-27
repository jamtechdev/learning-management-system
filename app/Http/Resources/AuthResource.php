<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AuthResource extends JsonResource
{
    public function toArray(Request $request)
    {
        $avatarPath = $this?->avatar;

        // Check if the file exists in the public disk (storage/app/public)
        $avatarUrl = ($avatarPath && Storage::disk('public')->exists($avatarPath))
            ? asset('storage/' . $avatarPath)
            : asset('images/logo/default-avatar.png'); // fallback to default image in public/images

        $data = [
            'first_name' => $this?->first_name,
            'last_name' => $this?->last_name,
            'email' => $this?->email,
            'student_type' => $this?->student_type,
            'phone' => $this?->phone,
            'address' => $this?->address,
            'avatar' => $avatarUrl,
            'token' => $this->token,
        ];

        if ($this->role === 'child') {
            $data['parent'] = $this->parent ?? null;
        }

        return $data;
    }
}
