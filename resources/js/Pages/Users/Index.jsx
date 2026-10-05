import ActionButtons, { CreateButton } from '@/Components/ActionButtons';
import CrudModal from '@/Components/CrudModal';
import DataTable from '@/Components/DataTable';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import useRowActions from '@/Hooks/useRowActions';
import { Head, useForm } from '@inertiajs/react';
import { useState } from 'react';

function toggleSelection(items, itemId) {
    return items.includes(itemId)
        ? items.filter((id) => id !== itemId)
        : [...items, itemId];
}

const badge = (tone, text, key) => (
    <span key={key} className={`border px-2 py-1 text-xs ${tone}`}>
        {text}
    </span>
);

const columns = [
    {
        key: 'name',
        label: 'Usuario',
        searchValue: (user) => `${user.name} ${user.email}`,
        render: (user) => (
            <div>
                <p className="font-medium text-slate-900">{user.name}</p>
                <p className="text-slate-500">{user.email}</p>
            </div>
        ),
    },
    { key: 'code', label: 'Codigo', className: 'font-mono text-xs text-slate-500' },
    {
        key: 'companies',
        label: 'Empresas',
        searchValue: (user) => user.companies.map((company) => company.name).join(' '),
        render: (user) => (
            <div className="flex flex-wrap gap-2">
                {user.companies.map((company) =>
                    badge('border-slate-200 bg-slate-50 text-slate-600', company.name, company.id),
                )}
            </div>
        ),
    },
    {
        key: 'roles',
        label: 'Roles',
        searchValue: (user) => user.roles.map((role) => role.name).join(' '),
        render: (user) => (
            <div className="flex flex-wrap gap-2">
                {user.roles.map((role) =>
                    badge('border-teal-200 bg-teal-50 text-teal-700', role.name, role.id),
                )}
            </div>
        ),
    },
];

export default function Index({ users, companies, roles, branches }) {
    const rowActions = useRowActions('users');
    const [editing, setEditing] = useState(null);
    const [open, setOpen] = useState(false);
    const { data, setData, post, patch, processing, errors, reset, clearErrors } = useForm({
        name: '',
        code: '',
        email: '',
        password: '',
        password_confirmation: '',
        branch_id: '',
        company_ids: [],
        role_ids: [],
    });

    const close = () => {
        setOpen(false);
        setEditing(null);
        reset();
        clearErrors();
    };

    const openCreate = () => {
        close();
        setOpen(true);
    };

    const openEdit = (user) => {
        clearErrors();
        setEditing(user);
        setData({
            name: user.name,
            code: user.code,
            email: user.email,
            password: '',
            password_confirmation: '',
            branch_id: user.branchId ?? '',
            company_ids: user.companies.map((company) => company.id),
            role_ids: user.roles.map((role) => role.id),
        });
        setOpen(true);
    };

    const submit = () => {
        const options = { preserveScroll: true, onSuccess: close };

        if (editing) {
            patch(route('users.update', editing.id), options);
        } else {
            post(route('users.store'), options);
        }
    };

    const field = (id, label, type = 'text', extra = {}) => (
        <div>
            <InputLabel htmlFor={id} value={label} />
            <TextInput
                id={id}
                type={type}
                value={data[id]}
                className="mt-1 block w-full"
                onChange={(event) => setData(id, event.target.value)}
                required={!(editing && id.startsWith('password'))}
                {...extra}
            />
            <InputError message={errors[id]} className="mt-2" />
        </div>
    );

    return (
        <AuthenticatedLayout
            header={
                <div>
                    <p className="text-sm font-medium text-teal-700">Administracion</p>
                    <h1 className="mt-1 text-2xl font-semibold text-slate-950">Usuarios</h1>
                </div>
            }
        >
            <Head title="Usuarios" />

            <section className="border border-slate-200 bg-white">
                <div className="flex items-center justify-between border-b border-slate-200 px-5 py-4 sm:px-6">
                    <div>
                        <h2 className="text-base font-semibold text-slate-950">Directorio</h2>
                        <p className="mt-1 text-sm text-slate-500">
                            {users.length} usuarios registrados
                        </p>
                    </div>
                    <CreateButton module="users" onClick={openCreate} label="Nuevo usuario" />
                </div>

                <DataTable
                    columns={columns}
                    rows={users}
                    emptyMessage="Todavia no hay usuarios."
                    actions={(user) => (
                        <ActionButtons module="users" row={user} onEdit={openEdit} {...rowActions} />
                    )}
                />
            </section>

            <CrudModal
                show={open}
                title={editing ? 'Editar usuario' : 'Nuevo usuario'}
                onClose={close}
                onSubmit={submit}
                processing={processing}
                maxWidth="xl"
            >
                {field('name', 'Nombre')}
                {field('code', 'Usuario')}
                {field('email', 'Correo', 'email')}
                {field('password', editing ? 'Nueva contrasena (opcional)' : 'Contrasena', 'password', { autoComplete: 'new-password' })}
                {field('password_confirmation', 'Confirmar contrasena', 'password', {
                    autoComplete: 'new-password',
                })}

                <div>
                    <InputLabel htmlFor="branch_id" value="Sucursal" />
                    <select
                        id="branch_id"
                        value={data.branch_id}
                        onChange={(event) => setData('branch_id', event.target.value)}
                        className="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                    >
                        <option value="">Sin sucursal</option>
                        {branches.map((branch) => (
                            <option key={branch.id} value={branch.id}>
                                {branch.name}
                            </option>
                        ))}
                    </select>
                    <InputError message={errors.branch_id} className="mt-2" />
                </div>

                <fieldset>
                    <legend className="text-sm font-medium text-slate-700">Empresas activas</legend>
                    <div className="mt-2 space-y-2">
                        {companies.length === 0 && (
                            <p className="text-sm text-slate-500">No hay empresas activas.</p>
                        )}
                        {companies.map((company) => (
                            <label key={company.id} className="flex items-center gap-2 text-sm text-slate-600">
                                <input
                                    type="checkbox"
                                    checked={data.company_ids.includes(company.id)}
                                    onChange={() =>
                                        setData('company_ids', toggleSelection(data.company_ids, company.id))
                                    }
                                    className="rounded border-slate-300 text-teal-600 focus:ring-teal-500"
                                />
                                {company.name}
                            </label>
                        ))}
                    </div>
                    <InputError message={errors['company_ids.0'] ?? errors.company_ids} className="mt-2" />
                </fieldset>

                <fieldset>
                    <legend className="text-sm font-medium text-slate-700">Roles</legend>
                    <div className="mt-2 space-y-2">
                        {roles.length === 0 && (
                            <p className="text-sm text-slate-500">No hay roles disponibles.</p>
                        )}
                        {roles.map((role) => (
                            <label key={role.id} className="flex items-center gap-2 text-sm text-slate-600">
                                <input
                                    type="checkbox"
                                    checked={data.role_ids.includes(role.id)}
                                    onChange={() =>
                                        setData('role_ids', toggleSelection(data.role_ids, role.id))
                                    }
                                    className="rounded border-slate-300 text-teal-600 focus:ring-teal-500"
                                />
                                <span>{role.name}</span>
                                <span className="text-xs text-slate-400">{role.company ?? 'Global'}</span>
                            </label>
                        ))}
                    </div>
                    <InputError message={errors['role_ids.0'] ?? errors.role_ids} className="mt-2" />
                </fieldset>
            </CrudModal>
        </AuthenticatedLayout>
    );
}
