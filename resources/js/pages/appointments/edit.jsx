import Layout from "../../hospital/Layout";
import AppointmentForm from "../../hospital/forms/appointments";
import { PageHeader } from "../../hospital/ui";

export default function Edit({ appointment, doctors, slots }) {
    return (
        <Layout title="Ubah janji temu">
            <PageHeader title="Ubah janji temu" subtitle="Hanya dapat diubah selama masih menunggu konfirmasi" />
            <AppointmentForm appointment={appointment} doctors={doctors} slots={slots} />
        </Layout>
    );
}
