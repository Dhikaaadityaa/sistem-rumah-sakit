<?php

namespace App\Support;

/**
 * Tanggal disimpan di basis data dalam format Y-m-d.
 * Kelas ini hanya mengubah TAMPILAN: pecah dengan explode("-"),
 * lalu ganti angka bulan menjadi nama bulan Indonesia.
 */
class Tanggal
{
    private const BULAN = [
        "01" => "Januari",
        "02" => "Februari",
        "03" => "Maret",
        "04" => "April",
        "05" => "Mei",
        "06" => "Juni",
        "07" => "Juli",
        "08" => "Agustus",
        "09" => "September",
        "10" => "Oktober",
        "11" => "November",
        "12" => "Desember",
    ];

    /** "2026-10-05" => "5 Oktober 2026" */
    public static function indonesia(?string $ymd): ?string
    {
        if (! $ymd) {
            return null;
        }

        [$tahun, $bulan, $hari] = explode("-", substr($ymd, 0, 10));

        return ((int) $hari)." ".self::BULAN[$bulan]." ".$tahun;
    }

    /** "2026-10" => "Oktober 2026" (untuk rekap bulanan) */
    public static function bulanTahun(string $ym): string
    {
        [$tahun, $bulan] = explode("-", $ym);

        return self::BULAN[$bulan]." ".$tahun;
    }
}
