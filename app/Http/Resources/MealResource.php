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
            'area' => AreaResource::make($this->whenLoaded('area')),
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'ingredients' => MealIngredientResource::collection($this->whenLoaded('ingredients')),
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
        ];
    }
}
