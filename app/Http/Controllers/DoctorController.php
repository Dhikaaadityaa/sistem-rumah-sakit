<?php

namespace App\Http\Controllers;

use App\Http\Requests\DoctorRequest;
use App\Models\Doctor;
use App\Models\Specialty;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class DoctorController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize("viewAny", Doctor::class);

        $filters = $request->validate([
            "q" => ["nullable", "string", "max:100"],
            "specialty_id" => ["nullable", "integer"],
        ]);

        $doctors = Doctor::with("specialty")
            ->when($filters["q"] ?? null, fn ($q, $term) => $q->where(
                "name", "ilike", "%".addcslashes($term, "%_\\")."%"
            ))
            ->when($filters["specialty_id"] ?? null, fn ($q, $id) => $q->where("specialty_id", $id))
            ->orderBy("name")
            ->paginate(10)
            ->withQueryString()
            ->through(function (Doctor $doctor) use ($request) {
                $doctor->setAttribute("can", [
                    "ubah" => $request->user()->can("update", $doctor),
                    "hapus" => $request->user()->can("delete", $doctor),
                ]);

                return $doctor;
            });

        return Inertia::render("doctors/index", [
            "doctors" => $doctors,
            "specialties" => Specialty::orderBy("name")->get(["id", "name"]),
            "filters" => $filters,
            "can" => ["tambah" => $request->user()->can("create", Doctor::class)],
        ]);
    }

    public function show(Doctor $doctor): Response
    {
        Gate::authorize("view", $doctor);

        return Inertia::render("doctors/show", ["doctor" => $doctor->load("specialty")]);
    }

    public function create(): Response
    {
        Gate::authorize("create", Doctor::class);

        return Inertia::render("doctors/create", [
            "specialties" => Specialty::orderBy("name")->get(["id", "name"]),
        ]);
    }

    public function store(DoctorRequest $request): RedirectResponse
    {
        Doctor::create($request->validated());

        return redirect()->route("doctors.index")->with("sukses", "Dokter berhasil ditambahkan.");
    }

    public function edit(Doctor $doctor): Response
    {
        Gate::authorize("update", $doctor);

        return Inertia::render("doctors/edit", [
            "doctor" => $doctor,
            "specialties" => Specialty::orderBy("name")->get(["id", "name"]),
        ]);
    }

    public function update(DoctorRequest $request, Doctor $doctor): RedirectResponse
    {
        $doctor->update($request->validated());

        return redirect()->route("doctors.index")->with("sukses", "Data dokter diperbarui.");
    }

    public function destroy(Doctor $doctor): RedirectResponse
    {
        Gate::authorize("delete", $doctor);

        if ($doctor->appointments()->exists()) {
            return back()->withErrors([
                "hapus" => "Dokter memiliki riwayat janji temu sehingga tidak dapat dihapus.",
            ]);
        }

        $doctor->delete();

        return redirect()->route("doctors.index")->with("sukses", "Dokter dihapus.");
    }
}
