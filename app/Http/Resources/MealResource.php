<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MealResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'thumbnail_url' => $this->thumbnail_url,
            'tags' => $this->tags,
            'instructions' => $this->instructions,
            'area' => $this->whenLoaded('area', AreaResource::make($this->area)),
            'category' => $this->whenLoaded('category', CategoryResource::make($this->category)),
            'ingredients' => $this->whenLoaded('ingredients', MealIngredientResource::collection($this->ingredients)),
            'comments' => $this->whenLoaded('comments', CommentResource::collection($this->comments)),
        ];
    }
}
