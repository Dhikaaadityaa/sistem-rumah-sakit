import Layout from "../../hospital/Layout";
import { PageHeader } from "../../hospital/ui";
import SpecialtyForm from "../../hospital/forms/specialties";

export default function Edit({ specialty }) {
    return (
        <Layout title="Ubah spesialisasi">
            <PageHeader title="Ubah spesialisasi" subtitle={specialty.name} />
            <SpecialtyForm specialty={specialty} />
        </Layout>
    );
}
