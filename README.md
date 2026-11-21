# Teleapoyo — prototipo funcional (Proyecto de grado, Grupo 17)

Plataforma web de teleapoyo médico-médico (Resolución 1644 de 2026): el médico general registra una consulta, el especialista de la especialidad emite **una única respuesta**, el coordinador ve indicadores y el administrador gestiona usuarios, catálogos y bitácora.

Pila: Laravel 11 (PHP 8.2+) · PostgreSQL · Blade. **Solo datos ficticios.**

> Este paquete es un *overlay*: contiene únicamente los archivos propios del proyecto. Se copia sobre una instalación limpia de Laravel.

## 1. Instalación (una vez)

Requisitos: PHP 8.2+, Composer, PostgreSQL 14+ (o SQLite para probar), Git.

```bash
composer create-project laravel/laravel teleapoyo
cd teleapoyo
# copie TODO el contenido de esta carpeta (app, config, database, resources, routes, tests) sobre ./teleapoyo, aceptando reemplazar archivos
```

Reemplaza: `app/Models/User.php` y `routes/web.php`. No borre `bootstrap/` ni `composer.json`.

Edite `.env` (vea `.env.teleapoyo.example`) y cree la base de datos:

```bash
createdb teleapoyo      # o cree la base desde pgAdmin
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Abra http://127.0.0.1:8000

### Usuarios de demostración (contraseña `Teleapoyo2026!`)

| Rol | Correo |
|---|---|
| Administrador | admin@teleapoyo.test |
| Coordinador | coordinador@teleapoyo.test |
| Especialista (Cardiología) | cardiologia@teleapoyo.test |
| Especialista (Pediatría) | pediatria@teleapoyo.test |
| Médico general (Turbo) | general1@teleapoyo.test |
| Médico general (Carepa) | general2@teleapoyo.test |

## 2. Pruebas

```bash
php artisan test
```

`tests/Feature/TeleapoyoTest.php` cubre: login válido/inválido, usuario inactivo, acceso por rol (403), validación de campos, cifrado del documento del paciente, filtro por especialidad, **única respuesta**, indicadores y aislamiento de consultas.

> Si usa SQLite en memoria para las pruebas, Laravel ya lo configura en `phpunit.xml`. Si falla alguna prueba, copie el error y se corrige.

## 3. Requerimientos del documento maestro → código

| Requerimiento | Dónde |
|---|---|
| RF-01 Login por rol | `AuthController`, `EnsureRole` |
| RF-02 Usuarios y roles | `Admin/UserController` |
| RF-03 Especialidades y sedes | `Admin/CatalogController` |
| RF-04/05 Registrar y asignar consulta | `ConsultationController`, `Consultation` |
| RF-06 Bandeja por especialidad | `InboxController@index/show` |
| RF-07 Única respuesta | `InboxController@respond` + índice único `responses.consultation_id` |
| RF-08 Ver respuesta | `ConsultationController@show` |
| RF-09 Aviso de alcance | vistas `consultations/*`, `inbox/*`, login |
| RF-10/12 Indicadores y CSV | `IndicatorController` |
| RF-11 Bitácora | `AuditLog`, `Admin/AuditController` |
| RNF-01 Seguridad | hash de contraseñas, CSRF, regeneración de sesión, `throttle:5,1` en login |
| RNF-02 Minimización | solo `patient_document` (cast `encrypted`) |

## 4. Subir a GitHub (Anexo F)

Para que el historial quede organizado por sprint (ramas `feature/sprint-N`, merges a `develop`, etiquetas `sprint-N`), use el script incluido **después de copiar el overlay**:

```bash
cd teleapoyo
bash commit_por_fases.sh        # crea los commits, ramas y etiquetas por sprint
git remote add origin https://github.com/SU_USUARIO/teleapoyo.git   # repositorio vacío creado en github.com
git push -u origin main develop
git push --tags
```

Importante: el script usa la **fecha real** en que lo ejecuta; no modifica fechas. Para que las fechas de la Tabla 20 coincidan con el repositorio, copie las fechas reales con `git log -1 --format=%ad sprint-3` (y las demás etiquetas) y los commits con `git log --oneline --decorate`. Si prefiere un historial más natural, ejecute el script por partes a medida que cierre cada sprint.

Acceso del tutor con **rol de lectura**: en una cuenta personal, un colaborador siempre tiene permiso de escritura. Opciones: (a) dejar el repositorio **público** (no contiene secretos: `.env` está ignorado y los datos son ficticios), o (b) crear una *organización* gratuita de GitHub, ponerlo ahí y añadir al tutor con rol *Read*. Confirme con el tutor cuál prefiere.

## 5. Entorno relevante TRL 5 (despliegue)

Desplegar en un servidor Linux con Nginx + PHP-FPM + PostgreSQL + HTTPS (p. ej. una VM pequeña con Let's Encrypt), `APP_ENV=production`, `APP_DEBUG=false`, `php artisan migrate --seed --force`, y cuentas de los cuatro perfiles. Documente en el informe la URL/IP y la fecha. Este despliegue es el "entorno relevante" que exige la Tabla 21 del documento maestro.

## 6. Limitaciones conocidas

- Estado «Vencida» derivado por plazo (`TELEAPOYO_SLA_HOURS`); no hay notificaciones por correo.
- Sin historia clínica electrónica ni integración con sistemas de terceros (fuera de alcance).
- El código fue escrito sin ejecutarlo en el entorno de generación: corra `php artisan test` y revise antes de grabar el video.
