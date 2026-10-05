import { Head, Link, usePage } from '@inertiajs/react';
import {
    ArrowLeft,
    ArrowUpRight,
    BookOpen,
    Building2,
    Check,
    Menu,
    ClipboardList,
    KeyRound,
    LogOut,
    Settings,
    ShieldCheck,
} from 'lucide-react';
import { useState } from 'react';

const actions = [
    {
        code: 'view',
        name: 'Consultar',
        method: 'GET',
        path: '',
        description: 'Ver los registros disponibles dentro del alcance de tu empresa y consultar su informacion.',
    },
    {
        code: 'create',
        name: 'Crear',
        method: 'POST',
        path: '',
        description: 'Agregar un registro usando el formulario del modulo. Los campos obligatorios se validan antes de guardar.',
    },
    {
        code: 'update',
        name: 'Editar',
        method: 'PATCH',
        path: '/{id}',
        description: 'Abrir una fila para modificar sus datos. Los cambios se guardan al confirmar el formulario.',
    },
    {
        code: 'delete',
        name: 'Eliminar',
        method: 'DELETE',
        path: '/{id}',
        description: 'Enviar el registro a eliminados. No se borra definitivamente y puede restaurarse si tienes permiso.',
    },
    {
        code: 'restore',
        name: 'Restaurar',
        method: 'PATCH',
        path: '/{id}/restore',
        description: 'Recuperar un registro eliminado. Este permiso tambien habilita que los eliminados aparezcan en la tabla.',
    },
    {
        code: 'force-delete',
        name: 'Eliminar definitivamente',
        method: 'DELETE',
        path: '/{id}/force',
        description: 'Borrar el registro de forma permanente. Esta accion no se puede deshacer.',
    },
];

const sections = [
    {
        name: 'Administracion',
        icon: Building2,
        modules: [
            {
                code: 'companies',
                name: 'Empresas',
                route: 'companies.index',
                description: 'Registra las empresas cliente y mantiene sus datos y estado. Soporte administra todas las empresas; otros roles trabajan solo con las empresas a las que pertenecen.',
                fields: 'Codigo, nombre, datos de contacto y estado de la empresa.',
            },
            {
                code: 'users',
                name: 'Usuarios',
                route: 'users.index',
                description: 'Administra las cuentas que acceden al sistema y su asociacion con empresas y roles.',
                fields: 'Nombre, codigo de acceso, correo, empresas asociadas y roles. La contrasena se establece o cambia de manera segura.',
                notes: 'El Administrador solo puede gestionar usuarios de su empresa y no puede administrar cuentas de Administrador o Soporte.',
            },
            {
                code: 'roles',
                name: 'Roles',
                route: 'roles.index',
                description: 'Define grupos de permisos para controlar que modulos y acciones puede usar cada cuenta.',
                fields: 'Nombre, codigo, alcance global o empresa y permisos agrupados por modulo.',
                notes: 'No se puede asignar el rol global de Soporte desde la administracion normal de roles.',
            },
            {
                code: 'audit-logs',
                name: 'Auditoria',
                route: 'audit-logs.index',
                description: 'Consulta eventos de cambios registrados por la aplicacion para dar seguimiento a la actividad.',
                fields: 'Accion, usuario, modulo, registro afectado y fecha del evento, segun la informacion disponible.',
                readOnly: true,
            },
        ],
    },
    {
        name: 'Operacion',
        icon: ClipboardList,
        modules: [
            {
                code: 'branches',
                name: 'Sucursales',
                route: 'branches.index',
                description: 'Organiza las ubicaciones fisicas asociadas a cada empresa.',
                fields: 'Empresa, codigo, nombre, direccion y correo.',
            },
            {
                code: 'employees',
                name: 'Empleados',
                route: 'employees.index',
                description: 'Mantiene el directorio de personas a quienes se pueden asignar activos o tareas.',
                fields: 'Empresa, sucursal, nombre, cargo, correo y telefono.',
            },
            {
                code: 'assets',
                name: 'Activos',
                route: 'assets.index',
                description: 'Controla el inventario de equipos y sus especificaciones tecnicas.',
                fields: 'Empresa, sucursal, nombre, tipo, serial, marca, modelo, compra, costo y detalles de hardware.',
            },
            {
                code: 'asset-assignments',
                name: 'Asignaciones',
                route: 'asset-assignments.index',
                description: 'Registra la entrega de un activo a un empleado y su devolucion.',
                fields: 'Activo, empleado, fecha de asignacion y fecha de devolucion.',
                special: [
                    {
                        name: 'Generar acta',
                        method: 'GET',
                        path: '/{id}/document',
                        permission: 'view',
                        description: 'Abre un documento imprimible de la asignacion con un numero de acta.',
                    },
                ],
            },
            {
                code: 'maintenances',
                name: 'Mantenimientos',
                route: 'maintenances.index',
                description: 'Registra intervenciones realizadas a los activos y sus costos.',
                fields: 'Activo, descripcion del trabajo, fecha y costo.',
            },
            {
                code: 'credentials',
                name: 'Credenciales',
                route: 'credentials.index',
                description: 'Guarda referencias de accesos de la empresa y su modo de uso.',
                fields: 'Nombre, tipo, URL, usuario, contrasena, modo de acceso y notas.',
                notes: 'Las contrasenas no se muestran en la tabla ni se devuelven al navegador al consultar registros.',
            },
            {
                code: 'invoices',
                name: 'Facturas',
                route: 'invoices.index',
                description: 'Organiza facturas de proveedores por empresa y sucursal.',
                fields: 'Numero, proveedor, fecha, categoria y una o mas lineas con descripcion, cantidad, precio e impuesto.',
                notes: 'El total de cada linea se calcula en el servidor a partir de cantidad, precio e impuesto.',
            },
            {
                code: 'tasks',
                name: 'Tareas',
                route: 'tasks.index',
                description: 'Asigna trabajo y da seguimiento a su estado y fecha limite.',
                fields: 'Empresa, titulo, descripcion, estado, prioridad, responsable, fecha limite y solucion.',
            },
            {
                code: 'tickets',
                name: 'Tickets',
                route: 'tickets.index',
                description: 'Registra solicitudes de soporte y conserva el hilo de mensajes relacionado.',
                fields: 'Empresa, titulo y estado: abierto, en progreso o cerrado.',
                special: [
                    {
                        name: 'Agregar mensaje',
                        method: 'POST',
                        path: '/{id}/messages',
                        permission: 'update',
                        description: 'Agrega una respuesta al hilo del ticket para documentar la conversacion.',
                    },
                ],
            },
        ],
    },
];

function permissionFor(action, moduleCode) {
    return `${moduleCode}.${action}`;
}

function ModuleActions({ module, permissions }) {
    const moduleActions = actions.filter((action) =>
        permissions.includes(permissionFor(action.code, module.code)),
    );

    return (
        <div className="space-y-3">
            {moduleActions.map((action) => (
                <article key={action.code} className="border border-slate-200 bg-white p-4">
                    <div className="flex flex-wrap items-center gap-2">
                        <h4 className="font-semibold text-slate-900">{action.name}</h4>
                        <code className="bg-slate-50 px-2 py-1 text-xs text-slate-600">
                            {action.method} /{module.code}{action.path}
                        </code>
                    </div>
                    <p className="mt-2 text-sm leading-6 text-slate-600">{action.description}</p>
                </article>
            ))}
            {module.special
                ?.filter((action) => permissions.includes(permissionFor(action.permission, module.code)))
                .map((action) => (
                    <article key={action.name} className="border border-teal-200 bg-teal-50/50 p-4">
                        <div className="flex flex-wrap items-center gap-2">
                            <h4 className="font-semibold text-slate-900">{action.name}</h4>
                            <code className="bg-white px-2 py-1 text-xs text-slate-600">
                                {action.method} /{module.code}{action.path}
                            </code>
                        </div>
                        <p className="mt-2 text-sm leading-6 text-slate-600">{action.description}</p>
                    </article>
                ))}
            {moduleActions.length === 0 && (
                <p className="border border-slate-200 bg-white p-4 text-sm text-slate-600">
                    Tu rol no tiene acciones habilitadas para este modulo.
                </p>
            )}
        </div>
    );
}

export default function Index() {
    const { auth } = usePage().props;
    const permissions = auth.permissions ?? [];
    const isSupport = auth.isSupport;
    const roleName = isSupport
        ? 'Soporte'
        : permissions.includes('users.view')
          ? 'Administrador'
          : 'Usuario';
    const visibleSections = sections
        .map((section) => ({
            ...section,
            modules: section.modules.filter((module) =>
                permissions.includes(permissionFor('view', module.code)),
            ),
        }))
        .filter((section) =>
            section.modules.length > 0 &&
            (!isSupport || section.name === 'Administracion'),
        );
    const visibleModules = visibleSections.flatMap((section) => section.modules);
    const [selectedCode, setSelectedCode] = useState(null);
    const [showingMenu, setShowingMenu] = useState(false);
    const selectedModule =
        visibleModules.find((module) => module.code === selectedCode) ?? visibleModules[0];

    const sidebar = (
        <>
            <Link href={route('dashboard')} className="flex items-center gap-3 px-2">
                <span className="grid h-10 w-10 place-items-center rounded-md bg-teal-600 text-lg font-bold text-white">
                    S
                </span>
                <span>
                    <span className="block text-sm font-semibold text-white">SysManager</span>
                    <span className="block text-xs text-slate-400">Documentacion</span>
                </span>
            </Link>
            <Link
                href={route('dashboard')}
                className="mt-6 flex items-center gap-2 rounded-md px-3 py-2 text-sm text-slate-300 hover:bg-slate-800 hover:text-white"
            >
                <ArrowLeft aria-hidden="true" className="h-4 w-4" />
                Volver al sistema
            </Link>
            <nav aria-label="Secciones de documentacion" className="mt-4 flex-1 space-y-5 overflow-y-auto">
                {visibleSections.map((section) => {
                    const SectionIcon = section.icon;

                    return (
                        <section key={section.name}>
                            <h2 className="mb-2 flex items-center gap-2 px-3 text-xs font-bold uppercase tracking-wider text-slate-500">
                                <SectionIcon aria-hidden="true" className="h-4 w-4" />
                                {section.name}
                            </h2>
                            <div className="space-y-1">
                                {section.modules.map((module) => {
                                    const isSelected = selectedModule?.code === module.code;

                                    return (
                                        <button
                                            key={module.code}
                                            type="button"
                                            onClick={() => {
                                                setSelectedCode(module.code);
                                                setShowingMenu(false);
                                            }}
                                            aria-current={isSelected ? 'page' : undefined}
                                            className={`flex w-full items-center rounded-md px-3 py-2 text-left text-sm font-medium transition ${
                                                isSelected
                                                    ? 'bg-teal-600 text-white'
                                                    : 'text-slate-300 hover:bg-slate-800 hover:text-white'
                                            }`}
                                        >
                                            {module.name}
                                        </button>
                                    );
                                })}
                            </div>
                        </section>
                    );
                })}
            </nav>
        </>
    );

    return (
        <div className="min-h-screen bg-slate-50 text-slate-900">
            <Head title="Documentacion del sistema" />
            <aside className="fixed inset-y-0 left-0 hidden w-64 flex-col bg-slate-950 px-4 py-5 lg:flex">
                {sidebar}
            </aside>
            {showingMenu && (
                <div className="fixed inset-0 z-40 lg:hidden">
                    <div className="absolute inset-0 bg-slate-900/60" onClick={() => setShowingMenu(false)} />
                    <aside className="relative flex h-full w-72 max-w-[85%] flex-col bg-slate-950 px-4 py-5">
                        {sidebar}
                    </aside>
                </div>
            )}
            <div className="lg:pl-64">
                <header className="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200 bg-white px-4 sm:px-6">
                    <div className="flex items-center gap-3">
                        <button
                            type="button"
                            onClick={() => setShowingMenu(true)}
                            className="inline-flex h-10 w-10 items-center justify-center rounded-md text-slate-600 hover:bg-slate-100 lg:hidden"
                            aria-label="Abrir menu"
                        >
                            <Menu aria-hidden="true" className="h-5 w-5" />
                        </button>
                        <span className="flex items-center gap-2 text-sm font-semibold text-slate-900">
                            <BookOpen aria-hidden="true" className="h-5 w-5 text-teal-700" />
                            Documentacion del sistema
                        </span>
                    </div>
                    <div className="flex items-center gap-2">
                        <span className="hidden rounded-full bg-teal-50 px-3 py-1 text-xs font-medium text-teal-800 sm:inline-flex">
                            {roleName}
                        </span>
                        <Link
                            href={route('profile.edit')}
                            className="inline-flex h-10 items-center gap-2 rounded-md px-3 text-sm font-medium text-slate-600 hover:bg-slate-100"
                            title="Configuracion de perfil"
                        >
                            <Settings aria-hidden="true" className="h-5 w-5" />
                            <span className="hidden md:inline">Configuracion</span>
                        </Link>
                        <Link
                            href={route('logout')}
                            method="post"
                            as="button"
                            className="inline-flex h-10 items-center gap-2 rounded-md px-3 text-sm font-medium text-slate-600 hover:bg-red-50 hover:text-red-700"
                        >
                            <LogOut aria-hidden="true" className="h-5 w-5" />
                            <span className="hidden md:inline">Salir</span>
                        </Link>
                    </div>
                </header>
                <main className="min-w-0 p-4 sm:p-6 lg:p-8">
                    {selectedModule ? (
                        <div className="mb-3 mx-auto max-w-4xl space-y-8">
                            <header className="border-b border-slate-200 pb-6">
                                <p className="text-sm font-medium text-teal-700">
                                    {visibleSections.find((section) =>
                                        section.modules.some((module) => module.code === selectedModule.code),
                                    )?.name}
                                </p>
                                <div className="mt-2 flex flex-wrap items-start justify-between gap-4">
                                    <div>
                                        <h2 className="text-2xl font-semibold text-slate-950">
                                            {selectedModule.name}
                                        </h2>
                                        <p className="mt-3 max-w-2xl text-sm leading-6 text-slate-600">
                                            {selectedModule.description}
                                        </p>
                                    </div>
                                    <Link
                                        href={route(selectedModule.route)}
                                        className="inline-flex shrink-0 items-center gap-2 rounded-md bg-teal-700 px-4 py-2 text-sm font-medium text-white hover:bg-teal-800"
                                    >
                                        Abrir modulo
                                        <ArrowUpRight aria-hidden="true" className="h-4 w-4" />
                                    </Link>
                                </div>
                            </header>
                            <section className="mt-3 border border-slate-200 bg-white p-5">
                                <div className="flex items-center gap-2">
                                    <KeyRound aria-hidden="true" className="h-5 w-5 text-teal-700" />
                                    <h3 className="font-semibold text-slate-900">Informacion del modulo</h3>
                                </div>
                                <p className="mt-3 text-sm leading-6 text-slate-600">
                                    <span className="font-medium text-slate-800">Datos principales: </span>
                                    {selectedModule.fields}
                                </p>
                                {selectedModule.notes && (
                                    <p className="mt-3 text-sm leading-6 text-slate-600">
                                        <span className="font-medium text-slate-800">Nota: </span>
                                        {selectedModule.notes}
                                    </p>
                                )}
                                {selectedModule.readOnly && (
                                    <p className="mt-3 inline-flex items-center gap-2 text-sm text-slate-600">
                                        <Check aria-hidden="true" className="h-4 w-4 text-teal-700" />
                                        Modulo de consulta; no incluye acciones de escritura.
                                    </p>
                                )}
                            </section>

                            <section>
                                <h3 className="mb-3 mt-2 text-lg font-semibold text-slate-900">
                                    Funciones disponibles para {roleName}
                                </h3>
                                <ModuleActions module={selectedModule} permissions={permissions} />
                            </section>



                            <p className="flex items-center gap-2 text-xs text-slate-500">
                                <ShieldCheck aria-hidden="true" className="h-4 w-4" />
                                Las rutas tambien validan permisos y alcance de empresa en el servidor.
                            </p>
                        </div>
                    ) : (
                        <div className="mx-auto flex min-h-80 max-w-xl flex-col items-center justify-center text-center">
                            <BookOpen aria-hidden="true" className="h-10 w-10 text-slate-300" />
                            <h2 className="mt-4 text-lg font-semibold text-slate-900">
                                No hay modulos documentados disponibles
                            </h2>
                            <p className="mt-2 text-sm text-slate-600">
                                Tu rol no tiene permisos de consulta para los modulos de esta guia.
                            </p>
                        </div>
                    )}
                </main>
        </div>
        </div>
    );
}
