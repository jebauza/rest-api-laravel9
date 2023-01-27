<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    /**
     *
     * @OA\Schema(
     *      schema="UserResource",
     *      @OA\Property(property="id", type="integer", example=161),
     *      @OA\Property(property="name", type="string", example="Zeus user"),
     *      @OA\Property(property="email", type="string", example="admin@zeus.vision"),
     *      @OA\Property(property="created_at", type="string", example="2023-01-27T14:42:18.000000Z"),
     *      @OA\Property(property="updated_at", type="string", example="2023-01-27T19:15:23.000000Z"),
     * )
     */
    public function toArray($request)
    {
        // return parent::toArray($request);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->created_at,
            'updated_at' => $this->created_at,
        ];
    }
}
