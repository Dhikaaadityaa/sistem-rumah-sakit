import Layout from "../../hospital/Layout";
import { PageHeader } from "../../hospital/ui";
import DoctorForm from "../../hospital/forms/doctors";

export default function Edit({ doctor, specialties }) {
    return (
        <Layout title="Ubah dokter">
            <PageHeader title="Ubah dokter" subtitle={doctor.name} />
            <DoctorForm doctor={doctor} specialties={specialties} />
        </Layout>
    );
}
