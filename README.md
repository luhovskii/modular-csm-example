**Modular CMS - README**

- **Description:**: Small file-based modular PHP CMS intended as a lightweight example and starter kit. Includes a simple router, module loader, blog module, optional gallery module, session auth, CSRF helpers, and a small PSR‑4-like autoloader fallback.

**Quickstart:**
- **PHP built-in server (local development):**

```powershell
# from project root
php -S localhost:8000 -t public
```

- **Open:** `http://localhost:8000` (site) and `http://localhost:8000/admin/login` (admin)

**Project structure (important files):**
- **`public/`**: webroot and front controller `public/index.php`.
- **`core/`**: framework pieces (`bootstrap.php`, `Router.php`, `ModuleManager.php`, `Auth.php`, `Csrf.php`, `Middleware.php`).
- **`modules/`**: modules live here. Each module has a `module.json` and PHP files. Example modules:
  - `modules/blog` — Blog module (posts stored in `modules/blog/data/posts.json`).
  - `modules/gallery` — Gallery module (images in `modules/gallery/data/images.json`).
- **`storage/`**: writable runtime data (`users.json`, autoload debug log, etc.).
- **`diagnose.php`**: helper script to gather runtime info for shared hosting troubleshooting.

**Modules & how they register routes**
- Modules declare `namespace` and `main` in `module.json`. The `ModuleManager` loads each module's main class and calls `register($router)` so modules can add routes, e.g. `/blog`, `/gallery`.

**Auth & Security**
- Session-based authentication in `core/Auth.php` (passwords hashed). Admin users stored in `storage/users.json`.
- CSRF helper `core/Csrf.php` and `core/Middleware.php` helpers are used for form and AJAX protection. All admin forms include CSRF tokens.

**Autoloading**
- If `vendor/autoload.php` (Composer) is present it is used. Otherwise a small PSR‑4-like autoloader in `core/bootstrap.php` maps `Core\` -> `core/` and `Modules\` -> `modules/`.
- Additional fallbacks were added to tolerate filename/directory casing mismatches on case-sensitive hosts: case-insensitive path matching and a recursive filename basename lookup. A debug log for module autoload attempts is written to `storage/autoload_debug.log` when resolving `Modules\Gallery` classes.

**Common run & maintenance commands**
- Lint PHP files changed during development:

```powershell
php -l public/index.php
php -l core/bootstrap.php
php -l modules/blog/models/Post.php
```

- Test autoload for gallery controller (prints `true`/`false`):

```powershell
php -r "require 'core/bootstrap.php'; var_export(class_exists('Modules\\Gallery\\Controllers\\GalleryController'));"
```

- Helper test script added: `php tools/test_autoload.php` (prints result and shows `storage/autoload_debug.log` if present).

**Deployment notes (shared hosting / cPanel)**
- Preferred approach: point document root to the repository `public/` folder. If you cannot change the webroot, add an `index.php` shim in the host root to require `public/index.php` and add `.htaccess` rewrite rules to forward requests to `public/index.php`.
- Ensure `storage/` and module `data/` folders are writable by PHP on the host but are not web-accessible. If you cannot place them outside webroot, add an `.htaccess` with `Deny from all` in `storage/` (Apache) or protect with server config.
- Run `composer install` on the host (if available) or run it locally and upload `vendor/`.

**Troubleshooting: Handler class not found (gallery)**
- Symptom: `Handler class not found` when visiting `/gallery` — typically printed by `core/Router.php` when the controller class referenced by a route cannot be autoloaded.
- Root cause (common): filesystem case-sensitivity mismatch. On Windows `modules/gallery/controllers/` and namespace `Modules\\Gallery\\Controllers` may still work locally, but on Linux the directory names and namespace segments must match casing exactly.
- Recommended fix (canonical): rename directories on the host to match namespaces exactly. Example (SSH):

```bash
# from project root
mv modules/gallery modules/Gallery
mv modules/Gallery/controllers modules/Gallery/Controllers
mv modules/Gallery/models modules/Gallery/Models
mv modules/Gallery/views modules/Gallery/Views
```

- Mitigation included in this repo: `core/bootstrap.php` now attempts case-insensitive path resolution and a final recursive basename lookup to locate `GalleryController.php` even when directory names differ in case. Also the autoloader writes simple debug entries for `Modules\\Gallery` to `storage/autoload_debug.log`.
- To inspect autoload attempts: open `storage/autoload_debug.log` after reproducing the error.

**If you still see the error**
- Check `storage/autoload_debug.log` for entries and paste them here.
- Run the autoload test script:

```powershell
php tools/test_autoload.php
```

**Where to look for quick fixes**
- `core/bootstrap.php` — autoloader behavior and debug logging.
- `core/ModuleManager.php` — module discovery and requiring main file if the class is not already loaded.
- `public/index.php` — module registration and route setup.
- `diagnose.php` — runtime environment checks for shared hosting.

**Notes & contributors**
- This project is intended as an educational example and starter kit. Review security and sanitize input appropriately before production use.
- If you want, I can add a script to normalize module directory casing automatically (I won't run it without your approval).

---
Generated: concise README added by assistant.
# modular-cms-test

This repository contains a small modular PHP CMS used for development and experimentation.

Structure highlights:
- `core/` - framework core files (router, bootstrap, auth, etc.)
- `modules/` - modular features (e.g. `modules/blog`)
- `public/` - webroot and front controller

This README was created by an assistant to initialize a git repository.
