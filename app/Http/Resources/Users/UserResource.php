<?php

namespace App\Http\Resources\Users;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
class UserResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            '_id' => $this->_id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'location' => $this->location,
            'address' => $this->address,
            'role' => $this->role,
            'created_at' => $this->created_at,
        ];
    }
}
