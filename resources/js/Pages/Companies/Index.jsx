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

const columns = [
    { key: 'name', label: 'Empresa', className: 'font-medium text-slate-900' },
    { key: 'code', label: 'Codigo', className: 'font-mono text-xs text-slate-500' },
    {
        key: 'isActive',
        label: 'Estado',
        searchValue: (company) => (company.isActive ? 'Activa' : 'Inactiva'),
        render: (company) => (
            <span
                className={`inline-flex border px-2 py-1 text-xs font-medium ${
                    company.isActive
                        ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                        : 'border-slate-200 bg-slate-100 text-slate-600'
                }`}
            >
                {company.isActive ? 'Activa' : 'Inactiva'}
            </span>
        ),
    },
];

export default function Index({ companies }) {
    const rowActions = useRowActions('companies');
    const [editing, setEditing] = useState(null);
    const [open, setOpen] = useState(false);
    const { data, setData, post, patch, processing, errors, reset, clearErrors } = useForm({
        name: '',
        code: '',
        is_active: true,
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

    const openEdit = (company) => {
        clearErrors();
        setEditing(company);
        setData({ name: company.name, code: company.code, is_active: company.isActive });
        setOpen(true);
    };

    const submit = () => {
        const options = { preserveScroll: true, onSuccess: close };

        if (editing) {
            patch(route('companies.update', editing.id), options);
        } else {
            post(route('companies.store'), options);
        }
    };

    return (
        <AuthenticatedLayout
            header={
                <div>
                    <p className="text-sm font-medium text-teal-700">Administracion</p>
                    <h1 className="mt-1 text-2xl font-semibold text-slate-950">Empresas</h1>
                </div>
            }
        >
            <Head title="Empresas" />

            <section className="border border-slate-200 bg-white">
                <div className="flex items-center justify-between border-b border-slate-200 px-5 py-4 sm:px-6">
                    <div>
                        <h2 className="text-base font-semibold text-slate-950">Directorio</h2>
                        <p className="mt-1 text-sm text-slate-500">
                            {companies.length} empresas registradas
                        </p>
                    </div>
                    <CreateButton module="companies" onClick={openCreate} label="Nueva empresa" />
                </div>

                <DataTable
                    columns={columns}
                    rows={companies}
                    emptyMessage="Todavia no hay empresas."
                    actions={(company) => (
                        <ActionButtons module="companies" row={company} onEdit={openEdit} {...rowActions} />
                    )}
                />
            </section>

            <CrudModal
                show={open}
                title={editing ? 'Editar empresa' : 'Nueva empresa'}
                onClose={close}
                onSubmit={submit}
                processing={processing}
            >
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
                {editing && (
                    <label className="flex items-center gap-2 text-sm text-slate-700">
                        <input
                            type="checkbox"
                            checked={data.is_active}
                            onChange={(event) => setData('is_active', event.target.checked)}
                            className="rounded border-slate-300 text-teal-600 focus:ring-teal-500"
                        />
                        Empresa activa
                    </label>
                )}
            </CrudModal>
        </AuthenticatedLayout>
    );
}
