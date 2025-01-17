<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DivisionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => (string)$this->id,
            "label" => (string)$this->division_name,
            "attributes" => [
                "division_code" => (string)$this->division_code,
                "division_name" => (string)$this->division_name,
                "office_name" => (string)$this->office_name,
                "division_type" => (string)$this->division_type
            ]

        ];
    }
}
