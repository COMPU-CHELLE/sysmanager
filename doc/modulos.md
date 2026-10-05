# Modulos, funciones y rutas

SysManager organiza sus funciones por modulo. Las pantallas de administracion
y operacion usan permisos independientes; el servidor valida cada accion y
restringe la informacion a las empresas permitidas para el usuario.

La guia interactiva de `/documentation` tiene un menu lateral propio, separado
del menu de trabajo. Sus secciones y modulos se filtran por los permisos del
usuario: Soporte consulta Empresas, Usuarios, Roles y Auditoria; Administrador
y Usuario consultan los modulos habilitados para su rol. La vista de cada
modulo describe sus acciones, rutas y datos principales.

## Convenciones de rutas CRUD

Salvo donde se indica lo contrario, cada modulo CRUD expone:

| Metodo | Ruta | Accion | Permiso |
| --- | --- | --- | --- |
| `GET` | `/<modulo>` | Listar registros | `<modulo>.view` |
| `POST` | `/<modulo>` | Crear registro | `<modulo>.create` |
| `PATCH` | `/<modulo>/{id}` | Actualizar registro | `<modulo>.update` |
| `DELETE` | `/<modulo>/{id}` | Eliminar logicamente | `<modulo>.delete` |
| `PATCH` | `/<modulo>/{id}/restore` | Restaurar registro eliminado | `<modulo>.restore` |
| `DELETE` | `/<modulo>/{id}/force` | Eliminar definitivamente | `<modulo>.force-delete` |

Las rutas para restaurar y eliminar definitivamente solo estan disponibles en
los modulos con eliminacion logica. Los registros eliminados se incluyen en la
lista solo si el usuario cuenta con el permiso `restore`.

## Administracion

| Modulo | Funcion | Ruta principal |
| --- | --- | --- |
| Empresas | Crear y administrar las empresas cliente y su estado. El alcance de cada usuario no Soporte se limita a sus empresas asociadas. | `/companies` |
| Usuarios | Administrar cuentas, sus empresas, roles y datos de acceso dentro del alcance permitido. | `/users` |
| Roles | Crear roles y asignar permisos por modulo y accion. La pantalla presenta los permisos agrupados por Administracion y Operacion. | `/roles` |
| Planes | Administrar el catalogo de planes, precio y limites configurados de usuarios y activos. No aparece en el sidebar actual. | `/plans` |
| Auditoria | Consultar los eventos de auditoria registrados por las operaciones de la aplicacion. Es de solo lectura. | `GET /audit-logs` |

Los modulos Empresas, Usuarios y Roles usan las acciones CRUD; soportan
restauracion y eliminacion definitiva con sus permisos correspondientes.
Auditoria no tiene rutas de escritura.

## Operacion

| Modulo | Funcion | Ruta principal |
| --- | --- | --- |
| Sucursales | Registrar sedes asociadas a una empresa, con codigo, direccion y correo. | `/branches` |
| Empleados | Mantener el directorio de empleados y su sucursal. | `/employees` |
| Activos | Inventariar equipos y otros activos, asociarlos a empresa/sucursal y registrar especificaciones de hardware. | `/assets` |
| Asignaciones | Registrar la entrega y devolucion de activos a empleados. Permite generar un acta imprimible. | `/asset-assignments` |
| Mantenimientos | Registrar descripcion, fecha y costo de trabajos realizados sobre activos. | `/maintenances` |
| Credenciales | Guardar credenciales de acceso por empresa, junto con tipo, URL y modo de acceso. La contrasena no se devuelve en los datos de la tabla. | `/credentials` |
| Facturas | Registrar facturas por empresa y sucursal con proveedor, fecha, categoria y lineas de detalle. El total de cada linea se calcula en servidor. | `/invoices` |
| Tareas | Asignar y dar seguimiento a tareas por estado, prioridad, responsable y fecha limite. | `/tasks` |
| Tickets | Registrar solicitudes de soporte, cambiar su estado y mantener una conversacion de mensajes. | `/tickets` |

Todos los modulos operativos tienen rutas CRUD y acciones de restauracion /
eliminacion definitiva sujetas a permisos.

### Rutas especiales de operacion

| Metodo | Ruta | Funcion | Permiso |
| --- | --- | --- | --- |
| `GET` | `/asset-assignments/{id}/document` | Generar o consultar el acta imprimible de una asignacion. | `asset-assignments.view` |
| `POST` | `/tickets/{id}/messages` | Agregar un mensaje al hilo de un ticket. | `tickets.update` |

## Dashboard, perfil y autenticacion

| Ruta | Funcion |
| --- | --- |
| `GET /` | Redirige a `/login`. |
| `GET /login` | Formulario de inicio de sesion; acepta correo o codigo de acceso. |
| `GET /dashboard` | Muestra un tablero segun rol: Soporte ve empresas y actividad; Administrador ve informacion de su empresa; Usuario ve sus tareas y movimientos. |
| `GET /documentation` | Documentacion integrada, con menu lateral y contenido filtrado segun permisos. |
| `GET /profile` | Ver perfil. Todos pueden cambiar su contrasena; Soporte tambien puede actualizar sus datos y eliminar su propia cuenta. |
| `PATCH /profile` | Actualizar nombre y correo; solo Soporte. |
| `DELETE /profile` | Eliminar la cuenta actual; solo Soporte y requiere confirmar la contrasena. |

La navegacion visible varia por rol y permisos. Soporte tiene una barra lateral
de administracion (Empresas, Usuarios, Roles y Auditoria), sin las funciones
operativas.
