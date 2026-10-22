import Layout from "../../hospital/Layout";
import AppointmentForm from "../../hospital/forms/appointments";
import { PageHeader } from "../../hospital/ui";

export default function Create({ doctors, slots }) {
    return (
        <Layout title="Ajukan janji temu">
            <PageHeader title="Ajukan janji temu" subtitle="Janji akan menunggu konfirmasi admin" />
            <AppointmentForm doctors={doctors} slots={slots} />
        </Layout>
    );
}
