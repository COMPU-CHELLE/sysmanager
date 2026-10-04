import { Link, usePage } from '@inertiajs/react';
import {
    Building2,
    LayoutDashboard,
    LogOut,
    Menu,
    Settings,
    ShieldCheck,
    UserRound,
    UsersRound,
    X,
} from 'lucide-react';
import { useState } from 'react';

const navigation = [
    {
        label: 'Panel',
        href: 'dashboard',
        active: 'dashboard',
        icon: LayoutDashboard,
    },
    {
        label: 'Perfil',
        href: 'profile.edit',
        active: 'profile.*',
        icon: UserRound,
    },
    {
        label: 'Empresas',
        href: 'companies.index',
        active: 'companies.*',
        icon: Building2,
    },
    {
        label: 'Usuarios',
        href: 'users.index',
        active: 'users.*',
        icon: UsersRound,
    },
    {
        label: 'Roles',
        href: 'roles.index',
        active: 'roles.*',
        icon: ShieldCheck,
    },
];

function Navigation({ onNavigate }) {
    return (
        <nav aria-label="Navegacion principal" className="space-y-1">
            {navigation.map(({ label, href, active, icon: Icon }) => (
                <Link
                    key={href}
                    href={route(href)}
                    onClick={onNavigate}
                    className={`flex items-center gap-3 rounded-md px-3 py-2.5 text-sm font-medium transition ${
                        route().current(active)
                            ? 'bg-teal-600 text-white shadow-sm'
                            : 'text-slate-300 hover:bg-slate-800 hover:text-white'
                    }`}
                >
                    <Icon aria-hidden="true" className="h-5 w-5" />
                    {label}
                </Link>
            ))}
        </nav>
    );
}

export default function AuthenticatedLayout({ header, children }) {
    const { auth } = usePage().props;
    const [showingNavigation, setShowingNavigation] = useState(false);
    const userInitial = auth.user.name.charAt(0).toUpperCase();

    const closeNavigation = () => setShowingNavigation(false);

    return (
        <div className="min-h-screen bg-slate-50 text-slate-900">
            <aside className="fixed inset-y-0 hidden w-64 flex-col bg-slate-950 px-4 py-5 lg:flex">
                <Link
                    href={route('dashboard')}
                    className="flex items-center gap-3 px-2 text-white"
                >
                    <span className="grid h-9 w-9 place-items-center rounded-md bg-teal-500 text-lg font-bold">
                        S
                    </span>
                    <span className="text-base font-semibold tracking-wide">
                        SysManager
                    </span>
                </Link>

                <div className="mt-10">
                    <p className="px-3 text-xs font-semibold uppercase tracking-wider text-slate-500">
                        Espacio de trabajo
                    </p>
                    <div className="mt-3">
                        <Navigation />
                    </div>
                </div>

                <div className="mt-auto border-t border-slate-800 pt-4">
                    <Link
                        href={route('profile.edit')}
                        className="flex items-center gap-3 rounded-md px-2 py-2 hover:bg-slate-800"
                    >
                        <span className="grid h-9 w-9 place-items-center rounded-full bg-slate-700 text-sm font-semibold text-white">
                            {userInitial}
                        </span>
                        <span className="min-w-0">
                            <span className="block truncate text-sm font-medium text-white">
                                {auth.user.name}
                            </span>
                            <span className="block truncate text-xs text-slate-400">
                                {auth.user.email}
                            </span>
                        </span>
                    </Link>
                </div>
            </aside>

            <div className="lg:pl-64">
                <header className="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-slate-200 bg-white px-4 sm:px-6 lg:px-8">
                    <div className="flex items-center gap-3">
                        <button
                            type="button"
                            onClick={() => setShowingNavigation(true)}
                            className="grid h-10 w-10 place-items-center rounded-md text-slate-600 hover:bg-slate-100 lg:hidden"
                            aria-label="Abrir navegacion"
                            title="Abrir navegacion"
                        >
                            <Menu aria-hidden="true" className="h-5 w-5" />
                        </button>
                        <span className="text-base font-semibold text-slate-900 lg:hidden">
                            SysManager
                        </span>
                    </div>

                    <div className="flex items-center gap-1">
                        <Link
                            href={route('profile.edit')}
                            className="grid h-10 w-10 place-items-center rounded-md text-slate-500 hover:bg-slate-100 hover:text-slate-900"
                            aria-label="Configuracion de perfil"
                            title="Configuracion de perfil"
                        >
                            <Settings aria-hidden="true" className="h-5 w-5" />
                        </Link>
                        <Link
                            href={route('logout')}
                            method="post"
                            as="button"
                            className="grid h-10 w-10 place-items-center rounded-md text-slate-500 hover:bg-red-50 hover:text-red-600"
                            aria-label="Cerrar sesion"
                            title="Cerrar sesion"
                        >
                            <LogOut aria-hidden="true" className="h-5 w-5" />
                        </Link>
                    </div>
                </header>

                <main className="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                    {header && <div className="mb-8">{header}</div>}
                    {children}
                </main>
            </div>

            {showingNavigation && (
                <div className="fixed inset-0 z-50 lg:hidden">
                    <button
                        type="button"
                        onClick={closeNavigation}
                        className="absolute inset-0 w-full bg-slate-950/50"
                        aria-label="Cerrar navegacion"
                    />
                    <aside className="relative flex h-full w-72 flex-col bg-slate-950 px-4 py-5 shadow-xl">
                        <div className="flex items-center justify-between">
                            <Link
                                href={route('dashboard')}
                                onClick={closeNavigation}
                                className="flex items-center gap-3 text-white"
                            >
                                <span className="grid h-9 w-9 place-items-center rounded-md bg-teal-500 text-lg font-bold">
                                    S
                                </span>
                                <span className="text-base font-semibold tracking-wide">
                                    SysManager
                                </span>
                            </Link>
                            <button
                                type="button"
                                onClick={closeNavigation}
                                className="grid h-10 w-10 place-items-center rounded-md text-slate-300 hover:bg-slate-800 hover:text-white"
                                aria-label="Cerrar navegacion"
                                title="Cerrar navegacion"
                            >
                                <X aria-hidden="true" className="h-5 w-5" />
                            </button>
                        </div>
                        <div className="mt-10">
                            <Navigation onNavigate={closeNavigation} />
                        </div>
                    </aside>
                </div>
            )}
        </div>
    );
}
