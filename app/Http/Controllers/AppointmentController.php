<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentRequest;
use App\Http\Requests\AppointmentStatusRequest;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Room;
use App\Models\Specialty;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class AppointmentController extends Controller
{
    /** Alur 1: pencarian bertingkat (nama + status + spesialisasi + rentang tanggal). */
    public function index(Request $request): Response
    {
        Gate::authorize("viewAny", Appointment::class);

        $user = $request->user();

        $filters = $request->validate([
            "q" => ["nullable", "string", "max:100"],
            "status" => ["nullable", Rule::in(Appointment::STATUSES)],
            "specialty_id" => ["nullable", "integer"],
            "from" => ["nullable", "date_format:Y-m-d"],
            "to" => ["nullable", "date_format:Y-m-d", "after_or_equal:from"],
        ]);

        $appointments = Appointment::with(["doctor.specialty", "user:id,name", "room"])
            ->visibleTo($user)                       // pasien hanya melihat miliknya
            ->filter($filters, $user->isAdmin())
            ->orderByDesc("appointment_date")
            ->orderBy("appointment_time")
            ->paginate(10)
            ->withQueryString()
            ->through(function (Appointment $a) use ($user) {
                $a->setAttribute("can", [
                    "lihat" => $user->can("view", $a),
                    "ubah" => $user->can("update", $a),
                    "batal" => $user->can("cancel", $a),
                    "kelola" => $user->can("manage", $a),
                    "hapus" => $user->can("delete", $a),
                ]);

                return $a;
            });

        return Inertia::render("appointments/index", [
            "appointments" => $appointments,
            "filters" => $filters,
            "statuses" => Appointment::LABELS,
            "specialties" => Specialty::orderBy("name")->get(["id", "name"]),
            "can" => ["tambah" => $user->can("create", Appointment::class)],
        ]);
    }

    public function show(Request $request, Appointment $appointment): Response
    {
        Gate::authorize("view", $appointment);

        $user = $request->user();

        return Inertia::render("appointments/show", [
            "appointment" => $appointment->load(["doctor.specialty", "user:id,name,email", "room"]),
            "rooms" => $user->isAdmin()
                ? Room::where("status", Room::AVAILABLE)->orderBy("room_number")->get(["id", "room_number", "room_type", "floor"])
                : [],
            "can" => [
                "ubah" => $user->can("update", $appointment),
                "batal" => $user->can("cancel", $appointment),
                "kelola" => $user->can("manage", $appointment),
                "hapus" => $user->can("delete", $appointment),
            ],
            "transitions" => Appointment::TRANSITIONS[$appointment->status],
            "statuses" => Appointment::LABELS,
        ]);
    }

    public function create(): Response
    {
        Gate::authorize("create", Appointment::class);

        return Inertia::render("appointments/create", $this->formOptions());
    }

    /** Alur 2: pengajuan dengan pemeriksaan jadwal bentrok. */
    public function store(AppointmentRequest $request): RedirectResponse
    {
        try {
            // user_id diisi lewat relasi, bukan dari isian formulir.
            $request->user()->appointments()->create($request->validated());
        } catch (UniqueConstraintViolationException) {
            return back()->withErrors([
                "appointment_time" => "Jadwal tersebut baru saja diambil pasien lain. Pilih jam lain.",
            ])->withInput();
        }

        return redirect()->route("appointments.index")
            ->with("sukses", "Janji temu berhasil diajukan dan menunggu konfirmasi.");
    }

    public function edit(Appointment $appointment): Response
    {
        Gate::authorize("update", $appointment);

        return Inertia::render("appointments/edit", [
            "appointment" => $appointment,
            ...$this->formOptions(),
        ]);
    }

    public function update(AppointmentRequest $request, Appointment $appointment): RedirectResponse
    {
        try {
            $appointment->update($request->validated());
        } catch (UniqueConstraintViolationException) {
            return back()->withErrors([
                "appointment_time" => "Jadwal tersebut baru saja diambil pasien lain. Pilih jam lain.",
            ]);
        }

        return redirect()->route("appointments.show", $appointment)
            ->with("sukses", "Perubahan janji temu tersimpan.");
    }

    public function destroy(Appointment $appointment): RedirectResponse
    {
        Gate::authorize("delete", $appointment);

        $appointment->delete();

        return redirect()->route("appointments.index")->with("sukses", "Janji temu dihapus.");
    }

    /** Pasien (pemilik) atau admin membatalkan. */
    public function cancel(Appointment $appointment): RedirectResponse
    {
        Gate::authorize("cancel", $appointment);

        $appointment->update(["status" => Appointment::CANCELLED]);

        return back()->with("sukses", "Janji temu dibatalkan.");
    }

    /** Admin: alur persetujuan (pending -> confirmed -> completed) + penetapan ruangan. */
    public function updateStatus(AppointmentStatusRequest $request, Appointment $appointment): RedirectResponse
    {
        $data = $request->validated();

        // Ruangan hanya bermakna saat dikonfirmasi; saat batal/selesai biarkan apa adanya.
        if ($data["status"] !== Appointment::CONFIRMED) {
            unset($data["room_id"]);
        }

        $appointment->update($data);

        return back()->with("sukses", "Status janji temu diperbarui.");
    }

    private function formOptions(): array
    {
        return [
            "doctors" => Doctor::with("specialty:id,name")->orderBy("name")->get(["id", "name", "specialty_id"]),
            "slots" => Appointment::slots(),
        ];
    }
}
