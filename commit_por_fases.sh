#!/usr/bin/env bash
# Organiza el código del prototipo en commits, ramas y etiquetas por sprint.
# Ejecutar DENTRO del proyecto Laravel, después de copiar el overlay (ver README).
# Los commits llevan la FECHA REAL en que usted ejecuta el script (no se alteran fechas).
# Haga estos pasos cuando realmente vaya cerrando cada sprint, o deje que Tabla 20 refleje las fechas reales.
set -euo pipefail

[ -f artisan ] || { echo "Ejecute este script en la raíz del proyecto Laravel."; exit 1; }
[ -f routes/web.php ] || { echo "Falta routes/web.php"; exit 1; }
git rev-parse --git-dir >/dev/null 2>&1 || { git init -b main; }

# 0) Línea base: el esqueleto de Laravel (sin los archivos del overlay)
OVERLAY=(app/Http/Controllers/AuthController.php app/Http/Controllers/HomeController.php app/Http/Controllers/Admin
 app/Http/Controllers/ConsultationController.php app/Http/Controllers/InboxController.php app/Http/Controllers/IndicatorController.php
 app/Http/Middleware/EnsureRole.php app/Models config/teleapoyo.php database/migrations/2026_01_01_* database/seeders/DatabaseSeeder.php
 resources/views/admin resources/views/auth resources/views/consultations resources/views/inbox resources/views/indicators resources/views/layouts
 routes/web.php tests/Feature/TeleapoyoTest.php README.md .env.teleapoyo.example commit_por_fases.sh)

cp routes/web.php /tmp/teleapoyo_routes_full.php
if ! git rev-parse HEAD >/dev/null 2>&1; then
  git add -A
  git reset -q -- "${OVERLAY[@]}" 2>/dev/null || true
  git commit -q -m "chore: esqueleto base de Laravel 11 (Sprint 0)" || true
fi
git checkout -q -B develop

commit() { git add -- "${@:2}"; git commit -q -m "$1"; }
# versión de rutas filtrada: quita los bloques de sprints posteriores
routes_upto() {
  cp /tmp/teleapoyo_routes_full.php routes/web.php
  for s in "$@"; do sed -i.bak "/SPRINT-${s}-START/,/SPRINT-${s}-END/d" routes/web.php; rm -f routes/web.php.bak; done
}
finish() { # $1 rama, $2 tag
  git checkout -q develop && git merge -q --no-ff "$1" -m "merge: $1" && git tag -f "$2" >/dev/null
}

# ---------------- Sprint 2: diseño ----------------
git checkout -q -b feature/sprint-2-diseno
commit "docs: README con arquitectura, requerimientos y trazabilidad RF/RNF" README.md .env.teleapoyo.example
finish feature/sprint-2-diseno sprint-2

# ---------------- Sprint 3: autenticación y administración (HU-01, 02, 03, 10) ----------------
git checkout -q -b feature/sprint-3-auth-admin
commit "feat(db): migraciones de especialidades, sedes y campos de rol en usuarios (HU-02, HU-03)" database/migrations/2026_01_01_000001_* database/migrations/2026_01_01_000002_*
commit "feat(db): bitácora de auditoría y modelo AuditLog (HU-10)" database/migrations/2026_01_01_000004_* app/Models/AuditLog.php
commit "feat(models): User con roles, Specialty y Site" app/Models/User.php app/Models/Specialty.php app/Models/Site.php
commit "feat(auth): login/logout con sesión regenerada, límite de intentos y mensaje genérico (HU-01)" app/Http/Controllers/AuthController.php app/Http/Controllers/HomeController.php resources/views/auth resources/views/layouts
commit "feat(authz): middleware EnsureRole con denegación por defecto y registro en bitácora (HU-01)" app/Http/Middleware/EnsureRole.php
commit "feat(admin): gestión de usuarios, roles, especialidades y sedes; vista de bitácora (HU-02, 03, 10)" app/Http/Controllers/Admin resources/views/admin
routes_upto 4 5; commit "feat(routes): rutas de autenticación y administración" routes/web.php
finish feature/sprint-3-auth-admin sprint-3

# ---------------- Sprint 4: módulo de teleapoyo (HU-04 a HU-07) ----------------
git checkout -q -b feature/sprint-4-teleapoyo
commit "feat(db): consultas y respuestas con restricción única por consulta (HU-06)" database/migrations/2026_01_01_000003_*
commit "feat(models): Consultation (documento cifrado, estado visible) y Response; config del plazo" app/Models/Consultation.php app/Models/Response.php config/teleapoyo.php
commit "feat(general): registrar, listar y ver consultas con aviso de alcance (HU-04, HU-07)" app/Http/Controllers/ConsultationController.php resources/views/consultations
commit "feat(especialista): bandeja por especialidad y respuesta única transaccional (HU-05, HU-06)" app/Http/Controllers/InboxController.php resources/views/inbox
routes_upto 5; commit "feat(routes): rutas del médico general y del especialista" routes/web.php
commit "chore(seed): datos ficticios de desarrollo (sedes, especialidades, usuarios, consultas)" database/seeders/DatabaseSeeder.php
finish feature/sprint-4-teleapoyo sprint-4

# ---------------- Sprint 5: indicadores (HU-08, HU-09) ----------------
git checkout -q -b feature/sprint-5-indicadores
commit "feat(coordinador): indicadores por especialidad y sede y exportación CSV (HU-08, HU-09)" app/Http/Controllers/IndicatorController.php resources/views/indicators
cp /tmp/teleapoyo_routes_full.php routes/web.php; commit "feat(routes): rutas de indicadores" routes/web.php
finish feature/sprint-5-indicadores sprint-5

# ---------------- Sprint 6: pruebas ----------------
git checkout -q -b feature/sprint-6-pruebas
commit "test: pruebas de funcionalidad (login, roles, validación, cifrado, única respuesta, indicadores)" tests/Feature/TeleapoyoTest.php
finish feature/sprint-6-pruebas sprint-6

git add -A; git diff --cached --quiet || git commit -q -m "chore: ajustes finales del Sprint 7"
git checkout -q main 2>/dev/null || git checkout -q -b main
git merge -q --no-ff develop -m "release: prototipo TRL 5 (Sprints 2-6)"
git tag -f sprint-7 >/dev/null
echo; git log --oneline --decorate --graph | head -40
echo; echo "Siguiente: git remote add origin <URL> && git push -u origin main develop && git push --tags"
