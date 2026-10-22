import { Link, router } from "@inertiajs/react";
import Layout from "../../hospital/Layout";
import { Empty, PageHeader, Pagination, StatusBadge, hapus } from "../../hospital/ui";

export default function Index({ rooms, filters, statuses, can }) {
    const ubahStatus = (v) => router.get("/rooms", { status: v || undefined }, { preserveState: true, replace: true });

    return (
        <Layout title="Ruangan">
            <PageHeader title="Ruangan" subtitle="Ruang pemeriksaan dan perawatan">
                {can.tambah ? <Link href="/rooms/create" className="rs-btn">+ Tambah ruangan</Link> : null}
            </PageHeader>
            <div className="rs-filter">
                <div style={{ maxWidth: 220 }}>
                    <label>Status</label>
                    <select value={filters.status ?? ""} onChange={(e) => ubahStatus(e.target.value)}>
                        <option value="">Semua</option>
                        {Object.entries(statuses).map(([k, v]) => <option key={k} value={k}>{v}</option>)}
                    </select>
                </div>
            </div>
            <div className="rs-tw">
                <table className="rs-t">
                    <thead><tr><th>Nomor</th><th>Tipe</th><th>Lantai</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        {rooms.data.map((r) => (
                            <tr key={r.id}>
                                <td><b>{r.room_number}</b></td>
                                <td>{r.room_type}</td>
                                <td>{r.floor}</td>
                                <td><StatusBadge status={r.status} label={r.status_label} /></td>
                                <td className="rs-r">
                                    {r.can.ubah ? <Link href={`/rooms/${r.id}/edit`} className="rs-btn sec sm">Ubah</Link> : null}
                                    {r.can.hapus ? <button className="rs-btn danger sm" onClick={() => hapus(`/rooms/${r.id}`, `Hapus ruangan ${r.room_number}?`)}>Hapus</button> : null}
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
                {rooms.data.length === 0 ? <Empty>Tidak ada ruangan.</Empty> : null}
            </div>
            <Pagination links={rooms.links} />
        </Layout>
    );
}
