import Layout from "../../hospital/Layout";
import { PageHeader } from "../../hospital/ui";
import DoctorForm from "../../hospital/forms/doctors";

export default function Create({ specialties }) {
    return (
        <Layout title="Tambah dokter">
            <PageHeader title="Tambah dokter" />
            <DoctorForm specialties={specialties} />
        </Layout>
    );
}
