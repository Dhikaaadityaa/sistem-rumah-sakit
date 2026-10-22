import { Link, router } from "@inertiajs/react";

/** Judul halaman + tombol aksi di kanan. */
export function PageHeader({ title, subtitle, children }) {
    return (
        <div className="rs-head">
            <div>
                <h1>{title}</h1>
                {subtitle ? <p>{subtitle}</p> : null}
            </div>
            <div className="rs-actions">{children}</div>
        </div>
    );
}

/** Pesan galat per kolom (dari validasi server). */
export function FieldError({ message }) {
    return message ? <div className="rs-err">{message}</div> : null;
}

/** Satu baris isian formulir: label + isi + galat. */
export function Field({ label, error, help, children }) {
    return (
        <div className="rs-f">
            <label>{label}</label>
            {children}
            {help ? <div className="rs-help">{help}</div> : null}
            <FieldError message={error} />
        </div>
    );
}

export function StatusBadge({ status, label }) {
    return <span className={`rs-badge rs-b-${status}`}>{label ?? status}</span>;
}

/** Tautan halaman dari paginator Laravel (links[]). Label berisi HTML entitas, jadi diganti manual. */
export function Pagination({ links }) {
    if (!links || links.length <= 3) return null;
    const teks = (l) =>
        l.label.replace("&laquo; Previous", "‹ Sebelumnya").replace("Next &raquo;", "Berikutnya ›");
    return (
        <nav className="rs-pg" aria-label="Halaman">
            {links.map((l, i) =>
                l.url ? (
                    <Link key={i} href={l.url} className={l.active ? "on" : ""} preserveScroll>
                        {teks(l)}
                    </Link>
                ) : (
                    <span key={i} className="off">
                        {teks(l)}
                    </span>
                ),
            )}
        </nav>
    );
}

/** Hapus dengan konfirmasi. Penegakan tetap di server (Policy). */
export function hapus(url, pesan) {
    if (window.confirm(pesan)) {
        router.delete(url, { preserveScroll: true });
    }
}

export function Empty({ children }) {
    return <div className="rs-empty">{children}</div>;
}
