import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm, usePage } from '@inertiajs/react';
import { Plus, ShieldCheck } from 'lucide-react';

function toggleSelection(items, itemId) {
    return items.includes(itemId)
        ? items.filter((id) => id !== itemId)
        : [...items, itemId];
}

export default function Index({ roles, companies, permissions }) {
    const { auth } = usePage().props;
    const canCreate = (auth.permissions ?? []).includes('roles.create');
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        code: '',
        company_id: '',
        permission_ids: [],
    });

    const submit = (event) => {
        event.preventDefault();

        post(route('roles.store'), {
            onSuccess: () => reset(),
        });
    };

    return (
        <AuthenticatedLayout
            header={
                <div>
                    <p className="text-sm font-medium text-teal-700">Administracion</p>
                    <h1 className="mt-1 text-2xl font-semibold text-slate-950">Roles y permisos</h1>
                </div>
            }
        >
            <Head title="Roles y permisos" />

            <div className={canCreate ? 'grid gap-8 xl:grid-cols-[minmax(0,1fr)_25rem]' : ''}>
                <section className="border border-slate-200 bg-white">
                    <div className="flex items-center justify-between border-b border-slate-200 px-5 py-4 sm:px-6">
                        <div>
                            <h2 className="text-base font-semibold text-slate-950">Roles configurados</h2>
                            <p className="mt-1 text-sm text-slate-500">{roles.length} roles disponibles</p>
                        </div>
                        <ShieldCheck aria-hidden="true" className="h-5 w-5 text-slate-400" />
                    </div>

                    {roles.length === 0 ? (
                        <div className="px-6 py-14 text-center text-sm text-slate-500">No hay roles configurados.</div>
                    ) : (
                        <div className="divide-y divide-slate-100">
                            {roles.map((role) => (
                                <article key={role.id} className="px-5 py-4 sm:px-6">
                                    <div className="flex flex-wrap items-start justify-between gap-3">
                                        <div>
                                            <h3 className="text-sm font-semibold text-slate-950">{role.name}</h3>
                                            <p className="mt-1 font-mono text-xs text-slate-500">{role.code}</p>
                                        </div>
                                        <span className="border border-slate-200 bg-slate-50 px-2 py-1 text-xs text-slate-600">
                                            {role.company ?? 'Global'}
                                        </span>
                                    </div>
                                    <div className="mt-3 flex flex-wrap gap-2">
                                        {role.permissions.length === 0 ? (
                                            <span className="text-xs text-slate-400">Sin permisos</span>
                                        ) : role.permissions.map((permission) => (
                                            <span key={permission.id} className="border border-teal-200 bg-teal-50 px-2 py-1 text-xs text-teal-700">
                                                {permission.name}
                                            </span>
                                        ))}
                                    </div>
                                </article>
                            ))}
                        </div>
                    )}
                </section>

                {canCreate && <section className="border border-slate-200 bg-white p-5 sm:p-6">
                    <div className="flex items-center gap-2">
                        <Plus aria-hidden="true" className="h-5 w-5 text-teal-700" />
                        <h2 className="text-base font-semibold text-slate-950">Nuevo rol</h2>
                    </div>
                    <form onSubmit={submit} className="mt-6 space-y-5">
                        <div>
                            <InputLabel htmlFor="name" value="Nombre" />
                            <TextInput id="name" name="name" value={data.name} className="mt-1 block w-full" onChange={(event) => setData('name', event.target.value)} required />
                            <InputError message={errors.name} className="mt-2" />
                        </div>
                        <div>
                            <InputLabel htmlFor="code" value="Codigo" />
                            <TextInput id="code" name="code" value={data.code} className="mt-1 block w-full uppercase" onChange={(event) => setData('code', event.target.value.toUpperCase())} required />
                            <InputError message={errors.code} className="mt-2" />
                        </div>
                        <div>
                            <InputLabel htmlFor="company_id" value="Alcance" />
                            <select id="company_id" name="company_id" value={data.company_id} onChange={(event) => setData('company_id', event.target.value)} className="mt-1 block w-full border-slate-300 text-sm text-slate-700 focus:border-teal-500 focus:ring-teal-500">
                                <option value="">Global</option>
                                {companies.map((company) => <option key={company.id} value={company.id}>{company.name}</option>)}
                            </select>
                            <InputError message={errors.company_id} className="mt-2" />
                        </div>
                        <fieldset>
                            <legend className="text-sm font-medium text-slate-700">Permisos</legend>
                            <div className="mt-2 space-y-2">
                                {permissions.map((permission) => (
                                    <label key={permission.id} className="flex items-center gap-2 text-sm text-slate-600">
                                        <input type="checkbox" checked={data.permission_ids.includes(permission.id)} onChange={() => setData('permission_ids', toggleSelection(data.permission_ids, permission.id))} className="rounded border-slate-300 text-teal-600 focus:ring-teal-500" />
                                        <span>{permission.name}</span>
                                    </label>
                                ))}
                            </div>
                            <InputError message={errors.permission_ids} className="mt-2" />
                        </fieldset>
                        <button type="submit" disabled={processing} className="inline-flex w-full items-center justify-center gap-2 bg-teal-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-teal-700 disabled:cursor-not-allowed disabled:opacity-60">
                            <ShieldCheck aria-hidden="true" className="h-4 w-4" />
                            Crear rol
                        </button>
                    </form>
                </section>}
            </div>
        </AuthenticatedLayout>
    );
}
