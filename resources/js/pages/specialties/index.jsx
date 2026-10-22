import { Link } from "@inertiajs/react";
import Layout from "../../hospital/Layout";
import { Empty, PageHeader, Pagination, hapus } from "../../hospital/ui";

export default function Index({ specialties }) {
    return (
        <Layout title="Spesialisasi">
            <PageHeader title="Spesialisasi" subtitle="Bidang keahlian dokter">
                <Link href="/specialties/create" className="rs-btn">+ Tambah spesialisasi</Link>
            </PageHeader>
            <div className="rs-tw">
                <table className="rs-t">
                    <thead>
                        <tr><th>Nama</th><th>Deskripsi</th><th>Jumlah dokter</th><th></th></tr>
                    </thead>
                    <tbody>
                        {specialties.data.map((s) => (
                            <tr key={s.id}>
                                <td><b>{s.name}</b></td>
                                <td>{s.description || <span className="rs-muted">-</span>}</td>
                                <td>{s.doctors_count}</td>
                                <td className="rs-r">
                                    <Link href={`/specialties/${s.id}/edit`} className="rs-btn sec sm">Ubah</Link>
                                    <button className="rs-btn danger sm" onClick={() => hapus(`/specialties/${s.id}`, `Hapus spesialisasi "${s.name}"?`)}>Hapus</button>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
                {specialties.data.length === 0 ? <Empty>Belum ada spesialisasi.</Empty> : null}
            </div>
            <Pagination links={specialties.links} />
        </Layout>
    );
}
