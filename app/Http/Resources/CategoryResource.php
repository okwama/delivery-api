<?php

namespace App\Http\Resources;

use App\Models\Products\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Category */
class CategoryResource extends JsonResource
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
            'photo' => $this->photo,
            'mobile_banner' => $this->mobile_banner,
            'image_url' => (isset($this->photo) && !empty($this->photo)) ? config('app.image_url').'/' . $this->photo : '',
            'mobile_banner_url' => (isset($this->mobile_banner) && !empty($this->mobile_banner)) ? config('app.image_url').'/' . $this->mobile_banner : '',
            'url' => $this->url,
            'active' => $this->active,
            'subcategories' => $this->subcategories,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'menu' => $this->menu,
        ];
    }
}
