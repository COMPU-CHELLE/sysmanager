import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm, usePage } from '@inertiajs/react';
import { KeyRound, Plus, UsersRound } from 'lucide-react';

function toggleSelection(items, itemId) {
    return items.includes(itemId)
        ? items.filter((id) => id !== itemId)
        : [...items, itemId];
}

export default function Index({ users, companies, roles }) {
    const { auth } = usePage().props;
    const canCreate = (auth.permissions ?? []).includes('users.create');
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        code: '',
        email: '',
        password: '',
        password_confirmation: '',
        company_ids: [],
        role_ids: [],
    });

    const submit = (event) => {
        event.preventDefault();

        post(route('users.store'), {
            onSuccess: () => reset(),
        });
    };

    return (
        <AuthenticatedLayout
            header={
                <div>
                    <p className="text-sm font-medium text-teal-700">
                        Administracion
                    </p>
                    <h1 className="mt-1 text-2xl font-semibold text-slate-950">
                        Usuarios
                    </h1>
                </div>
            }
        >
            <Head title="Usuarios" />

            <div className={canCreate ? 'grid gap-8 xl:grid-cols-[minmax(0,1fr)_25rem]' : ''}>
                <section className="border border-slate-200 bg-white">
                    <div className="flex items-center justify-between border-b border-slate-200 px-5 py-4 sm:px-6">
                        <div>
                            <h2 className="text-base font-semibold text-slate-950">
                                Directorio
                            </h2>
                            <p className="mt-1 text-sm text-slate-500">
                                {users.length} usuarios registrados
                            </p>
                        </div>
                        <UsersRound aria-hidden="true" className="h-5 w-5 text-slate-400" />
                    </div>

                    {users.length === 0 ? (
                        <div className="px-6 py-14 text-center">
                            <p className="text-sm font-medium text-slate-700">
                                Todavia no hay usuarios.
                            </p>
                            <p className="mt-1 text-sm text-slate-500">
                                Crea la primera cuenta desde el formulario.
                            </p>
                        </div>
                    ) : (
                        <div className="divide-y divide-slate-100">
                            {users.map((user) => (
                                <article key={user.id} className="px-5 py-4 sm:px-6">
                                    <div className="flex items-start justify-between gap-4">
                                        <div className="min-w-0">
                                            <h3 className="truncate text-sm font-semibold text-slate-950">
                                                {user.name}
                                            </h3>
                                            <p className="mt-1 truncate text-sm text-slate-500">
                                                {user.email}
                                            </p>
                                        </div>
                                        <span className="shrink-0 font-mono text-xs text-slate-500">
                                            {user.code}
                                        </span>
                                    </div>
                                    <div className="mt-3 flex flex-wrap gap-2">
                                        {user.companies.map((company) => (
                                            <span key={company.id} className="border border-slate-200 bg-slate-50 px-2 py-1 text-xs text-slate-600">
                                                {company.name}
                                            </span>
                                        ))}
                                        {user.roles.map((role) => (
                                            <span key={role.id} className="border border-teal-200 bg-teal-50 px-2 py-1 text-xs text-teal-700">
                                                {role.name}
                                            </span>
                                        ))}
                                        {user.companies.length === 0 && user.roles.length === 0 && (
                                            <span className="text-xs text-slate-400">Sin asignaciones</span>
                                        )}
                                    </div>
                                </article>
                            ))}
                        </div>
                    )}
                </section>

                {canCreate && <section className="border border-slate-200 bg-white p-5 sm:p-6">
                    <div className="flex items-center gap-2">
                        <Plus aria-hidden="true" className="h-5 w-5 text-teal-700" />
                        <h2 className="text-base font-semibold text-slate-950">Nuevo usuario</h2>
                    </div>

                    <form onSubmit={submit} className="mt-6 space-y-5">
                        <div>
                            <InputLabel htmlFor="name" value="Nombre" />
                            <TextInput id="name" name="name" value={data.name} className="mt-1 block w-full" onChange={(event) => setData('name', event.target.value)} required />
                            <InputError message={errors.name} className="mt-2" />
                        </div>
                        <div>
                            <InputLabel htmlFor="code" value="Usuario" />
                            <TextInput id="code" name="code" value={data.code} className="mt-1 block w-full" onChange={(event) => setData('code', event.target.value)} required />
                            <InputError message={errors.code} className="mt-2" />
                        </div>
                        <div>
                            <InputLabel htmlFor="email" value="Correo" />
                            <TextInput id="email" type="email" name="email" value={data.email} className="mt-1 block w-full" onChange={(event) => setData('email', event.target.value)} required />
                            <InputError message={errors.email} className="mt-2" />
                        </div>
                        <div>
                            <InputLabel htmlFor="password" value="Contrasena" />
                            <TextInput id="password" type="password" name="password" value={data.password} className="mt-1 block w-full" autoComplete="new-password" onChange={(event) => setData('password', event.target.value)} required />
                            <InputError message={errors.password} className="mt-2" />
                        </div>
                        <div>
                            <InputLabel htmlFor="password_confirmation" value="Confirmar contrasena" />
                            <TextInput id="password_confirmation" type="password" name="password_confirmation" value={data.password_confirmation} className="mt-1 block w-full" autoComplete="new-password" onChange={(event) => setData('password_confirmation', event.target.value)} required />
                        </div>

                        <fieldset>
                            <legend className="text-sm font-medium text-slate-700">Empresas activas</legend>
                            <div className="mt-2 space-y-2">
                                {companies.length === 0 ? (
                                    <p className="text-sm text-slate-500">No hay empresas activas.</p>
                                ) : companies.map((company) => (
                                    <label key={company.id} className="flex items-center gap-2 text-sm text-slate-600">
                                        <input type="checkbox" checked={data.company_ids.includes(company.id)} onChange={() => setData('company_ids', toggleSelection(data.company_ids, company.id))} className="rounded border-slate-300 text-teal-600 focus:ring-teal-500" />
                                        {company.name}
                                    </label>
                                ))}
                            </div>
                            <InputError message={errors.company_ids} className="mt-2" />
                        </fieldset>

                        <fieldset>
                            <legend className="text-sm font-medium text-slate-700">Roles</legend>
                            <div className="mt-2 space-y-2">
                                {roles.length === 0 ? (
                                    <p className="text-sm text-slate-500">No hay roles disponibles.</p>
                                ) : roles.map((role) => (
                                    <label key={role.id} className="flex items-center gap-2 text-sm text-slate-600">
                                        <input type="checkbox" checked={data.role_ids.includes(role.id)} onChange={() => setData('role_ids', toggleSelection(data.role_ids, role.id))} className="rounded border-slate-300 text-teal-600 focus:ring-teal-500" />
                                        <span>{role.name}</span>
                                        <span className="text-xs text-slate-400">{role.company ?? 'Global'}</span>
                                    </label>
                                ))}
                            </div>
                            <InputError message={errors.role_ids} className="mt-2" />
                        </fieldset>

                        <button type="submit" disabled={processing} className="inline-flex w-full items-center justify-center gap-2 bg-teal-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-teal-700 disabled:cursor-not-allowed disabled:opacity-60">
                            <KeyRound aria-hidden="true" className="h-4 w-4" />
                            Crear usuario
                        </button>
                    </form>
                </section>}
            </div>
        </AuthenticatedLayout>
    );
}
