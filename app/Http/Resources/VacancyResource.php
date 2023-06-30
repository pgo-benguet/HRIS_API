<?php

namespace App\Http\Resources;

use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VacancyResource extends JsonResource
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
            "attributes" => [
                "date_submitted" => (string)$this->date_submitted,
                "date_queued" => (string)$this->date_queued,
                "date_approved" => (string)$this->date_approved,
                "status" => (string)$this->status,
                "office_name" => (string)$this->office_name,
                "department_name" => (string)$this->department_name,
                "title" => (string)$this->title,
                "number" => (string)$this->number,
                "amount" => (string)$this->amount,
                "item_number" => (string)$this->item_number,
                "education" => (string)$this->education,
                "training" => (string)$this->training,
                "experience" => (string)$this->experience,
                "eligibility" => (string)$this->eligibility,
                "competency" => (string)$this->competency,
            ],

        ];
    }
}
