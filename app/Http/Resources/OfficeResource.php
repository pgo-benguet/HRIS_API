<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OfficeResource extends JsonResource
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
            "attributes"=>[
                "office_code" => (string)$this->office_code,
                "office_name" => (string)$this->office_name,
                'divisions' => $this->hasManyDivisions->map(function ($divisions) {
                    return [
                        'division_code' => $divisions->division_code,
                        'division_name' => $divisions->division_name,
                    ];
                }),

            ],
            
           
        ];
    }
}
