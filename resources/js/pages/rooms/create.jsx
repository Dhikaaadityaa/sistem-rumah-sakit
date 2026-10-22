import Layout from "../../hospital/Layout";
import { PageHeader } from "../../hospital/ui";
import RoomForm from "../../hospital/forms/rooms";

export default function Create({ statuses }) {
    return (
        <Layout title="Tambah ruangan">
            <PageHeader title="Tambah ruangan" />
            <RoomForm statuses={statuses} />
        </Layout>
    );
}
