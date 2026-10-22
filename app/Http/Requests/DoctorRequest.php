<?php

namespace App\Http\Requests;

use App\Models\Doctor;
use Illuminate\Foundation\Http\FormRequest;

class DoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        $doctor = $this->route("doctor");

        return $doctor
            ? $this->user()->can("update", $doctor)
            : $this->user()->can("create", Doctor::class);
    }

    public function rules(): array
    {
        return [
            "specialty_id" => ["required", "integer", "exists:specialties,id"],
            "name" => ["required", "string", "max:120"],
            "phone" => ["nullable", "string", "max:20", "regex:/^[0-9+\\-\\s]+$/"],
        ];
    }

    public function attributes(): array
    {
        return ["specialty_id" => "spesialisasi", "name" => "nama dokter", "phone" => "nomor telepon"];
    }
}
