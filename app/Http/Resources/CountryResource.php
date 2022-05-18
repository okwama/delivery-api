<?php

namespace App\Http\Resources;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Country */
class CountryResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'dialing_code' => $this->dialing_code,
        ];
    }
}
