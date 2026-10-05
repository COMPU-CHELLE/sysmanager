import { router } from '@inertiajs/react';

export default function useRowActions(module) {
    const run = (method, name, row, message) => {
        if (message && !window.confirm(message)) {
            return;
        }

        router[method](route(`${module}.${name}`, row.id), {}, { preserveScroll: true });
    };

    return {
        onDelete: (row) => run('delete', 'destroy', row, 'Se enviara a la papelera. Continuar?'),
        onRestore: (row) => run('patch', 'restore', row),
        onForceDelete: (row) =>
            run('delete', 'force-delete', row, 'Se eliminara definitivamente. Esta accion no se puede deshacer. Continuar?'),
    };
}
