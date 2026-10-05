# SysManager

SysManager es una aplicacion multiempresa para administrar operaciones de TI:
empresas, usuarios, activos, asignaciones, mantenimiento, credenciales,
facturas, tareas, tickets y auditoria.

## Tecnologias

- PHP 8.3 o superior y Laravel 13.
- MySQL.
- Inertia.js 2 y React.
- Tailwind CSS.

## Requisitos

- PHP y extensiones requeridas por Laravel.
- Composer.
- Node.js y npm.
- Servidor MySQL accesible desde la aplicacion.

## Instalacion local

1. Instalar dependencias:

   ```sh
   composer install
   npm install
   ```

2. Crear `.env` a partir de `.env.example` si aun no existe y configurar alli
   la conexion MySQL. No compartir ni subir `.env`.

3. Generar una clave de aplicacion si el entorno aun no tiene una:

   ```sh
   php artisan key:generate
   ```

4. Ejecutar migraciones y cargar los datos iniciales:

   ```sh
   php artisan migrate --seed
   ```

5. Compilar los recursos del frontend:

   ```sh
   npm run build
   ```

6. Iniciar la aplicacion:

   ```sh
   php artisan serve --host=127.0.0.1 --port=8000
   ```

   Durante el desarrollo del frontend, usar `npm run dev` en otra terminal.
   La raiz `/` redirige al formulario de inicio de sesion `/login`.

> `php artisan migrate:fresh --seed` elimina todas las tablas y datos de la
> base seleccionada antes de volver a crearlas. Usarlo solo cuando se quiera
> reiniciar intencionalmente una base local.

## Acceso inicial y roles

`DatabaseSeeder` es el seeder inicial unico. Crea los permisos, los roles
globales **Soporte**, **Administrador** y **Usuario**, la empresa **DEMO** y el
usuario global de Soporte. El usuario predeterminado se identifica con el
codigo `SOPORTE` y el correo `soporte@sysmanager.test`.

Si no se configura una contrasena para Soporte, el seeder genera una aleatoria
y la muestra en la salida una sola vez al crear la cuenta. Se pueden definir
`SUPPORT_NAME`, `SUPPORT_CODE`, `SUPPORT_EMAIL` y `SUPPORT_PASSWORD` en el
entorno local antes de sembrar. No guardar contrasenas en el repositorio.

- **Soporte** tiene alcance global, administra empresas, usuarios, roles y
  auditoria. Su barra lateral no muestra los modulos operativos.
- **Administrador** administra la operacion de su empresa y no administra
  cuentas de Soporte ni de otros administradores.
- **Usuario** opera los modulos que le permiten sus permisos.
- Administrador y Usuario solo pueden cambiar su contrasena desde su perfil.
  Soporte puede actualizar los datos del perfil y eliminar su propia cuenta.

Los permisos se nombran como `<modulo>.<accion>`. Las acciones disponibles son
`view`, `create`, `update`, `delete`, `restore` y `force-delete`. Los registros
eliminados logicamente se muestran solo a quienes tienen el permiso `restore`;
la eliminacion definitiva requiere `force-delete`.

## Modulos y documentacion

La documentacion funcional completa, incluyendo proposito, rutas y permisos
por modulo, se encuentra en [`doc/modulos.md`](doc/modulos.md). Tambien esta
disponible desde el boton **Documentacion del sistema** en la barra superior
despues de iniciar sesion.

| Area | Modulos |
| --- | --- |
| Administracion | Empresas, Usuarios, Roles, Planes y Auditoria |
| Operacion | Sucursales, Empleados, Activos, Asignaciones, Mantenimientos, Credenciales, Facturas, Tareas y Tickets |

La documentacion para desarrolladores y los detalles de las rutas se mantienen
en [`doc/README.md`](doc/README.md) y [`doc/modulos.md`](doc/modulos.md).

## Pruebas y calidad

El proyecto usa PHPUnit. La configuracion de pruebas fuerza la base MySQL
`sysmanager_testing`; debe existir y estar disponible antes de ejecutar:

```sh
php artisan test --compact
```

Las pruebas Feature se retiraron del proyecto; revisar `tests/Unit` para las
pruebas que permanecen. Para dar formato a los archivos PHP modificados:

```sh
vendor/bin/pint --dirty --format agent
```

## Estructura principal

- `app/Http/Controllers`: controladores HTTP, incluidos los CRUD modulares.
- `app/Models`: modelos Eloquent.
- `database/migrations`: esquema de base de datos.
- `database/seeders/DatabaseSeeder.php`: permisos, roles, empresa DEMO y
  usuario Soporte.
- `resources/js/Pages`: paginas Inertia/React.
- `resources/js/Layouts/AuthenticatedLayout.jsx`: layout y navegacion
  autenticados.
- `routes/web.php`: rutas web autenticadas y protegidas por permisos.
- `doc`: documentacion funcional y rutas por modulo.

## Seguridad

No versionar `.env`, claves, contrasenas ni datos reales. La autorizacion se
valida en el servidor mediante middleware y alcance por empresa; ocultar una
opcion en la interfaz no reemplaza esa validacion.
