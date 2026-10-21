<?php

namespace App\Http\Requests;

use App\Models\Appointment;
use App\Models\Room;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/** Admin: konfirmasi / selesaikan / batalkan janji, sambil menetapkan ruangan. */
class AppointmentStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can("manage", $this->route("appointment"));
    }

    public function rules(): array
    {
        return [
            "status" => ["required", Rule::in([
                Appointment::CONFIRMED, Appointment::COMPLETED, Appointment::CANCELLED,
            ])],
            "room_id" => ["nullable", "integer", "exists:rooms,id"],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $appointment = $this->route("appointment");
            $tujuan = $this->input("status");

            if (! in_array($tujuan, Appointment::TRANSITIONS[$appointment->status], true)) {
                $validator->errors()->add("status", "Status tidak dapat diubah dari \"{$appointment->status_label}\" ke status tersebut.");

                return;
            }

            if ($tujuan === Appointment::CONFIRMED) {
                if (! $this->filled("room_id")) {
                    $validator->errors()->add("room_id", "Pilih ruangan sebelum mengonfirmasi janji temu.");

                    return;
                }

                if (Room::whereKey($this->input("room_id"))->value("status") !== Room::AVAILABLE) {
                    $validator->errors()->add("room_id", "Ruangan tersebut sedang tidak tersedia.");
                }
            }
        });
    }
}
