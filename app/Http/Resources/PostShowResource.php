<?php

namespace App\Http\Resources;

use App\Http\Resources\CommentResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PostShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'username' => $this->user->username,
            'slug' => $this->slug,
            'url' => $this->url,
            'owner' => auth()->id() == $this->user_id ? true : false,
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
            'votes' => $this->votes,
            'postVotes' => $this->whenLoaded('postVotes')
        ];
    }
}
