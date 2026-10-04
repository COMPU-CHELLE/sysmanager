import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, usePage } from '@inertiajs/react';
import { ArrowRight, CircleCheckBig, UserRound } from 'lucide-react';

export default function Dashboard() {
    const { auth } = usePage().props;

    return (
        <AuthenticatedLayout
            header={
                <div>
                    <p className="text-sm font-medium text-teal-700">Panel</p>
                    <h1 className="mt-1 text-2xl font-semibold text-slate-950">
                        Hola, {auth.user.name}
                    </h1>
                </div>
            }
        >
            <Head title="Panel" />

            <section className="border border-slate-200 bg-white p-6 sm:p-8">
                <div className="flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between">
                    <div className="max-w-xl">
                        <div className="flex items-center gap-2 text-sm font-medium text-teal-700">
                            <CircleCheckBig aria-hidden="true" className="h-5 w-5" />
                            Entorno preparado
                        </div>
                        <h2 className="mt-4 text-xl font-semibold text-slate-950">
                            Tu espacio de trabajo esta listo.
                        </h2>
                        <p className="mt-2 text-sm leading-6 text-slate-600">
                            El acceso esta protegido y la base de datos ya esta conectada.
                        </p>
                    </div>
                    <Link
                        href={route('profile.edit')}
                        className="inline-flex shrink-0 items-center justify-center gap-2 border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:border-slate-400 hover:bg-slate-50"
                    >
                        <UserRound aria-hidden="true" className="h-4 w-4" />
                        Ver perfil
                        <ArrowRight aria-hidden="true" className="h-4 w-4" />
                    </Link>
                </div>
            </section>
        </AuthenticatedLayout>
    );
}
