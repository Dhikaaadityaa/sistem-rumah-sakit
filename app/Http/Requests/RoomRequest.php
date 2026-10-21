<?php

namespace App\Http\Requests;

use App\Models\Room;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        $room = $this->route("room");

        return $room
            ? $this->user()->can("update", $room)
            : $this->user()->can("create", Room::class);
    }

    public function rules(): array
    {
        return [
            "room_number" => [
                "required", "string", "max:10",
                Rule::unique("rooms", "room_number")->ignore($this->route("room")),
            ],
            "room_type" => ["required", "string", "max:30"],
            "floor" => ["required", "integer", "min:0", "max:50"],
            "status" => ["required", Rule::in(Room::STATUSES)],
        ];
    }

    public function attributes(): array
    {
        return ["room_number" => "nomor ruangan", "room_type" => "tipe ruangan", "floor" => "lantai"];
    }
}
