import ActionButtons, { CreateButton } from '@/Components/ActionButtons';
import CrudModal from '@/Components/CrudModal';
import DataTable from '@/Components/DataTable';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import usePermissions from '@/Hooks/usePermissions';
import useRowActions from '@/Hooks/useRowActions';
import { Head, useForm } from '@inertiajs/react';
import { useState } from 'react';

function toggleSelection(items, itemId) {
    return items.includes(itemId)
        ? items.filter((id) => id !== itemId)
        : [...items, itemId];
}

const permissionSections = [
    {
        name: 'Administracion',
        modules: ['companies', 'users', 'roles', 'plans', 'audit-logs'],
    },
    {
        name: 'Operacion',
        modules: [
            'branches',
            'employees',
            'assets',
            'asset-assignments',
            'maintenances',
            'credentials',
            'invoices',
            'tasks',
            'tickets',
        ],
    },
];

const columns = [
    {
        key: 'name',
        label: 'Rol',
        searchValue: (role) => `${role.name} ${role.code}`,
        render: (role) => (
            <div>
                <p className="font-medium text-slate-900">{role.name}</p>
                <p className="font-mono text-xs text-slate-500">{role.code}</p>
            </div>
        ),
    },
    {
        key: 'company',
        label: 'Alcance',
        render: (role) => role.company ?? 'Global',
    },
    {
        key: 'permissions',
        label: 'Permisos',
        searchValue: (role) => role.permissions.map((permission) => permission.name).join(' '),
        render: (role) => (
            <span
                className={`inline-flex border px-2 py-1 text-xs font-medium ${
                    role.permissions.length === 0
                        ? 'border-slate-200 bg-slate-100 text-slate-500'
                        : 'border-teal-200 bg-teal-50 text-teal-700'
                }`}
            >
                {role.permissions.length} / {role.totalPermissions}
            </span>
        ),
    },
];

export default function Index({ roles, companies, permissions, modules, actions }) {
    const groupedPermissions = Object.entries(modules)
        .map(([moduleCode, moduleName]) => ({
            code: moduleCode,
            name: moduleName,
            items: permissions.filter((permission) => permission.code.split('.')[0] === moduleCode),
        }))
        .filter((group) => group.items.length > 0);
    const groupedPermissionSections = permissionSections
        .map((section) => ({
            ...section,
            groups: groupedPermissions.filter((group) => section.modules.includes(group.code)),
        }))
        .filter((section) => section.groups.length > 0);

    const { can } = usePermissions();
    const rowActions = useRowActions('roles');
    const [editing, setEditing] = useState(null);
    const [open, setOpen] = useState(false);
    const { data, setData, post, patch, processing, errors, reset, clearErrors } = useForm({
        name: '',
        code: '',
        company_id: '',
        permission_ids: [],
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

    const openEdit = (role) => {
        clearErrors();
        setEditing(role);
        setData({
            name: role.name,
            code: role.code,
            company_id: role.companyId ?? '',
            permission_ids: role.permissions.map((permission) => permission.id),
        });
        setOpen(true);
    };

    const submit = () => {
        const options = { preserveScroll: true, onSuccess: close };

        if (editing) {
            patch(route('roles.update', editing.id), options);
        } else {
            post(route('roles.store'), options);
        }
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

            <section className="border border-slate-200 bg-white">
                <div className="flex items-center justify-between border-b border-slate-200 px-5 py-4 sm:px-6">
                    <div>
                        <h2 className="text-base font-semibold text-slate-950">Roles configurados</h2>
                        <p className="mt-1 text-sm text-slate-500">{roles.length} roles disponibles</p>
                    </div>
                    <CreateButton module="roles" onClick={openCreate} label="Nuevo rol" />
                </div>

                <DataTable
                    columns={columns}
                    rows={roles}
                    emptyMessage="No hay roles configurados."
                    actions={(role) => (
                        <ActionButtons module="roles" row={role} onEdit={openEdit} {...rowActions} />
                    )}
                />
            </section>

            {(can('roles.create') || can('roles.update')) && (
                <CrudModal
                    show={open}
                    title={editing ? 'Editar rol' : 'Nuevo rol'}
                    onClose={close}
                    onSubmit={submit}
                    processing={processing}
                    maxWidth="5xl"
                >
                    <div className="grid min-w-0 gap-6 lg:grid-cols-[minmax(14rem,0.8fr)_minmax(0,1.8fr)]">
                        <div className="space-y-5">
                            <div>
                                <InputLabel htmlFor="name" value="Nombre" />
                                <TextInput
                                    id="name"
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
                                    value={data.code}
                                    className="mt-1 block w-full uppercase"
                                    onChange={(event) => setData('code', event.target.value.toUpperCase())}
                                    required
                                />
                                <InputError message={errors.code} className="mt-2" />
                            </div>
                            <div>
                                <InputLabel htmlFor="company_id" value="Alcance" />
                                <select
                                    id="company_id"
                                    value={data.company_id}
                                    onChange={(event) => setData('company_id', event.target.value)}
                                    className="mt-1 block w-full border-slate-300 text-sm text-slate-700 focus:border-teal-500 focus:ring-teal-500"
                                >
                                    <option value="">Global</option>
                                    {companies.map((company) => (
                                        <option key={company.id} value={company.id}>
                                            {company.name}
                                        </option>
                                    ))}
                                </select>
                                <InputError message={errors.company_id} className="mt-2" />
                            </div>
                        </div>

                        <fieldset className="min-w-0">
                            <legend className="text-sm font-medium text-slate-700">Permisos</legend>
                            <div className="mt-2 space-y-5 lg:max-h-[calc(100vh-16rem)] lg:overflow-y-auto lg:pr-2">
                                {groupedPermissionSections.map((section) => (
                                    <section key={section.name}>
                                        <h3 className="border-b border-slate-200 pb-2 text-sm font-semibold text-slate-800">
                                            {section.name}
                                        </h3>
                                        <div className="mt-3 grid gap-3 sm:grid-cols-2">
                                            {section.groups.map((group) => {
                                                const ids = group.items.map((permission) => permission.id);
                                                const selected = ids.filter((id) => data.permission_ids.includes(id)).length;
                                                const allSelected = selected === ids.length;

                                                return (
                                                    <div key={group.code} className="min-w-0 border border-slate-200">
                                                        <label className="flex items-center justify-between gap-2 border-b border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-800">
                                                            <span className="flex min-w-0 items-center gap-2">
                                                                <input
                                                                    type="checkbox"
                                                                    checked={allSelected}
                                                                    onChange={() =>
                                                                        setData(
                                                                            'permission_ids',
                                                                            allSelected
                                                                                ? data.permission_ids.filter((id) => !ids.includes(id))
                                                                                : [...new Set([...data.permission_ids, ...ids])],
                                                                        )
                                                                    }
                                                                    className="rounded border-slate-300 text-teal-600 focus:ring-teal-500"
                                                                />
                                                                <span className="truncate">{group.name}</span>
                                                            </span>
                                                            <span className="text-xs font-normal text-slate-500">
                                                                {selected}/{ids.length}
                                                            </span>
                                                        </label>
                                                        <div className="grid gap-y-2 p-3">
                                                            {group.items.map((permission) => (
                                                                <label
                                                                    key={permission.id}
                                                                    className="flex min-w-0 items-start gap-2 text-sm text-slate-600"
                                                                >
                                                                    <input
                                                                        type="checkbox"
                                                                        checked={data.permission_ids.includes(permission.id)}
                                                                        onChange={() =>
                                                                            setData(
                                                                                'permission_ids',
                                                                                toggleSelection(data.permission_ids, permission.id),
                                                                            )
                                                                        }
                                                                        className="mt-0.5 shrink-0 rounded border-slate-300 text-teal-600 focus:ring-teal-500"
                                                                    />
                                                                    <span className="break-words">
                                                                        {actions[permission.code.split('.')[1]] ?? permission.name}
                                                                    </span>
                                                                </label>
                                                            ))}
                                                        </div>
                                                    </div>
                                                );
                                            })}
                                        </div>
                                    </section>
                                ))}
                            </div>
                            <InputError message={errors['permission_ids.0'] ?? errors.permission_ids} className="mt-2" />
                        </fieldset>
                    </div>
                </CrudModal>
            )}
        </AuthenticatedLayout>
    );
}
