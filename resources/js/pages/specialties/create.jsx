import Layout from "../../hospital/Layout";
import { PageHeader } from "../../hospital/ui";
import SpecialtyForm from "../../hospital/forms/specialties";

export default function Create() {
    return (
        <Layout title="Tambah spesialisasi">
            <PageHeader title="Tambah spesialisasi" />
            <SpecialtyForm />
        </Layout>
    );
}
