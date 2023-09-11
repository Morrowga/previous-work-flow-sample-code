<?php

namespace Src\BlendedConcept\Security\Domain\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthPermissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->role->permissions->pluck('name');
    }
}
