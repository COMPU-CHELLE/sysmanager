import { Link } from '@inertiajs/react';
import { Boxes, ShieldCheck, Building2 } from 'lucide-react';

const highlights = [
    { icon: Building2, text: 'Multiempresa con datos aislados por cliente' },
    { icon: ShieldCheck, text: 'Roles y permisos por modulo y accion' },
    { icon: Boxes, text: 'Activos, facturas, tareas y tickets en un solo lugar' },
];

export default function GuestLayout({ children }) {
    return (
        <div className="grid min-h-screen bg-slate-50 lg:grid-cols-2">
            <aside className="hidden flex-col justify-between bg-slate-950 p-12 text-white lg:flex">
                <Link href="/" className="flex items-center gap-3 text-lg font-semibold">
                    <span className="grid h-10 w-10 place-items-center rounded-md bg-teal-600">
                        <Boxes aria-hidden="true" className="h-6 w-6" />
                    </span>
                    SysManager
                </Link>

                <div>
                    <h2 className="text-3xl font-semibold leading-tight">
                        Administra tu operacion de TI sin perder el control.
                    </h2>
                    <ul className="mt-8 space-y-4 text-slate-300">
                        {highlights.map(({ icon: Icon, text }) => (
                            <li key={text} className="flex items-center gap-3">
                                <Icon aria-hidden="true" className="h-5 w-5 text-teal-400" />
                                {text}
                            </li>
                        ))}
                    </ul>
                </div>

                <p className="text-sm text-slate-500">&copy; {new Date().getFullYear()} SysManager</p>
            </aside>

            <main className="flex items-center justify-center px-6 py-12">
                <div className="w-full max-w-md">
                    <Link href="/" className="mb-8 flex items-center gap-3 text-lg font-semibold text-slate-900 lg:hidden">
                        <span className="grid h-10 w-10 place-items-center rounded-md bg-teal-600 text-white">
                            <Boxes aria-hidden="true" className="h-6 w-6" />
                        </span>
                        SysManager
                    </Link>
                    <div className="border border-slate-200 bg-white p-8 shadow-sm">{children}</div>
                </div>
            </main>
        </div>
    );
}
