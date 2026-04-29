# Manual de Uso — Biblioteca para simples

Fecha: 2026-04-28

Este manual describe cómo usar la aplicación web desde la perspectiva de un usuario y de un administrador.

## Requisitos previos

- Acceso a la URL de la app (por defecto `http://localhost:8000` cuando se usa Docker Compose con la configuración provista).
- Cuenta de usuario o administrador (puedes registrarte o usar el usuario admin creado por el seeder: `admin@local.test` / `Password123`).

## Registro y verificación

1. Ir a la página de registro (`/register`).
2. Completar formulario con nombre, apellidos, fecha de nacimiento, email y contraseña.
3. Al registrarte la aplicación genera un email de verificación. En entorno de desarrollo abre MailHog (`http://localhost:8025`) para ver el correo y hacer clic en el enlace de verificación.
4. Tras verificar el email podrás iniciar sesión.

> Nota: si el correo no llega, confirma que MailHog está en marcha y que en `.env` tienes `MAIL_MAILER=smtp`, `MAIL_HOST=mailhog`, `MAIL_PORT=1025`.

## Inicio de sesión

- Navega a `/login`, introduce tu email y contraseña y pulsa `Login`.
- Si olvidaste la contraseña usa `Forgot password` para recibir un enlace de restablecimiento (ver en MailHog en desarrollo).

## Navegación principal

- Menú superior contiene enlaces: `Home`, `Books`, `About us`, `Contact`, `WishList`, `Cart`, `My Account`.
- `Books` muestra el catálogo de ejemplares.

## Buscar y ver libros

- En `Books` puedes navegar y usar paginación.
- Pulsa en un libro para ver su `Detalle` (título, autor, editorial, colección, epílogo, puntuación, precio).
- La pestaña del navegador muestra el nombre del libro.
- Si no hay imagen de portada, el sistema asigna una imagen por defecto o aleatoria (según el seeder).

## Añadir a WishList

- En la página del detalle pulsa el icono de corazón para añadir/eliminar de la WishList.
- Accede a `WishList` desde el menú para ver tus libros guardados.

## Alquilar un ejemplar

- En el detalle pulsa `Rent` para abrir la ventana de alquiler.
- Si eres `socio` (rol), el precio mostrará descuento automáticamente.
- Confirmar fecha de alquiler y devolución según la política (la UI limita fechas según rol).

## Carrito y checkout

- Añade ejemplares al carrito desde la vista detalle.
- Abre `Cart` desde el menú para ver los artículos seleccionados y proceder al pago (si está implementado) o confirmar alquiler.

## Perfil de usuario

- `My Account` permite actualizar datos personales, cambiar contraseña y ver historial de alquileres.
- Cambiar foto de perfil: sube imagen en la sección correspondiente (si habilitado).

## Panel de administrador (rol `administrador`)

- Si tu usuario tiene `idRol == 3`, verás el enlace `Admin` en el menú.
- Funcionalidades típicas del admin:
  - Gestionar ejemplares: crear, editar, eliminar.
  - Gestionar autores, editoriales y colecciones.
  - Buscar usuarios, cambiar roles y eliminar cuentas.

## Gestión de ejemplares en admin

- `Admin -> Ejemplares` muestra listado con paginación.
- `Add new` permite crear un ejemplar: título, epílogo, fecha publicación, tema, idioma, precio, imagen, autor, editorial, colección.
- Al subir imágenes, colócalas en `public/book/` o sube a través de la UI (según implementación).

## Reglas y comportamientos

- Los emails de verificación deben confirmarse antes de usar funcionalidades que lo requieran.
- La cuenta `socio` ofrece descuentos al alquilar.
- La aplicación guarda historial de alquileres en `detalle_alquiler`.

## Problemas comunes y solución rápida

- No recibo el correo de verificación: abre MailHog (`http://localhost:8025`) para ver correo en desarrollo o configura SMTP real.
- No veo imágenes de libros: verifica que `public/book` contiene imágenes y que `image_book` en la tabla `ejemplar` apunta al nombre de archivo existente. Si no, ejecutar el seeder `UpdateEjemplarImagesSeeder`.
- Error al registrarme por FK `idRol`: ejecutar `php artisan db:seed --class=Database\\Seeders\\RolSeeder` o reiniciar seeders.

## Contacto y ayuda

- Para problemas técnicos, consulta el `docs/Informe_Tecnico.md` en el repositorio.
- Para aportes o incidencias, abre un issue o envía un correo al equipo responsable.

---

Si quieres, puedo:
- exportar estos documentos a PDF y guardarlos en `docs/`;
- crear un commit en una rama con estos ficheros y empujar al remoto.
