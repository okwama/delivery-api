<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Rating */
class RatingResource extends JsonResource {
	/**
	 * @param \Illuminate\Http\Request $request
	 * @return array
	 */
	public function toArray($request) {
		return [
			'_id' => $this->_id,
			'stars' => $this->stars,
			'review' => $this->review,
			'status' => $this->status,
			'created_at' => Carbon::parse($this->created_at)->format('M d Y'),
			'productId' => $this->productId,
			'name' => $this->name,
			'email' => $this->email,
			'phone' => $this->phone,
			'product' => $this->product,
		];
	}
}
