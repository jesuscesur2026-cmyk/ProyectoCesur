# Informe Final — Biblioteca para simples

Fecha: 2026-04-29

## Resumen ejecutivo

Biblioteca para simples es una aplicación web desarrollada en Laravel 8 que simula una biblioteca digital con funcionalidades de gestión de ejemplares, usuarios y alquileres. El objetivo del proyecto fue hacer la aplicación reproducible (Docker), robusta (migraciones, seeders, tests) y presentable (contenido y diseño). Este informe resume la arquitectura, decisiones técnicas, cómo ejecutar el proyecto y una reflexión final.

---

## 1. Arquitectura general

- Lenguaje: PHP 8.x (contenedorizado con `php:8.4-fpm` en Docker).
- Framework: Laravel 8.
- Frontend: Blade + SASS (Laravel Mix), librerías mínimas (FontAwesome, AOS). Se añadió SCSS moderno en `resources/sass/custom.scss`.
- Base de datos: MySQL 8 (contenedor `db`).
- Contenedores: `app` (php-fpm), `webserver` (nginx), `db` (mysql), `node` (assets), `mailhog` (dev mail capture).

---

## 2. Instalación y ejecución (resumen)

1. Instalar Docker Desktop.
2. Ejecutar en la raíz del repo:

```bash
docker-compose up -d --build
```

3. Ejecutar migraciones y seeders:

```bash
docker-compose exec app php artisan migrate --seed
```

4. Compilar assets (si cambias CSS/JS):

```bash
# en host o en contenedor node
docker-compose exec node npm ci
docker-compose exec node npm run production
```

5. MailHog (dev) en http://localhost:8025 (SMTP 1025).

---

## 3. Base de datos y seeders

- Tablas principales: `usuario`, `rol`, `ejemplar`, `editorial`, `autor`, `coleccion`, `detalle_alquiler`, `wishlist`.
- Seeders notables:
  - `RolSeeder`: roles base.
  - `AdminAndEjemplarSeeder`: crea `admin@local.test` / `Password123` y 100 ejemplares.
  - `PopulateAllSeeder`: generación masiva de datos (autores/editoriales/usuarios/ejemplares).
  - `UpdateEjemplarImagesSeeder`: asigna imágenes válidas a ejemplares que carecen de ellas.
- Nota: la columna `contenido` del modelo `Ejemplar` se ajustó a `TEXT` para evitar errores por límite de longitud.

---

## 4. Emails y MailHog

- En desarrollo `MAIL_MAILER=smtp`, `MAIL_HOST=mailhog`, `MAIL_PORT=1025` (ver en `.env`).
- Se añadió `mailhog` al `docker-compose.yml` para capturar correos de verificación y recuperación.
- Diagnóstico aplicado: se comprobó resolución DNS entre contenedores, se vació `MAIL_ENCRYPTION` para evitar intentos TLS en el puerto 1025, y se creó un script de test (`scripts/send_test_mail.php`) para verificar envío.

---

## 5. Tests y Calidad

- PHPUnit 9 usado; las pruebas de características (`Feature`) se adaptaron para usar `DatabaseMigrations` en lugar de `RefreshDatabase` por compatibilidad con MySQL en contenedores.
- Se añadieron tests esenciales para flujos de autenticación y roles.

---

## 6. Rediseño visual

- Se añadió `resources/sass/custom.scss` con una paleta moderna (fondo oscuro, acentos cian/morado) y reglas para tarjetas, header y footer.
- Se actualizó `resources/views/layouts/app.blade.php` para enlazar `public/css/custom.css`.
- Componentes clave (`ejemplares`, `detalles`, `libro`) fueron adaptados para usar las nuevas clases y mejorar accesibilidad.

---

## 7. Correcciones importantes realizadas

- Evitar auto-login tras registro: `RegisterController::register()` ahora crea el usuario, despacha la notificación de verificación y redirige a `login` con aviso.
- Fix rutas en `CarritoController` (redir `carrito.show` en lugar de `show`).
- Relleno de la vista de lectura: `resources/views/ejemplares/libro.blade.php` ahora muestra contenido HTML o usa el visor PDF si el `contenido` es PDF.
- Seeders idempotentes: uso de `firstOrCreate` en creaciones repetibles para evitar errores de clave duplicada al re-ejecutar seeders.

---

## 8. Cómo generar el PDF final (local)

He incluido el contenido de este informe en `docs/Informe_Final.md`. Para generar un PDF localmente necesitas `pandoc` o `wkhtmltopdf` (o un conversor Markdown→PDF equivalente).

Ejemplo con `pandoc` (recomendado):

```bash
# instalar pandoc (si no tienes)
# en Debian/Ubuntu: sudo apt install pandoc

# desde la raíz del repo
pandoc docs/Informe_Final.md -o docs/Informe_Final.pdf --pdf-engine=xelatex --variable mainfont="DejaVu Sans"
```

Alternativa con `wkhtmltopdf` (convierte HTML a PDF):

```bash
# convertir Markdown a HTML y a PDF
pandoc docs/Informe_Final.md -o docs/Informe_Final.html
wkhtmltopdf docs/Informe_Final.html docs/Informe_Final.pdf
```

También incluí scripts en `scripts/` para facilitar la generación (`generate_pdf.sh` y `generate_pdf.ps1`).

---

## 9. Consideraciones y limitaciones

- Entorno de desarrollo: MailHog gestiona correos en dev pero no envía a destinatarios reales.
- Tests: la suite de tests básica pasa, pero la cobertura no es exhaustiva; conviene ampliar tests de integración y rutas críticas.
- Seguridad: no se realizó una auditoría completa; revisar configuración de sesiones, CSRF, XSS y validaciones de subida de archivos antes de producción.

---

## 10. Reflexión final

Este proyecto se centró en transformar una aplicación existente en un proyecto reproducible, robusto y adecuado para demostración. Los objetivos principales —contenerización, datos de ejemplo fiables, tests que pasen en un entorno similar al de producción, y una UI moderna— se han cumplido en gran medida.

Retos:
- Entender la variedad de errores originados por diferentes entornos (host vs contenedor) fue el mayor desafío; por ejemplo la resolución del host `mailhog` y las diferencias de red.
- Hacer los seeders idempotentes para permitir repetir la generación de datos de forma segura.

Siguientes pasos recomendados:
- Automatizar la generación del PDF en CI para incluirlo en releases.
- Completar la documentación de despliegue a producción (NGINX config, SSL, backups).
- Añadir más tests de integración y E2E (Cypress/Playwright) para flujos complejos.

---

Si quieres, genero ahora el PDF en tu entorno remoto (necesitarás `pandoc`/LaTeX) o te guío paso a paso para ejecutarlo aquí localmente. ¿Lo genero y guardo en `docs/Informe_Final.pdf` mediante instrucciones que te doy para ejecutar?