<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    public const AVAILABLE = "available";
    public const OCCUPIED = "occupied";
    public const MAINTENANCE = "maintenance";

    public const STATUSES = [self::AVAILABLE, self::OCCUPIED, self::MAINTENANCE];

    public const LABELS = [
        self::AVAILABLE => "Tersedia",
        self::OCCUPIED => "Terisi",
        self::MAINTENANCE => "Perawatan",
    ];

    protected $fillable = ["room_number", "room_type", "floor", "status"];

    protected $appends = ["status_label"];

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::LABELS[$this->status] ?? $this->status;
    }
}
