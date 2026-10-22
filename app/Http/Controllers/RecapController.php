<?php

namespace App\Http\Controllers;

use App\Services\RecapService;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class RecapController extends Controller
{
    /** Halaman rekap: meringkas janji temu, spesialisasi, dan ruangan sekaligus. */
    public function __invoke(RecapService $recap): Response
    {
        Gate::authorize("view-recap");

        return Inertia::render("recap", $recap->build());
    }
}
