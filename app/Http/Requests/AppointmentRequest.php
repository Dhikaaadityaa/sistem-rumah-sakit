<?php

namespace App\Http\Requests;

use App\Models\Appointment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/** Dipakai untuk store DAN update janji temu oleh pasien. */
class AppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $appointment = $this->route("appointment");

        return $appointment
            ? $this->user()->can("update", $appointment)
            : $this->user()->can("create", Appointment::class);
    }

    /**
     * Perhatikan: "status", "room_id", dan "user_id" SENGAJA tidak ada di sini.
     * Pasien tidak boleh menentukan sendiri statusnya, ruangannya, atau pemiliknya.
     */
    public function rules(): array
    {
        return [
            "doctor_id" => ["required", "integer", "exists:doctors,id"],
            "appointment_date" => ["required", "date_format:Y-m-d", "after_or_equal:today"],
            "appointment_time" => ["required", Rule::in(Appointment::slots())],
            "notes" => ["nullable", "string", "max:500"],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $bentrok = Appointment::query()
                ->where("doctor_id", $this->input("doctor_id"))
                ->where("appointment_date", $this->input("appointment_date"))
                ->where("appointment_time", $this->input("appointment_time"))
                ->where("status", "!=", Appointment::CANCELLED)
                ->when($this->route("appointment"), fn ($q, $a) => $q->whereKeyNot($a->getKey()))
                ->exists();

            if ($bentrok) {
                $validator->errors()->add(
                    "appointment_time",
                    "Dokter sudah memiliki janji pada tanggal dan jam tersebut. Pilih jam lain."
                );
            }
        });
    }

    public function attributes(): array
    {
        return [
            "doctor_id" => "dokter",
            "appointment_date" => "tanggal",
            "appointment_time" => "jam",
            "notes" => "keluhan",
        ];
    }

    public function messages(): array
    {
        return [
            "appointment_date.after_or_equal" => "Tanggal tidak boleh sebelum hari ini.",
            "appointment_time.in" => "Jam harus salah satu dari jam praktik (08:00 sampai 15:30).",
        ];
    }
}
