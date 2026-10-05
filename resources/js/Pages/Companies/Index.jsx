import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router, useForm, usePage } from '@inertiajs/react';
import { Building2, Plus, Power } from 'lucide-react';

export default function Index({ companies }) {
    const { auth } = usePage().props;
    const permissions = auth.permissions ?? [];
    const canCreate = permissions.includes('companies.create');
    const canUpdate = permissions.includes('companies.update');
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        code: '',
    });

    const submit = (event) => {
        event.preventDefault();

        post(route('companies.store'), {
            onSuccess: () => reset(),
        });
    };

    const toggleStatus = (company) => {
        router.patch(
            route('companies.update', company.id),
            { is_active: !company.isActive },
            { preserveScroll: true },
        );
    };

    return (
        <AuthenticatedLayout
            header={
                <div>
                    <p className="text-sm font-medium text-teal-700">
                        Administracion
                    </p>
                    <h1 className="mt-1 text-2xl font-semibold text-slate-950">
                        Empresas
                    </h1>
                </div>
            }
        >
            <Head title="Empresas" />

            <div className={canCreate ? 'grid gap-8 lg:grid-cols-[minmax(0,1fr)_22rem]' : ''}>
                <section className="border border-slate-200 bg-white">
                    <div className="flex items-center justify-between border-b border-slate-200 px-5 py-4 sm:px-6">
                        <div>
                            <h2 className="text-base font-semibold text-slate-950">
                                Directorio
                            </h2>
                            <p className="mt-1 text-sm text-slate-500">
                                {companies.length} empresas registradas
                            </p>
                        </div>
                        <Building2 aria-hidden="true" className="h-5 w-5 text-slate-400" />
                    </div>

                    {companies.length === 0 ? (
                        <div className="px-6 py-14 text-center">
                            <p className="text-sm font-medium text-slate-700">
                                Todavia no hay empresas.
                            </p>
                            <p className="mt-1 text-sm text-slate-500">
                                Crea la primera desde el formulario.
                            </p>
                        </div>
                    ) : (
                        <div className="overflow-x-auto">
                            <table className="w-full text-left text-sm">
                                <thead className="border-b border-slate-200 bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    <tr>
                                        <th className="px-5 py-3 sm:px-6">Empresa</th>
                                        <th className="px-5 py-3">Codigo</th>
                                        <th className="px-5 py-3">Estado</th>
                                        {canUpdate && <th className="px-5 py-3 text-right sm:px-6">Accion</th>}
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-100">
                                    {companies.map((company) => (
                                        <tr key={company.id}>
                                            <td className="px-5 py-4 font-medium text-slate-900 sm:px-6">
                                                {company.name}
                                            </td>
                                            <td className="px-5 py-4 font-mono text-xs text-slate-500">
                                                {company.code}
                                            </td>
                                            <td className="px-5 py-4">
                                                <span
                                                    className={`inline-flex border px-2 py-1 text-xs font-medium ${
                                                        company.isActive
                                                            ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                                                            : 'border-slate-200 bg-slate-100 text-slate-600'
                                                    }`}
                                                >
                                                    {company.isActive ? 'Activa' : 'Inactiva'}
                                                </span>
                                            </td>
                                            {canUpdate && (
                                                <td className="px-5 py-4 text-right sm:px-6">
                                                    <button
                                                        type="button"
                                                        onClick={() => toggleStatus(company)}
                                                        className="inline-grid h-9 w-9 place-items-center text-slate-500 hover:bg-slate-100 hover:text-slate-900"
                                                        aria-label={`${company.isActive ? 'Desactivar' : 'Activar'} ${company.name}`}
                                                        title={company.isActive ? 'Desactivar' : 'Activar'}
                                                    >
                                                        <Power aria-hidden="true" className="h-4 w-4" />
                                                    </button>
                                                </td>
                                            )}
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}
                </section>

                {canCreate && <section className="border border-slate-200 bg-white p-5 sm:p-6">
                    <div className="flex items-center gap-2">
                        <Plus aria-hidden="true" className="h-5 w-5 text-teal-700" />
                        <h2 className="text-base font-semibold text-slate-950">
                            Nueva empresa
                        </h2>
                    </div>

                    <form onSubmit={submit} className="mt-6 space-y-5">
                        <div>
                            <InputLabel htmlFor="name" value="Nombre" />
                            <TextInput
                                id="name"
                                name="name"
                                value={data.name}
                                className="mt-1 block w-full"
                                onChange={(event) => setData('name', event.target.value)}
                                required
                            />
                            <InputError message={errors.name} className="mt-2" />
                        </div>

                        <div>
                            <InputLabel htmlFor="code" value="Codigo" />
                            <TextInput
                                id="code"
                                name="code"
                                value={data.code}
                                className="mt-1 block w-full uppercase"
                                onChange={(event) => setData('code', event.target.value.toUpperCase())}
                                required
                            />
                            <InputError message={errors.code} className="mt-2" />
                        </div>

                        <button
                            type="submit"
                            disabled={processing}
                            className="inline-flex w-full items-center justify-center gap-2 bg-teal-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-teal-700 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            <Plus aria-hidden="true" className="h-4 w-4" />
                            Crear empresa
                        </button>
                    </form>
                </section>}
            </div>
        </AuthenticatedLayout>
    );
}
