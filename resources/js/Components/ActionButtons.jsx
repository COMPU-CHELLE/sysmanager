import usePermissions from '@/Hooks/usePermissions';
import { Pencil, Plus, RotateCcw, Trash2, XOctagon } from 'lucide-react';

const base =
    'inline-grid h-9 w-9 place-items-center text-slate-500 hover:bg-slate-100 hover:text-slate-900';

export function CreateButton({ module, onClick, label = 'Nuevo' }) {
    const { can } = usePermissions();

    if (!can(`${module}.create`)) {
        return null;
    }

    return (
        <button
            type="button"
            onClick={onClick}
            className="inline-flex items-center gap-2 rounded-md bg-teal-600 px-3 py-2 text-sm font-medium text-white hover:bg-teal-700"
        >
            <Plus aria-hidden="true" className="h-4 w-4" />
            {label}
        </button>
    );
}

export default function ActionButtons({
    module,
    row,
    onEdit,
    onDelete,
    onRestore,
    onForceDelete,
}) {
    const { can } = usePermissions();
    const trashed = Boolean(row.deletedAt);

    if (row.locked) {
        return null;
    }

    const items = [
        { action: 'update', handler: onEdit, icon: Pencil, label: 'Editar', show: !trashed },
        { action: 'delete', handler: onDelete, icon: Trash2, label: 'Eliminar', show: !trashed },
        { action: 'restore', handler: onRestore, icon: RotateCcw, label: 'Restaurar', show: trashed },
        { action: 'force-delete', handler: onForceDelete, icon: XOctagon, label: 'Eliminar definitivamente', show: trashed },
    ].filter(
        ({ action, handler, show }) => show && handler && can(`${module}.${action}`),
    );

    return (
        <div className="flex justify-end">
            {items.map(({ action, handler, icon: Icon, label }) => (
                <button
                    key={action}
                    type="button"
                    onClick={() => handler(row)}
                    className={base}
                    aria-label={label}
                    title={label}
                >
                    <Icon aria-hidden="true" className="h-4 w-4" />
                </button>
            ))}
        </div>
    );
}
