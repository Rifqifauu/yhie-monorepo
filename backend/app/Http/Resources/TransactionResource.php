<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "reference_id" => $this->reference_id,
            "amount" => $this->amount,
            "payment_status" => $this->payment_status,
            "payment_method" => $this->payment_method,
            "payment_url" => $this->payment_url,
            "transaction_receipt" => $this->transaction_receipt,

            // whenLoaded mencegah error N+1 query jika relasi tidak dipanggil
            "program_registration" => $this->whenLoaded('programRegistration', function () {
                $program = $this->programRegistration->program ?? null;

                return [
                    "full_name" => $this->programRegistration->full_name,
                    "program" => $program ? [
                        "title_id" => $program->title_id,
                        "title_en" => $program->title_en,
                    ] : null,
                ];
            }),
        ];
    }
}
