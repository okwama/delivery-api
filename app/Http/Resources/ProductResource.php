<?php

namespace App\Http\Resources;

use App\Models\Products\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Product */
class ProductResource extends JsonResource
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
            'url' => $this->url,
            'category' => $this->category,
            'brand' => $this->brand,
            'image' => $this->image,
            'image_url'=>(isset($this->image) && !empty($this->image)) ? config('app.image_url').'/' . $this->image : '',
            'features' => $this->features,
            'published' => $this->published,
            'displayCategory' => $this->displayCategory,
            'available' => $this->available,
            'label' => $this->label,
            'images' => $this->images,
            'subcategory' => $this->subcategory,
            'description' => $this->description,
            'meta' => $this->meta,
            'quantities' => $this->quantities,
            'discount' => $this->discount,
            'featured' => $this->featured,
            'tags' => collect($this->tags)->filter(),
            'percentage' => $this->percentage,
            'country' => $this->country,
            'videoLink' => $this->videoLink,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
