import { Link, router, usePage } from "@inertiajs/react";
import { useState } from "react";
import Layout from "../../hospital/Layout";
import { FieldError, PageHeader, StatusBadge, hapus } from "../../hospital/ui";

export default function Show({ appointment: a, rooms, can, transitions, statuses }) {
    const { errors = {} } = usePage().props;
    const [roomId, setRoomId] = useState(a.room_id ?? "");

    const ubahStatus = (status) => {
        const payload = { status };
        if (status === "confirmed") payload.room_id = roomId || null;
        router.patch(`/appointments/${a.id}/status`, payload, { preserveScroll: true });
    };
    const batal = () => {
        if (window.confirm("Batalkan janji temu ini?")) router.patch(`/appointments/${a.id}/cancel`, {}, { preserveScroll: true });
    };

    return (
        <Layout title="Detail janji temu">
            <PageHeader title="Detail janji temu" subtitle={`${a.tanggal_indonesia}, pukul ${a.jam}`}>
                <Link href="/appointments" className="rs-btn sec">Kembali</Link>
                {can.ubah ? <Link href={`/appointments/${a.id}/edit`} className="rs-btn sec">Ubah</Link> : null}
                {can.batal ? <button className="rs-btn danger" onClick={batal}>Batalkan</button> : null}
                {can.hapus ? <button className="rs-btn danger" onClick={() => hapus(`/appointments/${a.id}`, "Hapus janji temu ini secara permanen?")}>Hapus</button> : null}
            </PageHeader>

            <div className="rs-card">
                <dl className="rs-dl">
                    <dt>Status</dt><dd><StatusBadge status={a.status} label={a.status_label} /></dd>
                    <dt>Pasien</dt><dd>{a.user?.name}{a.user?.email ? <span className="rs-muted"> ({a.user.email})</span> : null}</dd>
                    <dt>Dokter</dt><dd>{a.doctor?.name}</dd>
                    <dt>Spesialisasi</dt><dd>{a.doctor?.specialty?.name}</dd>
                    <dt>Tanggal</dt><dd>{a.tanggal_indonesia}</dd>
                    <dt>Jam</dt><dd>{a.jam}</dd>
                    <dt>Ruangan</dt><dd>{a.room ? `${a.room.room_number} (${a.room.room_type}, lantai ${a.room.floor})` : "Belum ditetapkan"}</dd>
                    <dt>Keluhan</dt><dd style={{ whiteSpace: "pre-wrap" }}>{a.notes || "-"}</dd>
                </dl>
            </div>

            {can.kelola ? (
                <div className="rs-card rs-form">
                    <h2>Kelola janji temu</h2>
                    {transitions.includes("confirmed") ? (
                        <div className="rs-f" style={{ maxWidth: 360 }}>
                            <label>Ruangan (wajib saat konfirmasi)</label>
                            <select value={roomId} onChange={(e) => setRoomId(e.target.value)}>
                                <option value="">Pilih ruangan tersedia</option>
                                {rooms.map((r) => <option key={r.id} value={r.id}>{r.room_number} - {r.room_type} (lt. {r.floor})</option>)}
                            </select>
                            <FieldError message={errors.room_id} />
                        </div>
                    ) : null}
                    <FieldError message={errors.status} />
                    <div className="rs-actions">
                        {transitions.includes("confirmed") ? <button className="rs-btn" onClick={() => ubahStatus("confirmed")}>Konfirmasi</button> : null}
                        {transitions.includes("completed") ? <button className="rs-btn" onClick={() => ubahStatus("completed")}>Tandai selesai</button> : null}
                        {transitions.includes("cancelled") ? <button className="rs-btn danger" onClick={() => window.confirm("Batalkan janji temu ini?") && ubahStatus("cancelled")}>Batalkan</button> : null}
                    </div>
                </div>
            ) : null}
        </Layout>
    );
}
