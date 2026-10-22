import { Link, router } from "@inertiajs/react";
import { useState } from "react";
import Layout from "../../hospital/Layout";
import { Empty, PageHeader, Pagination, hapus } from "../../hospital/ui";

export default function Index({ doctors, specialties, filters, can }) {
    const [q, setQ] = useState(filters.q ?? "");
    const [spec, setSpec] = useState(filters.specialty_id ?? "");

    const cari = (e) => {
        e?.preventDefault();
        router.get("/doctors", { q: q || undefined, specialty_id: spec || undefined }, { preserveState: true, replace: true });
    };
    const reset = () => {
        setQ(""); setSpec("");
        router.get("/doctors");
    };

    return (
        <Layout title="Dokter">
            <PageHeader title="Dokter" subtitle="Daftar dokter dan spesialisasinya">
                {can.tambah ? <Link href="/doctors/create" className="rs-btn">+ Tambah dokter</Link> : null}
            </PageHeader>
            <form className="rs-filter" onSubmit={cari}>
                <div style={{ flex: "2 1 220px" }}>
                    <label>Cari nama dokter</label>
                    <input value={q} onChange={(e) => setQ(e.target.value)} placeholder="mis. Andi" />
                </div>
                <div>
                    <label>Spesialisasi</label>
                    <select value={spec} onChange={(e) => setSpec(e.target.value)}>
                        <option value="">Semua</option>
                        {specialties.map((s) => <option key={s.id} value={s.id}>{s.name}</option>)}
                    </select>
                </div>
                <div style={{ flex: "none" }} className="rs-actions">
                    <button className="rs-btn">Cari</button>
                    <button type="button" className="rs-btn sec" onClick={reset}>Atur ulang</button>
                </div>
            </form>
            <div className="rs-tw">
                <table className="rs-t">
                    <thead><tr><th>Nama</th><th>Spesialisasi</th><th>Telepon</th><th></th></tr></thead>
                    <tbody>
                        {doctors.data.map((d) => (
                            <tr key={d.id}>
                                <td><Link className="rs-link" href={`/doctors/${d.id}`}>{d.name}</Link></td>
                                <td>{d.specialty?.name}</td>
                                <td>{d.phone || <span className="rs-muted">-</span>}</td>
                                <td className="rs-r">
                                    {d.can.ubah ? <Link href={`/doctors/${d.id}/edit`} className="rs-btn sec sm">Ubah</Link> : null}
                                    {d.can.hapus ? <button className="rs-btn danger sm" onClick={() => hapus(`/doctors/${d.id}`, `Hapus ${d.name}?`)}>Hapus</button> : null}
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
                {doctors.data.length === 0 ? <Empty>Tidak ada dokter yang cocok.</Empty> : null}
            </div>
            <Pagination links={doctors.links} />
        </Layout>
    );
}
