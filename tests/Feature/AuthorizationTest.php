<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Bukti bahwa kewenangan ditegakkan di SERVER, bukan di antarmuka.
 * Setiap tes meniru penyerang yang mengetik alamat langsung (seperti curl).
 */
class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function buatUser(string $peran): User
    {
        $user = User::factory()->create();
        $user->forceFill(["role" => $peran])->save();

        return $user;
    }

    private function buatJanji(User $pasien, string $jam = "09:00"): Appointment
    {
        $spesialisasi = Specialty::firstOrCreate(["name" => "Umum"]);
        $dokter = Doctor::firstOrCreate(
            ["name" => "dr. Uji"],
            ["specialty_id" => $spesialisasi->id]
        );

        return $pasien->appointments()->create([
            "doctor_id" => $dokter->id,
            "appointment_date" => now()->addDay()->format("Y-m-d"),
            "appointment_time" => $jam,
            "notes" => "Keluhan uji",
        ]);
    }

    public function test_tamu_dialihkan_ke_halaman_masuk(): void
    {
        $this->get("/appointments")->assertRedirect("/login");
        $this->get("/recap")->assertRedirect("/login");
    }

    public function test_pasien_tidak_dapat_membuka_halaman_admin(): void
    {
        $pasien = $this->buatUser("patient");

        $this->actingAs($pasien)->get("/recap")->assertForbidden();
        $this->actingAs($pasien)->get("/specialties")->assertForbidden();
        $this->actingAs($pasien)->get("/doctors/create")->assertForbidden();
    }

    public function test_pasien_tidak_dapat_menulis_data_master(): void
    {
        $pasien = $this->buatUser("patient");

        // 403 muncul sebelum validasi (authorize() pada Form Request).
        $this->actingAs($pasien)->post("/specialties", ["name" => "Bedah"])->assertForbidden();
        $this->assertDatabaseMissing("specialties", ["name" => "Bedah"]);
    }

    public function test_pasien_tidak_dapat_melihat_janji_milik_orang_lain(): void
    {
        $pemilik = $this->buatUser("patient");
        $lain = $this->buatUser("patient");
        $janji = $this->buatJanji($pemilik);

        $this->actingAs($lain)->get("/appointments/{$janji->id}")->assertForbidden();
        $this->actingAs($lain)->patch("/appointments/{$janji->id}/cancel")->assertForbidden();
    }

    public function test_pasien_tidak_dapat_menghapus_atau_mengonfirmasi(): void
    {
        $pasien = $this->buatUser("patient");
        $janji = $this->buatJanji($pasien);

        // setara: curl -X DELETE /appointments/{id}
        $this->actingAs($pasien)->delete("/appointments/{$janji->id}")->assertForbidden();
        $this->actingAs($pasien)
            ->patch("/appointments/{$janji->id}/status", ["status" => "confirmed"])
            ->assertForbidden();

        $this->assertDatabaseHas("appointments", ["id" => $janji->id, "status" => "pending"]);
    }

    public function test_pasien_dapat_membatalkan_janji_sendiri(): void
    {
        $pasien = $this->buatUser("patient");
        $janji = $this->buatJanji($pasien);

        $this->actingAs($pasien)->patch("/appointments/{$janji->id}/cancel")->assertRedirect();

        $this->assertDatabaseHas("appointments", ["id" => $janji->id, "status" => "cancelled"]);
    }

    public function test_admin_dapat_menghapus_janji(): void
    {
        $admin = $this->buatUser("admin");
        $janji = $this->buatJanji($this->buatUser("patient"));

        $this->actingAs($admin)->delete("/appointments/{$janji->id}")->assertRedirect();

        $this->assertDatabaseMissing("appointments", ["id" => $janji->id]);
    }

    public function test_jadwal_dokter_yang_bentrok_ditolak(): void
    {
        $a = $this->buatUser("patient");
        $b = $this->buatUser("patient");
        $janji = $this->buatJanji($a, "10:00");

        $this->actingAs($b)->post("/appointments", [
            "doctor_id" => $janji->doctor_id,
            "appointment_date" => $janji->appointment_date->format("Y-m-d"),
            "appointment_time" => "10:00",
        ])->assertSessionHasErrors("appointment_time");

        $this->assertSame(1, Appointment::count());
    }

    public function test_pasien_tidak_dapat_menyelundupkan_status_atau_pemilik(): void
    {
        $pasien = $this->buatUser("patient");
        $lain = $this->buatUser("patient");
        $janji = $this->buatJanji($pasien, "11:00");

        $this->actingAs($pasien)->post("/appointments", [
            "doctor_id" => $janji->doctor_id,
            "appointment_date" => now()->addDays(2)->format("Y-m-d"),
            "appointment_time" => "11:30",
            "status" => "completed",       // harus diabaikan
            "user_id" => $lain->id,        // harus diabaikan
        ])->assertRedirect();

        $baru = Appointment::latest("id")->first();
        $this->assertSame("pending", $baru->status);
        $this->assertSame($pasien->id, $baru->user_id);
    }

    public function test_keluhan_tersimpan_terenkripsi_di_basis_data(): void
    {
        $janji = $this->buatJanji($this->buatUser("patient"));

        $mentah = \DB::table("appointments")->where("id", $janji->id)->value("notes");

        $this->assertNotSame("Keluhan uji", $mentah);          // di basis data: terenkripsi
        $this->assertSame("Keluhan uji", $janji->fresh()->notes); // lewat model: terbuka kembali
    }
}
