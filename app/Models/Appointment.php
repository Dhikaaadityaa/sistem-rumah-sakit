<?php

namespace App\Models;

use App\Support\Tanggal;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    public const PENDING = "pending";
    public const CONFIRMED = "confirmed";
    public const COMPLETED = "completed";
    public const CANCELLED = "cancelled";

    public const STATUSES = [self::PENDING, self::CONFIRMED, self::COMPLETED, self::CANCELLED];

    public const LABELS = [
        self::PENDING => "Menunggu",
        self::CONFIRMED => "Dikonfirmasi",
        self::COMPLETED => "Selesai",
        self::CANCELLED => "Dibatalkan",
    ];

    /** Alur status yang sah. Selain ini ditolak oleh Form Request. */
    public const TRANSITIONS = [
        self::PENDING => [self::CONFIRMED, self::CANCELLED],
        self::CONFIRMED => [self::COMPLETED, self::CANCELLED],
        self::COMPLETED => [],
        self::CANCELLED => [],
    ];

    // user_id TIDAK fillable: selalu diisi lewat $user->appointments()->create().
    protected $fillable = [
        "doctor_id",
        "room_id",
        "appointment_date",
        "appointment_time",
        "status",
        "notes",
    ];

    protected $casts = [
        "appointment_date" => "date:Y-m-d",
        "notes" => "encrypted",          // enkripsi DUA ARAH (bisa dibuka dengan APP_KEY)
    ];

    protected $appends = ["tanggal_indonesia", "jam", "status_label"];

    /** Jam praktik: 08:00 sampai 15:30, tiap 30 menit. */
    public static function slots(): array
    {
        $slots = [];
        for ($jam = 8; $jam < 16; $jam++) {
            foreach (["00", "30"] as $menit) {
                $slots[] = sprintf("%02d:%s", $jam, $menit);
            }
        }

        return $slots;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function getTanggalIndonesiaAttribute(): ?string
    {
        return Tanggal::indonesia($this->appointment_date?->format("Y-m-d"));
    }

    public function getJamAttribute(): ?string
    {
        return $this->appointment_time ? substr($this->appointment_time, 0, 5) : null;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::LABELS[$this->status] ?? $this->status;
    }

    /** Penyaringan DATA di kueri: pasien hanya melihat janji miliknya. */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $user->isAdmin() ? $query : $query->where("user_id", $user->id);
    }

    public function scopeFilter(Builder $query, array $f, bool $searchPatient = false): Builder
    {
        return $query
            ->when($f["q"] ?? null, function (Builder $q, string $term) use ($searchPatient) {
                // escape % dan _ agar tidak dianggap pola oleh pengguna
                $like = "%".addcslashes($term, "%_\\")."%";

                $q->where(function (Builder $q) use ($like, $searchPatient) {
                    $q->whereHas("doctor", fn ($d) => $d->where("name", "ilike", $like));
                    if ($searchPatient) {
                        $q->orWhereHas("user", fn ($u) => $u->where("name", "ilike", $like));
                    }
                });
            })
            ->when($f["status"] ?? null, fn (Builder $q, string $s) => $q->where("status", $s))
            ->when($f["specialty_id"] ?? null, fn (Builder $q, $id) => $q->whereHas(
                "doctor", fn ($d) => $d->where("specialty_id", $id)
            ))
            ->when($f["from"] ?? null, fn (Builder $q, string $d) => $q->whereDate("appointment_date", ">=", $d))
            ->when($f["to"] ?? null, fn (Builder $q, string $d) => $q->whereDate("appointment_date", "<=", $d));
    }
}
