import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';

const statCards = [
    ['branches', 'Sucursales'],
    ['employees', 'Empleados'],
    ['assets', 'Activos'],
    ['users', 'Usuarios'],
    ['openTasks', 'Tareas abiertas'],
    ['openTickets', 'Tickets abiertos'],
    ['invoiceTotal', 'Total facturado'],
];

const titles = {
    support: 'Resumen de empresas',
    admin: 'Resumen de mi empresa',
    user: 'Mi actividad',
};

export default function Dashboard({ view, companies, selectedCompany, stats, myTasks, activity }) {
    const filter = (value) =>
        router.get(route('dashboard'), value ? { company_id: value } : {}, { preserveState: true, replace: true });

    return (
        <AuthenticatedLayout
            header={
                <div className="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <p className="text-sm font-medium text-teal-700">Panel</p>
                        <h1 className="mt-1 text-2xl font-semibold text-slate-950">{titles[view]}</h1>
                    </div>
                    {view !== 'user' && companies.length > 1 && (
                        <select
                            value={selectedCompany ?? ''}
                            onChange={(event) => filter(event.target.value)}
                            aria-label="Empresa"
                            className="rounded-md border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500"
                        >
                            <option value="">Todas las empresas</option>
                            {companies.map((company) => (
                                <option key={company.id} value={company.id}>
                                    {company.name}
                                </option>
                            ))}
                        </select>
                    )}
                </div>
            }
        >
            <Head title="Panel" />

            {view !== 'user' && (
                <section className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    {statCards.map(([key, label]) => (
                        <div key={key} className="border border-slate-200 bg-white p-5">
                            <p className="text-sm text-slate-500">{label}</p>
                            <p className="mt-2 text-2xl font-semibold text-slate-950">{stats[key]}</p>
                        </div>
                    ))}
                </section>
            )}

            <div className="mt-6 grid gap-6 lg:grid-cols-2">
                <section className="border border-slate-200 bg-white">
                    <h2 className="border-b border-slate-200 px-5 py-4 text-base font-semibold text-slate-950">
                        Mis tareas pendientes
                    </h2>
                    <ul className="divide-y divide-slate-100">
                        {myTasks.length === 0 && <li className="px-5 py-4 text-sm text-slate-500">Sin tareas pendientes.</li>}
                        {myTasks.map((task) => (
                            <li key={task.id} className="flex items-center justify-between px-5 py-3 text-sm">
                                <span className="text-slate-800">{task.title}</span>
                                <span className="text-xs text-slate-500">
                                    {task.priority} {task.dueDate && `· ${task.dueDate}`}
                                </span>
                            </li>
                        ))}
                    </ul>
                    <div className="border-t border-slate-100 px-5 py-3 text-sm">
                        <Link href={route('tasks.index')} className="font-medium text-teal-700 hover:text-teal-900">
                            Ver todas las tareas
                        </Link>
                    </div>
                </section>

                <section className="border border-slate-200 bg-white">
                    <h2 className="border-b border-slate-200 px-5 py-4 text-base font-semibold text-slate-950">
                        {view === 'user' ? 'Mis movimientos' : 'Actividad reciente'}
                    </h2>
                    <ul className="divide-y divide-slate-100">
                        {activity.length === 0 && <li className="px-5 py-4 text-sm text-slate-500">Sin movimientos.</li>}
                        {activity.map((log) => (
                            <li key={log.id} className="flex items-center justify-between px-5 py-3 text-sm">
                                <span className="text-slate-800">
                                    <span className="font-mono text-xs">{log.action}</span>
                                    {view !== 'user' && <span className="text-slate-500"> · {log.userName}</span>}
                                </span>
                                <span className="text-xs text-slate-500">{log.createdAt}</span>
                            </li>
                        ))}
                    </ul>
                </section>
            </div>
        </AuthenticatedLayout>
    );
}
