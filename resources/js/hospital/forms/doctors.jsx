import { Link, useForm } from "@inertiajs/react";
import { Field } from "../ui";

export default function DoctorForm({ doctor, specialties }) {
    const { data, setData, post, put, processing, errors } = useForm({
        name: doctor?.name ?? "",
        specialty_id: doctor?.specialty_id ?? "",
        phone: doctor?.phone ?? "",
    });

    const kirim = (e) => {
        e.preventDefault();
        doctor ? put(`/doctors/${doctor.id}`) : post("/doctors");
    };

    return (
        <form onSubmit={kirim} className="rs-card rs-form" style={{ maxWidth: 560 }}>
            <Field label="Nama dokter" error={errors.name}>
                <input value={data.name} onChange={(e) => setData("name", e.target.value)} maxLength={120} autoFocus />
            </Field>
            <Field label="Spesialisasi" error={errors.specialty_id}>
                <select value={data.specialty_id} onChange={(e) => setData("specialty_id", e.target.value)}>
                    <option value="">Pilih spesialisasi</option>
                    {specialties.map((s) => <option key={s.id} value={s.id}>{s.name}</option>)}
                </select>
            </Field>
            <Field label="Nomor telepon (opsional)" error={errors.phone} help="Hanya angka, spasi, tanda + dan -.">
                <input value={data.phone} onChange={(e) => setData("phone", e.target.value)} maxLength={20} />
            </Field>
            <div className="rs-actions">
                <button className="rs-btn" disabled={processing}>Simpan</button>
                <Link href="/doctors" className="rs-btn sec">Batal</Link>
            </div>
        </form>
    );
}
