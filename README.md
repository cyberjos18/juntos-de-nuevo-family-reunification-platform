# Juntos de Nuevo (Reencuentro)

[![CI](https://github.com/cyberjos18/juntos-de-nuevo-family-reunification-platform/actions/workflows/ci.yml/badge.svg)](https://github.com/cyberjos18/juntos-de-nuevo-family-reunification-platform/actions/workflows/ci.yml)

A humanitarian web platform that helps reunite children and adolescents with their families after they have been separated by an emergency (earthquakes in Venezuela). Shelters register the minors in their care, and relatives can find them publicly to start the reunification process.

**Live site:** https://juntosdenuevo.org

> Non-profit humanitarian project. It handles sensitive data about minors — every change must put security, privacy and data integrity first.

---

## Roles and access

| Role | Description | Access |
|------|-------------|--------|
| **Public** | Anyone looking for a relative | Read only: public list and profile of each child |
| **Gestor** (shelter staff) | Staff of a single shelter | Register children, handle pickups for their own shelter |
| **Administrador** | Superuser | Validate shelters, manage users and configuration, global view |

## Main features

- Public registry of found children (photo and basic data), listed on the home page
- Detailed profile per child (`/ninos/{nino}`)
- Shelter sign-up with activation by email
- Administrator-managed Gestor accounts
- **Pickup process** (`recogida`): records the relative who collects the minor, with photographic evidence — an irreversible state change
- Configuration of the transactional email provider (Brevo)

The key business rules live in [`docs/software-plan.md`](juntos-de-nuevo/docs/software-plan.md).

## Tech stack

- **Backend:** Laravel 13 / PHP 8.3
- **Frontend:** Blade + Alpine.js + Tailwind CSS 4 (Vite)
- **Database:** MySQL 8
- **API auth:** Laravel Sanctum
- **Email:** Brevo (`app/Services/BrevoMailService.php`)
- **Tests:** Pest 4 / PHPUnit

## Code architecture

The project separates responsibilities beyond Laravel's standard MVC:

```
app/
├── Http/Controllers/
│   ├── Web/              # Blade view controllers (Auth, Gestor, Admin)
│   └── Api/              # API controllers (Sanctum)
├── Http/Middleware/      # CheckRole, EnsureCentroActivo
├── Models/               # Eloquent: Centro, Nino, NinoFamiliar, Recogida, User, Configuracion
├── Persistencia/         # Repository layer on top of the models
├── Validaciones/         # Validation rules, one class per use case
├── Services/             # External integrations (Brevo, mail configuration)
└── Mail/                 # Mailables (shelter activation, notifications)
```

## Local setup

Requirements: PHP 8.3, Composer, Node 20+, MySQL 8 (this environment uses WAMP64).

```bash
cd juntos-de-nuevo

composer install
npm install

cp .env.example .env   # fill in the database and mail credentials
php artisan key:generate
```

**Important — the database:** the main tables (`centros`, `ninos`, `ninos_familiares`, `recogidas`, `configuracion`) are *not* created by `php artisan migrate`; they were defined first as plain SQL scripts. Those scripts neither create nor select a database on their own — they run against whichever database the connection already points to, so create it and point to it yourself before importing:

```bash
mysql -u <user> -p -e "CREATE DATABASE <database> DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
mysql -u <user> -p <database> < docs/database-script.sql
mysql -u <user> -p <database> < docs/laravel-aux-tables.sql
mysql -u <user> -p <database> < docs/configuracion-table.sql
php artisan migrate   # Laravel's own tables only (personal_access_tokens already
                      # comes from the script; this applies future migrations)
```

Start the development environment:

```bash
composer run dev   # server, queue listener, logs (pail) and vite in parallel
```

## Tests

The tests are written in [Pest](https://pestphp.com) (on top of PHPUnit) and split into two suites:

```
tests/
├── Feature/   # End-to-end HTTP: Web (Blade) and API (Sanctum) routes, per role
└── Unit/      # Model logic in isolation (scopes, state helpers)
```

**Important — the test database:** the tests run against a real, separate MySQL database (`findchild_test`) rather than in-memory SQLite, because they rely on the same referential integrity constraints as production (`FOREIGN KEY`, `NOT NULL`). Each test runs inside a transaction that is rolled back afterwards (`DatabaseTransactions`), so the fixtures survive between tests.

Expected configuration (see `phpunit.xml`): MySQL on `127.0.0.1:3307`, database `findchild_test`. Before running the tests for the first time:

```bash
mysql -u root -P 3307 -e "CREATE DATABASE findchild_test DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
mysql -u root -P 3307 findchild_test < docs/database-script.sql
mysql -u root -P 3307 findchild_test < docs/laravel-aux-tables.sql
mysql -u root -P 3307 findchild_test < docs/configuracion-table.sql
```

The fixtures in `docs/database-script.sql` include users and children with fixed IDs that the tests reference directly (for example child `#1` of the "Protección Civil" shelter, and child `#4`, already picked up). The exact credentials are documented in the headers of the SQL script itself and in `tests/Helpers.php` (`adminUser()`, `gestorUser()`, `gestorHospitalUser()`).

Run the tests:

```bash
php artisan test
# or directly
./vendor/bin/pest
```

### What they cover

- **Authentication** (web and API): login, logout, shelter sign-up, activation by token, inactive accounts
- **Roles and permissions:** a Gestor never operates on another shelter's data; Admin routes are blocked for a Gestor and vice versa
- **`EnsureCentroActivo`:** a Gestor whose shelter has not yet been validated by an Administrador cannot create children, list their own, or register pickups — neither over the web nor over the API
- **Children:** registration, public profile vs. authenticated profile (sensitive data is hidden from anonymous visitors), validation of age, sex and state
- **Pickups:** only once per child (an irreversible state), evidence photo required
- **Transactional emails:** `Mail::fake()` asserts that `ActivarCentroMail`, `NuevoCentroPendienteMail` and `CentroActivadoMail` are dispatched during shelter sign-up and validation
- **Models** (`Unit`): `Nino` scopes (`encontrados`, `recogidos`, `familiaLocalizada`, `porCentro`) and the role/state helpers on `User` and `Nino`

### Continuous integration

[`.github/workflows/ci.yml`](.github/workflows/ci.yml) runs the suite on GitHub Actions against a MySQL 8 service container: it installs the PHP extensions, imports the three SQL scripts and runs `php artisan test`. The repository also keeps [`bitbucket-pipelines.yml`](bitbucket-pipelines.yml), the equivalent pipeline for the Bitbucket mirror of this project.

## Further documentation

The [`docs/`](juntos-de-nuevo/docs/) directory holds the project's design and handoff documents:

- `software-plan.md` — objective, MVP scope, business rules
- `database-handoff.md` / `database-script.sql` / `laravel-aux-tables.sql` / `configuracion-table.sql` — data model
- `backend-handoff.md`, `frontend-handoff.md`, `blade-handoff.md` — implementation decisions
- `blade-security-rules.md` — security rules for the views
- `reset-datos-mantener-admin.sql` — script to wipe test data while keeping the admin user

## Security

This system stores information about minors in a vulnerable situation: where they are, their state, and who collects them. Before deploying any change, verify:

- The role-based access rules (`CheckRole`, `EnsureCentroActivo`)
- That a Gestor can only operate on children from their own shelter
- That contact details (phone, email) are never exposed on public endpoints
- That the classes in `app/Validaciones/` validate the input before any write

If you find a vulnerability, please report it responsibly before discussing it publicly.
