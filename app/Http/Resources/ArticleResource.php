<?php

namespace App\Http\Resources;

use App\Models\Blog\Article;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Article */
class ArticleResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            '_id' => $this->_id,
            'title' => $this->title,
            'meta' => $this->meta,
            'url' => $this->url,
            'image_url' => $this->image_url(),
            'body' => $this->body,
            'image' => $this->image,
            'tags' => $this->tags,
            'created_at' => Carbon::parse($this->created_at)->format('M d, Y'),
            'updated_at' => $this->updated_at,
        ];
    }

    private function image_url(): string
    {
        if(empty($this->image) || is_null($this->image)){
            return "https://canadiansamaritansforafrica.org/wp-content/uploads/2020/06/placeholder-300x200.png";
        }
        else{
            $img=explode('.',$this->image);
            if(isset($img[1])){
                return config('app.image_url').'/blog/' . $this->image;

            }
            else{
                return config('app.image_url').'/blog/'. $this->image.'.'.'jpeg';
            }

        }

    }
}
