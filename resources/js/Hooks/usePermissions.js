import { usePage } from '@inertiajs/react';

export default function usePermissions() {
    const permissions = usePage().props.auth.permissions ?? [];

    return {
        permissions,
        can: (code) => permissions.includes(code),
    };
}
