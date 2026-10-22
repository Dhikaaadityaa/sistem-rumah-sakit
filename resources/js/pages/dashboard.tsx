import { Head, Link } from '@inertiajs/react';
import {
    CalendarDays,
    CheckCircle2,
    Clock3,
    DoorOpen,
    Stethoscope,
    Users,
    XCircle,
    Activity,
} from 'lucide-react';
import { dashboard } from '@/routes';

type Appointment = {
    id: number;
    appointment_date: string;
    jam: string;
    status: string;
    status_label: string;
    user?: { id: number; name: string };
    doctor?: { id: number; name: string };
    room?: { id: number; room_number: string } | null;
};

type Props = {
    stats: {
        appointments: number;
        pending: number;
        confirmed: number;
        completed: number;
        cancelled: number;
        doctors: number;
        rooms: number;
        availableRooms: number;
        specialties: number;
        patients: number;
    };
    recentAppointments: Appointment[];
    isAdmin: boolean;
};

const statusStyles: Record<string, string> = {
    pending: 'bg-amber-50 text-amber-700 ring-amber-600/20',
    confirmed: 'bg-teal-50 text-teal-700 ring-teal-600/20',
    completed: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
    cancelled: 'bg-rose-50 text-rose-700 ring-rose-600/20',
};

function StatCard({
    title,
    value,
    detail,
    icon: Icon,
    iconStyle,
}: {
    title: string;
    value: number;
    detail: string;
    icon: React.ElementType;
    iconStyle: string;
}) {
    return (
        <div className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <div className="flex items-start justify-between gap-3">
                <div>
                    <p className="text-sm font-medium text-slate-500">{title}</p>
                    <p className="mt-3 text-3xl font-bold tracking-tight text-slate-900">
                        {value}
                    </p>
                    <p className="mt-2 text-xs text-slate-500">{detail}</p>
                </div>
                <div className={`rounded-xl p-3 ${iconStyle}`}>
                    <Icon className="size-5" />
                </div>
            </div>
        </div>
    );
}

export default function Dashboard({
    stats,
    recentAppointments,
    isAdmin,
}: Props) {
    const today = new Intl.DateTimeFormat('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    }).format(new Date());

    return (
        <>
            <Head title="Dasbor Rumah Sakit" />

            <div className="min-h-full space-y-6 bg-slate-50/70 p-4 md:p-6 lg:p-8">
                <section className="flex flex-col justify-between gap-4 rounded-2xl bg-gradient-to-r from-teal-800 to-teal-600 p-6 text-white shadow-sm md:flex-row md:items-center md:p-8">
                    <div>
                        <p className="text-sm font-medium text-teal-100">
                            {today}
                        </p>
                        <h1 className="mt-2 text-2xl font-bold tracking-tight md:text-3xl">
                            {isAdmin ? 'Dasbor Rumah Sakit' : 'Dasbor Pasien'}
                        </h1>
                        <p className="mt-2 max-w-xl text-sm leading-6 text-teal-50">
                            {isAdmin
                                ? 'Pantau aktivitas layanan, jadwal dokter, dan ketersediaan ruangan dalam satu tempat.'
                                : 'Lihat ringkasan janji temu dan informasi layanan rumah sakit.'}
                        </p>
                    </div>
                    <div className="flex size-14 items-center justify-center rounded-2xl bg-white/15 ring-1 ring-white/20">
                        <Activity className="size-7" />
                    </div>
                </section>

                <section>
                    <div className="mb-4">
                        <h2 className="text-lg font-bold text-slate-900">
                            Ringkasan
                        </h2>
                        <p className="mt-1 text-sm text-slate-500">
                            Statistik terkini berdasarkan data sistem.
                        </p>
                    </div>

                    <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        <StatCard
                            title="Total Janji Temu"
                            value={stats.appointments}
                            detail="Seluruh janji temu yang tercatat"
                            icon={CalendarDays}
                            iconStyle="bg-teal-50 text-teal-700"
                        />
                        <StatCard
                            title="Menunggu"
                            value={stats.pending}
                            detail="Perlu tindak lanjut"
                            icon={Clock3}
                            iconStyle="bg-amber-50 text-amber-700"
                        />
                        <StatCard
                            title="Dokter"
                            value={stats.doctors}
                            detail="Dokter terdaftar"
                            icon={Stethoscope}
                            iconStyle="bg-sky-50 text-sky-700"
                        />
                        <StatCard
                            title="Ruangan Tersedia"
                            value={stats.availableRooms}
                            detail={`Dari ${stats.rooms} ruangan`}
                            icon={DoorOpen}
                            iconStyle="bg-emerald-50 text-emerald-700"
                        />
                    </div>
                </section>

                <section className="grid gap-6 xl:grid-cols-[1.6fr_1fr]">
                    <div className="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div className="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                            <div>
                                <h2 className="font-bold text-slate-900">
                                    Janji Temu Terbaru
                                </h2>
                                <p className="mt-1 text-xs text-slate-500">
                                    Lima data janji temu terakhir
                                </p>
                            </div>
                            <Link
                                href="/appointments"
                                className="rounded-lg px-3 py-2 text-sm font-semibold text-teal-700 transition hover:bg-teal-50"
                            >
                                Lihat semua
                            </Link>
                        </div>

                        {recentAppointments.length > 0 ? (
                            <div className="overflow-x-auto">
                                <table className="w-full text-left text-sm">
                                    <thead className="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                        <tr>
                                            {isAdmin && (
                                                <th className="px-5 py-3 font-semibold">
                                                    Pasien
                                                </th>
                                            )}
                                            <th className="px-5 py-3 font-semibold">
                                                Dokter
                                            </th>
                                            <th className="px-5 py-3 font-semibold">
                                                Jadwal
                                            </th>
                                            <th className="px-5 py-3 font-semibold">
                                                Status
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-slate-100">
                                        {recentAppointments.map((item) => (
                                            <tr key={item.id} className="transition hover:bg-slate-50/80">
                                                {isAdmin && (
                                                    <td className="whitespace-nowrap px-5 py-4 font-medium text-slate-800">
                                                        {item.user?.name ?? '-'}
                                                    </td>
                                                )}
                                                <td className="whitespace-nowrap px-5 py-4 text-slate-700">
                                                    {item.doctor?.name ?? '-'}
                                                </td>
                                                <td className="whitespace-nowrap px-5 py-4 text-slate-600">
                                                    <div>{item.appointment_date}</div>
                                                    <div className="mt-1 text-xs text-slate-400">
                                                        {item.jam}
                                                    </div>
                                                </td>
                                                <td className="whitespace-nowrap px-5 py-4">
                                                    <span className={`inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset ${statusStyles[item.status] ?? 'bg-slate-100 text-slate-600 ring-slate-500/20'}`}>
                                                        {item.status_label}
                                                    </span>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        ) : (
                            <div className="px-5 py-12 text-center">
                                <CalendarDays className="mx-auto size-8 text-slate-300" />
                                <p className="mt-3 font-medium text-slate-700">
                                    Belum ada janji temu
                                </p>
                                <p className="mt-1 text-sm text-slate-500">
                                    Data janji temu akan tampil di bagian ini.
                                </p>
                            </div>
                        )}
                    </div>

                    <div className="space-y-6">
                        <div className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <h2 className="font-bold text-slate-900">
                                Status Janji Temu
                            </h2>
                            <p className="mt-1 text-sm text-slate-500">
                                Distribusi berdasarkan status
                            </p>

                            <div className="mt-5 space-y-4">
                                {[
                                    { label: 'Dikonfirmasi', value: stats.confirmed, color: 'bg-teal-500', icon: CheckCircle2 },
                                    { label: 'Selesai', value: stats.completed, color: 'bg-emerald-500', icon: CheckCircle2 },
                                    { label: 'Dibatalkan', value: stats.cancelled, color: 'bg-rose-500', icon: XCircle },
                                ].map((item) => {
                                    const total = Math.max(stats.appointments, 1);
                                    const percent = Math.round((item.value / total) * 100);

                                    return (
                                        <div key={item.label}>
                                            <div className="mb-2 flex items-center justify-between gap-3 text-sm">
                                                <span className="flex items-center gap-2 text-slate-600">
                                                    <item.icon className="size-4 text-slate-400" />
                                                    {item.label}
                                                </span>
                                                <span className="font-semibold text-slate-900">
                                                    {item.value}
                                                </span>
                                            </div>
                                            <div className="h-2 overflow-hidden rounded-full bg-slate-100">
                                                <div
                                                    className={`h-full rounded-full ${item.color}`}
                                                    style={{ width: `${percent}%` }}
                                                />
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>
                        </div>

                        <div className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <h2 className="font-bold text-slate-900">
                                Informasi Layanan
                            </h2>
                            <div className="mt-4 divide-y divide-slate-100">
                                <div className="flex items-center justify-between py-3">
                                    <span className="flex items-center gap-2 text-sm text-slate-600">
                                        <Stethoscope className="size-4 text-teal-600" />
                                        Spesialisasi
                                    </span>
                                    <span className="font-bold text-slate-900">{stats.specialties}</span>
                                </div>
                                <div className="flex items-center justify-between py-3">
                                    <span className="flex items-center gap-2 text-sm text-slate-600">
                                        <Users className="size-4 text-teal-600" />
                                        Pasien terdaftar
                                    </span>
                                    <span className="font-bold text-slate-900">{stats.patients}</span>
                                </div>
                                <div className="flex items-center justify-between py-3">
                                    <span className="flex items-center gap-2 text-sm text-slate-600">
                                        <DoorOpen className="size-4 text-teal-600" />
                                        Ruangan tersedia
                                    </span>
                                    <span className="font-bold text-emerald-700">{stats.availableRooms} / {stats.rooms}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </>
    );
}

Dashboard.layout = {
    breadcrumbs: [
        {
            title: 'Dasbor',
            href: dashboard(),
        },
    ],
};