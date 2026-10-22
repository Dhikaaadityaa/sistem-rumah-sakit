import { Link, useForm } from "@inertiajs/react";
import { Field } from "../ui";

export default function RoomForm({ room, statuses }) {
    const { data, setData, post, put, processing, errors } = useForm({
        room_number: room?.room_number ?? "",
        room_type: room?.room_type ?? "",
        floor: room?.floor ?? 1,
        status: room?.status ?? "available",
    });

    const kirim = (e) => {
        e.preventDefault();
        room ? put(`/rooms/${room.id}`) : post("/rooms");
    };

    return (
        <form onSubmit={kirim} className="rs-card rs-form" style={{ maxWidth: 560 }}>
            <Field label="Nomor ruangan" error={errors.room_number}>
                <input value={data.room_number} onChange={(e) => setData("room_number", e.target.value)} maxLength={10} autoFocus />
            </Field>
            <Field label="Tipe ruangan" error={errors.room_type} help="Contoh: Poli Umum, Poli Gigi, Rawat Inap.">
                <input value={data.room_type} onChange={(e) => setData("room_type", e.target.value)} maxLength={30} />
            </Field>
            <Field label="Lantai" error={errors.floor}>
                <input type="number" min={0} max={50} value={data.floor} onChange={(e) => setData("floor", e.target.value)} />
            </Field>
            <Field label="Status" error={errors.status}>
                <select value={data.status} onChange={(e) => setData("status", e.target.value)}>
                    {Object.entries(statuses).map(([k, v]) => <option key={k} value={k}>{v}</option>)}
                </select>
            </Field>
            <div className="rs-actions">
                <button className="rs-btn" disabled={processing}>Simpan</button>
                <Link href="/rooms" className="rs-btn sec">Batal</Link>
            </div>
        </form>
    );
}
