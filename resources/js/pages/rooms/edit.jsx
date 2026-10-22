import Layout from "../../hospital/Layout";
import { PageHeader } from "../../hospital/ui";
import RoomForm from "../../hospital/forms/rooms";

export default function Edit({ room, statuses }) {
    return (
        <Layout title="Ubah ruangan">
            <PageHeader title="Ubah ruangan" subtitle={`Ruangan ${room.room_number}`} />
            <RoomForm room={room} statuses={statuses} />
        </Layout>
    );
}
