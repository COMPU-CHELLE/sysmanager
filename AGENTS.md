# SysManager

Aplicación Laravel 13 con PHP 8.4, MySQL, Inertia y React. Laravel Boost ya está
instalado: no volver a instalarlo. Las reglas detalladas del proyecto están en
`CLAUDE.md` y en `.claude/skills/`.

## Inicio local

```sh
composer install
npm install
php artisan migrate
npm run build
php artisan serve --host=127.0.0.1 --port=8000
```

La configuración de MySQL se toma de `.env`. No subir ni compartir ese archivo
ni sus credenciales.

## Arquitectura actual

- Autenticación por correo o por `users.code`.
- Multiempresa: relación muchos a muchos `company_user`.
- Control de acceso: usuarios, roles y permisos mediante `role_user` y
	`permission_role`. Cada módulo usa las acciones `view`, `create`, `update`,
	`delete`, `restore` y `force-delete`.
- Roles globales cuando `roles.company_id` es `null`; los demás pertenecen a una
	empresa.
- La administración de Empresas, Usuarios y Roles está protegida por los Gates
	de acción correspondiente, por ejemplo `companies.view` o `users.create`.
- DatabaseSeeder es el unico seeder: crea modulos y permisos (view, create, update, delete, restore, force-delete), los roles globales Soporte y Administrador (todos los permisos) y Usuario (view, create, update en modulos operativos), y la empresa DEMO. Todo lo demas se crea manualmente.
- Las pantallas Inertia se encuentran en `resources/js/Pages`; la navegación
	autenticada está en `resources/js/Layouts/AuthenticatedLayout.jsx`.

## Módulos operativos pendientes

Los módulos de negocio todavía no están definidos. No crear entidades, campos,
relaciones ni permisos operativos por suposición. El usuario proporcionará desde
otro equipo las migraciones o su diseño.

Por cada migración recibida, integrar en este orden:

1. Revisar claves foráneas, índices, soft deletes y relación con `companies`.
2. Crear el modelo, factory, request, controlador, rutas y pruebas necesarias.
3. Añadir el módulo al catálogo de `DatabaseSeeder` para generar permisos
	`view`, `create`, `update`, `delete`, `restore` y `force-delete`.
4. Proteger cada ruta con el Gate correspondiente y mostrar/ocultar navegación y
	botones Inertia según el permiso.

## Acceso administrativo inicial

Primero siembra permisos, roles (Soporte, Administrador, Usuario) y la empresa DEMO:

```sh
php artisan db:seed --force
```

Después crea o actualiza un administrador. El comando solicita la contraseña de
forma oculta, por lo que nunca debe incluirse en comandos, código ni chat:

```sh
php artisan sysmanager:make-admin 'NOMBRE' 'CODIGO DE ACCESO' 'correo@empresa.com'
```

## Pruebas y formato

Las pruebas usan la base MySQL `sysmanager_testing`. El archivo `phpunit.xml`
fuerza sus variables de conexión para evitar que hereden `DB_DATABASE` de la
shell local.

```sh
vendor/bin/pint --dirty --format agent
php artisan test --compact
npm run build
```

La última verificación completa pasó con 35 pruebas y 99 aserciones.
