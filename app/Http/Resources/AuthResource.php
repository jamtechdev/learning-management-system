<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    public function toArray(Request $request)
    {
        return [
            'first_name' => $this?->first_name,
            'last_name' => $this?->last_name,
            'email' => $this?->email,
            'student_type' => $this?->student_type,
            'phone' => $this?->phone,
            'address' => $this?->address,
            'avatar' => $this?->avatar,
            'token' => $this->token
        ];
    }
}
