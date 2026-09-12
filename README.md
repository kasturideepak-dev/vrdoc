# VR Doctors Academy — PHP + MySQL site

Public pages and `/admin/` in one PHP + MySQL app. No WordPress, Laravel, or Node.

Live: https://vrdoctors.in/

## Hostinger

Follow `START-HERE.txt`. Upload this folder into `public_html` (or `public_html/vrdoc`). You must see `install.php` next to `app/` and `views/`.

## Local

```bash
php database/install.php
php -S 127.0.0.1:8080 -t public public/router.php
```

- Site: http://127.0.0.1:8080/
- Admin: http://127.0.0.1:8080/admin/login/
- Email: `admin@vrdoctors.in`
- Password: `ChangeMe_VRDR2026` (change after first login)

Schema: `database/schema.sql`. `docs/` has the content audit and data map.
