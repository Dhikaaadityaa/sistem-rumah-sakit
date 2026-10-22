import { Link, useForm } from "@inertiajs/react";
import { Field } from "../ui";

function hariIni() {
    const d = new Date();
    const p = (n) => String(n).padStart(2, "0");
    return `${d.getFullYear()}-${p(d.getMonth() + 1)}-${p(d.getDate())}`;
}

/** Dipakai untuk mengajukan (create) dan mengubah (edit) janji temu oleh pasien. */
export default function AppointmentForm({ appointment, doctors, slots }) {
    const { data, setData, post, put, processing, errors } = useForm({
        doctor_id: appointment?.doctor_id ?? "",
        appointment_date: appointment?.appointment_date?.slice(0, 10) ?? "",
        appointment_time: appointment?.appointment_time?.slice(0, 5) ?? "",
        notes: appointment?.notes ?? "",
    });

    const kirim = (e) => {
        e.preventDefault();
        appointment ? put(`/appointments/${appointment.id}`) : post("/appointments");
    };

    return (
        <form onSubmit={kirim} className="rs-card rs-form" style={{ maxWidth: 600 }}>
            <Field label="Dokter" error={errors.doctor_id}>
                <select value={data.doctor_id} onChange={(e) => setData("doctor_id", e.target.value)}>
                    <option value="">Pilih dokter</option>
                    {doctors.map((d) => <option key={d.id} value={d.id}>{d.name} - {d.specialty?.name}</option>)}
                </select>
            </Field>
            <Field label="Tanggal" error={errors.appointment_date}>
                <input type="date" min={hariIni()} value={data.appointment_date} onChange={(e) => setData("appointment_date", e.target.value)} />
            </Field>
            <Field label="Jam" error={errors.appointment_time} help="Jam praktik 08:00 sampai 15:30, setiap 30 menit.">
                <select value={data.appointment_time} onChange={(e) => setData("appointment_time", e.target.value)}>
                    <option value="">Pilih jam</option>
                    {slots.map((s) => <option key={s} value={s}>{s}</option>)}
                </select>
            </Field>
            <Field label="Keluhan (opsional)" error={errors.notes} help="Disimpan terenkripsi di basis data.">
                <textarea rows={4} maxLength={500} value={data.notes} onChange={(e) => setData("notes", e.target.value)} />
            </Field>
            <div className="rs-actions">
                <button className="rs-btn" disabled={processing}>{appointment ? "Simpan perubahan" : "Ajukan janji temu"}</button>
                <Link href={appointment ? `/appointments/${appointment.id}` : "/appointments"} className="rs-btn sec">Batal</Link>
            </div>
        </form>
    );
}
