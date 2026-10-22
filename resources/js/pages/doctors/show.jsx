import { Link } from "@inertiajs/react";
import Layout from "../../hospital/Layout";
import { PageHeader } from "../../hospital/ui";

export default function Show({ doctor }) {
    return (
        <Layout title={doctor.name}>
            <PageHeader title={doctor.name} subtitle={doctor.specialty?.name}>
                <Link href="/doctors" className="rs-btn sec">Kembali</Link>
            </PageHeader>
            <div className="rs-card">
                <dl className="rs-dl">
                    <dt>Spesialisasi</dt><dd>{doctor.specialty?.name}</dd>
                    <dt>Telepon</dt><dd>{doctor.phone || "-"}</dd>
                    <dt>Deskripsi bidang</dt><dd>{doctor.specialty?.description || "-"}</dd>
                </dl>
            </div>
        </Layout>
    );
}
