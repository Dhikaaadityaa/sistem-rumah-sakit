import { Head, Link, router, usePage } from "@inertiajs/react";
import "./hospital.css";

/**
 * Kerangka semua halaman. Menu berasal dari server (props "menu", disaring Gate/Policy);
 * React hanya menggambar. Tidak ada keputusan hak akses di sini.
 */
export default function Layout({ title, children }) {
    const { menu = [], auth, flash, errors = {} } = usePage().props;
    const path = typeof window !== "undefined" ? window.location.pathname : "";
    const aktif = (href) => {
        let p = href;
        try {
            p = new URL(href, "http://x").pathname;
        } catch {
            /* abaikan */
        }
        return p === "/" ? path === "/" : path === p || path.startsWith(p + "/");
    };
    const peran = auth?.user?.role === "admin" ? "Admin" : "Pasien";

    return (
        <div className="rs-app">
            <Head title={title} />
            <header className="rs-top">
                <div className="rs-top-in">
                    <Link href="/dashboard" className="rs-brand">
                        <i>+</i> Rumah Sakit
                    </Link>
                    <nav className="rs-nav">
                        {menu.map((m) => (
                            <Link key={m.href} href={m.href} className={aktif(m.href) ? "on" : ""}>
                                {m.title}
                            </Link>
                        ))}
                    </nav>
                    {auth?.user ? (
                        <div className="rs-user">
                            <span>
                                {auth.user.name} ({peran})
                            </span>
                            <button className="rs-btn sec sm" onClick={() => router.post("/logout")}>
                                Keluar
                            </button>
                        </div>
                    ) : null}
                </div>
            </header>
            <main className="rs-main">
                {flash?.sukses ? <div className="rs-flash">{flash.sukses}</div> : null}
                {errors.hapus ? <div className="rs-alert">{errors.hapus}</div> : null}
                {children}
            </main>
        </div>
    );
}
