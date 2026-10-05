import ActionButtons, { CreateButton } from '@/Components/ActionButtons';
import CrudModal from '@/Components/CrudModal';
import DataTable from '@/Components/DataTable';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import useRowActions from '@/Hooks/useRowActions';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router, useForm } from '@inertiajs/react';
import { useMemo, useState } from 'react';

const selectClass =
    'mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500';

const emptyItem = { description: '', quantity: 1, unit_price: 0, tax: 0 };

function initialValue(field) {
    return field.type === 'items' ? [{ ...emptyItem }] : '';
}

function choicesFor(field, options) {
    return field.choices ?? options[field.options] ?? [];
}

export default function Index({ module, rows, options }) {
    const rowActions = useRowActions(module.key);
    const [editing, setEditing] = useState(null);
    const [open, setOpen] = useState(false);
    const [reply, setReply] = useState('');

    const blank = useMemo(
        () => Object.fromEntries(module.fields.map((field) => [field.name, initialValue(field)])),
        [module.fields],
    );

    const { data, setData, post, patch, processing, errors, clearErrors } = useForm(blank);

    const columns = module.columns.map((column) => {
        const field = module.fields.find((candidate) => candidate.name === column.key);

        if (!field?.choices) {
            return column;
        }

        const labelOf = (row) =>
            field.choices.find((choice) => choice.value === row[column.key])?.label ?? row[column.key];

        return { ...column, render: labelOf, searchValue: labelOf };
    });

    const close = () => {
        setOpen(false);
        setEditing(null);
        setData(blank);
        clearErrors();
    };

    const openCreate = () => {
        clearErrors();
        setEditing(null);
        setData(blank);
        setOpen(true);
    };

    const openEdit = (row) => {
        clearErrors();
        setEditing(row);
        setData(
            Object.fromEntries(
                module.fields.map((field) => [
                    field.name,
                    field.secret ? '' : (row[field.name] ?? initialValue(field)),
                ]),
            ),
        );
        setOpen(true);
    };

    const submit = () => {
        const requestOptions = { preserveScroll: true, onSuccess: close };

        if (editing) {
            patch(route(`${module.key}.update`, editing.id), requestOptions);
        } else {
            post(route(`${module.key}.store`), requestOptions);
        }
    };

    const updateItem = (index, key, value) =>
        setData(
            'items',
            data.items.map((item, position) => (position === index ? { ...item, [key]: value } : item)),
        );

    const renderItems = (field) => (
        <div key={field.name} className="space-y-2">
            <InputLabel value={field.label} />
            {data.items.map((item, index) => (
                <div key={index} className="grid grid-cols-12 gap-2">
                    <input
                        className="col-span-5 rounded-md border-slate-300 text-sm"
                        placeholder="Descripcion"
                        value={item.description}
                        onChange={(event) => updateItem(index, 'description', event.target.value)}
                    />
                    <input
                        type="number"
                        min="1"
                        className="col-span-2 rounded-md border-slate-300 text-sm"
                        placeholder="Cant."
                        value={item.quantity}
                        onChange={(event) => updateItem(index, 'quantity', event.target.value)}
                    />
                    <input
                        type="number"
                        min="0"
                        step="0.01"
                        className="col-span-2 rounded-md border-slate-300 text-sm"
                        placeholder="Precio"
                        value={item.unit_price}
                        onChange={(event) => updateItem(index, 'unit_price', event.target.value)}
                    />
                    <input
                        type="number"
                        min="0"
                        step="0.01"
                        className="col-span-2 rounded-md border-slate-300 text-sm"
                        placeholder="Impuesto"
                        value={item.tax ?? 0}
                        onChange={(event) => updateItem(index, 'tax', event.target.value)}
                    />
                    <button
                        type="button"
                        disabled={data.items.length === 1}
                        onClick={() =>
                            setData(
                                'items',
                                data.items.filter((_, position) => position !== index),
                            )
                        }
                        className="col-span-1 text-slate-500 hover:text-red-600 disabled:opacity-30"
                        aria-label="Quitar linea"
                    >
                        ×
                    </button>
                    {Object.entries(errors)
                        .filter(([key]) => key.startsWith(`items.${index}.`))
                        .map(([key, message]) => (
                            <InputError key={key} message={message} className="col-span-12" />
                        ))}
                </div>
            ))}
            <button
                type="button"
                onClick={() => setData('items', [...data.items, { ...emptyItem }])}
                className="text-sm font-medium text-teal-700 hover:text-teal-900"
            >
                + Agregar linea
            </button>
            <InputError message={errors.items} className="mt-2" />
        </div>
    );

    const currentTicket = module.key === 'tickets' && editing ? rows.find((row) => row.id === editing.id) : null;

    const sendReply = () => {
        router.post(
            route('tickets.messages.store', editing.id),
            { message: reply },
            { preserveScroll: true, onSuccess: () => setReply('') },
        );
    };

    const renderThread = () => (
        <div className="space-y-3 border-t border-slate-200 pt-4">
            <InputLabel value="Mensajes" />
            {(currentTicket?.messages ?? []).map((message) => (
                <div key={message.id} className="bg-slate-50 px-3 py-2 text-sm">
                    <p className="text-xs text-slate-500">
                        {message.user} · {message.createdAt}
                    </p>
                    <p className="mt-1 whitespace-pre-wrap text-slate-800">{message.message}</p>
                </div>
            ))}
            <textarea
                value={reply}
                rows={2}
                placeholder="Escribir mensaje..."
                className={selectClass}
                onChange={(event) => setReply(event.target.value)}
            />
            <button
                type="button"
                disabled={!reply.trim()}
                onClick={sendReply}
                className="text-sm font-medium text-teal-700 hover:text-teal-900 disabled:opacity-40"
            >
                Enviar mensaje
            </button>
        </div>
    );

    const renderField = (field) => {
        if (field.type === 'items') {
            return renderItems(field);
        }

        const id = `field-${field.name}`;
        const value = data[field.name] ?? '';
        const onChange = (event) => setData(field.name, event.target.value);
        let control;

        if (field.type === 'select') {
            const choices = choicesFor(field, options).filter(
                (choice) => !field.filterBy || String(choice.company_id) === String(data[field.filterBy]),
            );

            control = (
                <select id={id} value={value} className={selectClass} onChange={onChange} required={field.required}>
                    <option value="">Seleccione...</option>
                    {choices.map((choice) => (
                        <option key={choice.value} value={choice.value}>
                            {choice.label}
                        </option>
                    ))}
                </select>
            );
        } else if (field.type === 'textarea') {
            control = (
                <textarea
                    id={id}
                    value={value}
                    rows={3}
                    className={selectClass}
                    onChange={onChange}
                    required={field.required}
                />
            );
        } else {
            control = (
                <TextInput
                    id={id}
                    type={field.type}
                    step={field.type === 'number' ? 'any' : undefined}
                    value={value}
                    autoComplete={field.type === 'password' ? 'new-password' : 'off'}
                    placeholder={field.secret && editing ? 'Dejar vacio para conservar' : undefined}
                    className="mt-1 block w-full"
                    onChange={onChange}
                    required={field.required && !(field.secret && editing)}
                />
            );
        }

        return (
            <div key={field.name}>
                <InputLabel htmlFor={id} value={field.label} />
                {control}
                <InputError message={errors[field.name]} className="mt-2" />
            </div>
        );
    };

    return (
        <AuthenticatedLayout
            header={
                <div>
                    <p className="text-sm font-medium text-teal-700">{module.group}</p>
                    <h1 className="mt-1 text-2xl font-semibold text-slate-950">{module.title}</h1>
                </div>
            }
        >
            <Head title={module.title} />

            <section className="border border-slate-200 bg-white">
                <div className="flex items-center justify-between border-b border-slate-200 px-5 py-4 sm:px-6">
                    <div>
                        <h2 className="text-base font-semibold text-slate-950">Listado</h2>
                        <p className="mt-1 text-sm text-slate-500">{rows.length} registros</p>
                    </div>
                    {!module.readOnly && (
                        <CreateButton module={module.key} onClick={openCreate} label={`Nuevo ${module.singular}`} />
                    )}
                </div>

                <DataTable
                    columns={columns}
                    rows={rows}
                    emptyMessage="Todavia no hay registros."
                    actions={
                        module.readOnly
                            ? undefined
                            : (row) => (
                                  <div className="flex items-center justify-end">
                                      {module.key === 'asset-assignments' && !row.deletedAt && (
                                          <a
                                              href={route('asset-assignments.document', row.id)}
                                              target="_blank"
                                              rel="noreferrer"
                                              className="mr-2 text-sm font-medium text-teal-700 hover:text-teal-900"
                                          >
                                              Acta
                                          </a>
                                      )}
                                      <ActionButtons module={module.key} row={row} onEdit={openEdit} {...rowActions} />
                                  </div>
                              )
                    }
                />
            </section>

            <CrudModal
                show={open}
                title={editing ? `Editar ${module.singular}` : `Nuevo ${module.singular}`}
                onClose={close}
                onSubmit={submit}
                processing={processing}
            >
                {module.fields.map(renderField)}
                {currentTicket && renderThread()}
            </CrudModal>
        </AuthenticatedLayout>
    );
}
