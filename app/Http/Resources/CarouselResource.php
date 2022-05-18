<?php

namespace App\Http\Resources;

use App\Models\Carousel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Carousel */
class CarouselResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            '_id' => $this->_id,
            'image' =>$this->image,
            'mobile_image' =>$this->mobile_image,
            'image_url' => (isset($this->image) && !empty($this->image)) ? config('app.image_url').'/carousel/' . $this->image : '',
            'mobile_image_url' => (isset($this->mobile_image) && !empty($this->mobile_image)) ? config('app.image_url').'/carousel/' . $this->mobile_image : '',
            'caption' => $this->caption,
            'details' => $this->details,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
