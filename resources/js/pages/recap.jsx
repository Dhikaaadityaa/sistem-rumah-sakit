import { Link } from "@inertiajs/react";
import Layout from "../hospital/Layout";
import { Empty, PageHeader, StatusBadge } from "../hospital/ui";

function Bars({ rows, labelKey = "label" }) {
    const max = Math.max(1, ...rows.map((r) => r.total));
    if (rows.length === 0) return <Empty>Belum ada data.</Empty>;
    return rows.map((r, i) => (
        <div className="rs-bar" key={i}>
            <span className="n" title={r[labelKey]}>{r[labelKey]}</span>
            <span className="t"><i style={{ width: `${(r.total / max) * 100}%` }} /></span>
            <span className="v">{r.total}</span>
        </div>
    ));
}

export default function Recap({ total_janji, per_status, per_bulan, per_spesialisasi, ruangan, hari_ini }) {
    return (
        <Layout title="Rekap">
            <PageHeader title="Rekap" subtitle="Ringkasan janji temu, spesialisasi, dan ruangan" />

            <div className="rs-grid rs-g4" style={{ marginBottom: 18 }}>
                <div className="rs-stat"><b>{total_janji}</b><span>Total janji temu</span></div>
                {per_status.map((s) => (
                    <div className="rs-stat" key={s.status}><b>{s.total}</b><span>{s.label}</span></div>
                ))}
            </div>

            <div className="rs-grid rs-g2">
                <div className="rs-card">
                    <h2>Janji temu per bulan (6 bulan terakhir)</h2>
                    <Bars rows={per_bulan} />
                </div>
                <div className="rs-card">
                    <h2>Janji temu per spesialisasi</h2>
                    <Bars rows={per_spesialisasi} labelKey="name" />
                </div>
                <div className="rs-card">
                    <h2>Status ruangan</h2>
                    <Bars rows={ruangan} />
                </div>
                <div className="rs-card">
                    <h2>Jadwal hari ini</h2>
                    {hari_ini.length === 0 ? <Empty>Tidak ada jadwal hari ini.</Empty> : (
                        <table className="rs-t">
                            <tbody>
                                {hari_ini.map((a) => (
                                    <tr key={a.id}>
                                        <td>{a.jam}</td>
                                        <td><Link className="rs-link" href={`/appointments/${a.id}`}>{a.user?.name}</Link><div className="rs-muted" style={{ fontSize: 12 }}>{a.doctor?.name}</div></td>
                                        <td>{a.room?.room_number ?? "-"}</td>
                                        <td><StatusBadge status={a.status} label={a.status_label} /></td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    )}
                </div>
            </div>
        </Layout>
    );
}
