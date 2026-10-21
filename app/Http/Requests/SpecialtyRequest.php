<?php

namespace App\Http\Requests;

use App\Models\Specialty;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SpecialtyRequest extends FormRequest
{
    /** Otorisasi dijalankan SEBELUM validasi: pengguna tak berhak mendapat 403, bukan galat isian. */
    public function authorize(): bool
    {
        $specialty = $this->route("specialty");

        return $specialty
            ? $this->user()->can("update", $specialty)
            : $this->user()->can("create", Specialty::class);
    }

    public function rules(): array
    {
        return [
            "name" => [
                "required", "string", "max:100",
                Rule::unique("specialties", "name")->ignore($this->route("specialty")),
            ],
            "description" => ["nullable", "string", "max:500"],
        ];
    }

    public function attributes(): array
    {
        return ["name" => "nama spesialisasi", "description" => "deskripsi"];
    }
}
