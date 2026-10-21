<?php

namespace App\Http\Controllers;

use App\Http\Requests\SpecialtyRequest;
use App\Models\Specialty;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class SpecialtyController extends Controller
{
    public function index(): Response
    {
        Gate::authorize("viewAny", Specialty::class);

        return Inertia::render("specialties/index", [
            "specialties" => Specialty::withCount("doctors")->orderBy("name")->paginate(10),
        ]);
    }

    public function create(): Response
    {
        Gate::authorize("create", Specialty::class);

        return Inertia::render("specialties/create");
    }

    public function store(SpecialtyRequest $request): RedirectResponse
    {
        Specialty::create($request->validated());

        return redirect()->route("specialties.index")
            ->with("sukses", "Spesialisasi berhasil ditambahkan.");
    }

    public function edit(Specialty $specialty): Response
    {
        Gate::authorize("update", $specialty);

        return Inertia::render("specialties/edit", ["specialty" => $specialty]);
    }

    public function update(SpecialtyRequest $request, Specialty $specialty): RedirectResponse
    {
        $specialty->update($request->validated());

        return redirect()->route("specialties.index")
            ->with("sukses", "Perubahan spesialisasi tersimpan.");
    }

    public function destroy(Specialty $specialty): RedirectResponse
    {
        Gate::authorize("delete", $specialty);

        if ($specialty->doctors()->exists()) {
            return back()->withErrors([
                "hapus" => "Spesialisasi masih memiliki dokter. Pindahkan atau hapus dokternya lebih dulu.",
            ]);
        }

        $specialty->delete();

        return redirect()->route("specialties.index")
            ->with("sukses", "Spesialisasi dihapus.");
    }
}
