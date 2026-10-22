<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoomRequest;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class RoomController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize("viewAny", Room::class);

        $filters = $request->validate([
            "status" => ["nullable", Rule::in(Room::STATUSES)],
        ]);

        $rooms = Room::query()
            ->when($filters["status"] ?? null, fn ($q, $s) => $q->where("status", $s))
            ->orderBy("floor")
            ->orderBy("room_number")
            ->paginate(12)
            ->withQueryString()
            ->through(function (Room $room) use ($request) {
                $room->setAttribute("can", [
                    "ubah" => $request->user()->can("update", $room),
                    "hapus" => $request->user()->can("delete", $room),
                ]);

                return $room;
            });

        return Inertia::render("rooms/index", [
            "rooms" => $rooms,
            "filters" => $filters,
            "statuses" => Room::LABELS,
            "can" => ["tambah" => $request->user()->can("create", Room::class)],
        ]);
    }

    public function create(): Response
    {
        Gate::authorize("create", Room::class);

        return Inertia::render("rooms/create", ["statuses" => Room::LABELS]);
    }

    public function store(RoomRequest $request): RedirectResponse
    {
        Room::create($request->validated());

        return redirect()->route("rooms.index")->with("sukses", "Ruangan berhasil ditambahkan.");
    }

    public function edit(Room $room): Response
    {
        Gate::authorize("update", $room);

        return Inertia::render("rooms/edit", ["room" => $room, "statuses" => Room::LABELS]);
    }

    public function update(RoomRequest $request, Room $room): RedirectResponse
    {
        $room->update($request->validated());

        return redirect()->route("rooms.index")->with("sukses", "Data ruangan diperbarui.");
    }

    public function destroy(Room $room): RedirectResponse
    {
        Gate::authorize("delete", $room);

        $room->delete();   // janji temu yang memakai ruangan ini otomatis room_id = null

        return redirect()->route("rooms.index")->with("sukses", "Ruangan dihapus.");
    }
}
