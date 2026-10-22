import { Link, useForm } from "@inertiajs/react";
import { Field } from "../ui";

export default function SpecialtyForm({ specialty }) {
    const { data, setData, post, put, processing, errors } = useForm({
        name: specialty?.name ?? "",
        description: specialty?.description ?? "",
    });

    const kirim = (e) => {
        e.preventDefault();
        specialty ? put(`/specialties/${specialty.id}`) : post("/specialties");
    };

    return (
        <form onSubmit={kirim} className="rs-card rs-form" style={{ maxWidth: 560 }}>
            <Field label="Nama spesialisasi" error={errors.name}>
                <input value={data.name} onChange={(e) => setData("name", e.target.value)} maxLength={100} autoFocus />
            </Field>
            <Field label="Deskripsi (opsional)" error={errors.description}>
                <textarea rows={4} value={data.description} onChange={(e) => setData("description", e.target.value)} maxLength={500} />
            </Field>
            <div className="rs-actions">
                <button className="rs-btn" disabled={processing}>Simpan</button>
                <Link href="/specialties" className="rs-btn sec">Batal</Link>
            </div>
        </form>
    );
}
