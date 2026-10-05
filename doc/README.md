# Documentacion del sistema

Esta carpeta contiene la documentacion funcional de SysManager:

- [`modulos.md`](modulos.md): proposito de cada modulo, rutas, permisos,
  alcance y acciones especiales.

## Navegacion

Tras iniciar sesion, el boton **Documentacion** en la barra superior abre una
seccion dedicada con su propio menu lateral. Este menu muestra las secciones
de Administracion y Operacion, y solo los modulos que el usuario puede
consultar. La guia explica las acciones disponibles segun los permisos
efectivos de su rol.

Soporte ve la documentacion de Empresas, Usuarios, Roles y Auditoria.
Administrador y Usuario ven los modulos habilitados por sus permisos. En cada
modulo se describen los campos principales, las acciones disponibles, sus
rutas y las reglas especiales relevantes.

## Convenciones

- Las rutas web requieren autenticacion, excepto la pantalla de inicio de
  sesion y las rutas de autenticacion.
- Los permisos siguen el formato `<modulo>.<accion>`.
- Los CRUD usan `GET /<modulo>` para listar, `POST /<modulo>` para crear,
  `PATCH /<modulo>/{id}` para actualizar y `DELETE /<modulo>/{id}` para
  eliminar logicamente.
- Cuando el modulo soporta restauracion, se agrega
  `PATCH /<modulo>/{id}/restore` y
  `DELETE /<modulo>/{id}/force` para eliminacion definitiva.
- El backend aplica el alcance multiempresa incluso si se solicita una ruta
  directamente.

Las rutas y permisos descritos reflejan `routes/web.php`, `Permission` y los
controladores actuales. Actualizar este documento cuando cambie el contrato
de rutas o la responsabilidad de un modulo.
