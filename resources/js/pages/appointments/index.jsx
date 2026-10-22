import { Link, router, usePage } from "@inertiajs/react";
import { useState } from "react";
import Layout from "../../hospital/Layout";
import { Empty, PageHeader, Pagination, StatusBadge, hapus } from "../../hospital/ui";

export default function Index({ appointments, filters, statuses, specialties, can }) {
    const { auth, errors = {} } = usePage().props;
    const admin = auth?.user?.role === "admin";
    const [f, setF] = useState({
        q: filters.q ?? "", status: filters.status ?? "", specialty_id: filters.specialty_id ?? "",
        from: filters.from ?? "", to: filters.to ?? "",
    });
    const set = (k) => (e) => setF({ ...f, [k]: e.target.value });

    const cari = (e) => {
        e.preventDefault();
        const params = Object.fromEntries(Object.entries(f).filter(([, v]) => v !== ""));
        router.get("/appointments", params, { preserveState: true, replace: true });
    };
    const reset = () => {
        setF({ q: "", status: "", specialty_id: "", from: "", to: "" });
        router.get("/appointments");
    };
    const batal = (a) => {
        if (window.confirm("Batalkan janji temu ini?")) router.patch(`/appointments/${a.id}/cancel`, {}, { preserveScroll: true });
    };

    return (
        <Layout title="Janji Temu">
            <PageHeader title="Janji Temu" subtitle={admin ? "Semua janji temu pasien" : "Janji temu Anda"}>
                {can.tambah ? <Link href="/appointments/create" className="rs-btn">+ Ajukan janji temu</Link> : null}
            </PageHeader>

            <form className="rs-filter" onSubmit={cari}>
                <div style={{ flex: "2 1 200px" }}>
                    <label>{admin ? "Cari dokter atau pasien" : "Cari nama dokter"}</label>
                    <input value={f.q} onChange={set("q")} placeholder="Ketik nama" />
                </div>
                <div>
                    <label>Status</label>
                    <select value={f.status} onChange={set("status")}>
                        <option value="">Semua</option>
                        {Object.entries(statuses).map(([k, v]) => <option key={k} value={k}>{v}</option>)}
                    </select>
                </div>
                <div>
                    <label>Spesialisasi</label>
                    <select value={f.specialty_id} onChange={set("specialty_id")}>
                        <option value="">Semua</option>
                        {specialties.map((s) => <option key={s.id} value={s.id}>{s.name}</option>)}
                    </select>
                </div>
                <div>
                    <label>Dari tanggal</label>
                    <input type="date" value={f.from} onChange={set("from")} />
                </div>
                <div>
                    <label>Sampai tanggal</label>
                    <input type="date" value={f.to} onChange={set("to")} />
                </div>
                <div style={{ flex: "none" }} className="rs-actions">
                    <button className="rs-btn">Cari</button>
                    <button type="button" className="rs-btn sec" onClick={reset}>Atur ulang</button>
                </div>
            </form>
            {errors.to || errors.from ? <div className="rs-alert">{errors.to || errors.from}</div> : null}

            <div className="rs-tw">
                <table className="rs-t">
                    <thead>
                        <tr>
                            <th>Tanggal</th><th>Jam</th>{admin ? <th>Pasien</th> : null}
                            <th>Dokter</th><th>Ruangan</th><th>Status</th><th></th>
                        </tr>
                    </thead>
                    <tbody>
                        {appointments.data.map((a) => (
                            <tr key={a.id}>
                                <td>{a.tanggal_indonesia}</td>
                                <td>{a.jam}</td>
                                {admin ? <td>{a.user?.name}</td> : null}
                                <td>
                                    {a.doctor?.name}
                                    <div className="rs-muted" style={{ fontSize: 12 }}>{a.doctor?.specialty?.name}</div>
                                </td>
                                <td>{a.room?.room_number ?? <span className="rs-muted">-</span>}</td>
                                <td><StatusBadge status={a.status} label={a.status_label} /></td>
                                <td className="rs-r">
                                    {a.can.lihat ? <Link href={`/appointments/${a.id}`} className="rs-btn sec sm">Detail</Link> : null}
                                    {a.can.ubah ? <Link href={`/appointments/${a.id}/edit`} className="rs-btn sec sm">Ubah</Link> : null}
                                    {a.can.batal ? <button className="rs-btn danger sm" onClick={() => batal(a)}>Batalkan</button> : null}
                                    {a.can.hapus ? <button className="rs-btn danger sm" onClick={() => hapus(`/appointments/${a.id}`, "Hapus janji temu ini secara permanen?")}>Hapus</button> : null}
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
                {appointments.data.length === 0 ? <Empty>Tidak ada janji temu yang cocok.</Empty> : null}
            </div>
            <Pagination links={appointments.links} />
        </Layout>
    );
}
