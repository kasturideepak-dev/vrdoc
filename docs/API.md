# Public REST API

All routes are JSON. Trailing slash required. CORS is allowlisted in Settings (`api_cors_origins`).

| Method | Path | Notes |
|---|---|---|
| GET | `/api/v1/bootstrap/` | Settings + menus + all lists |
| GET | `/api/v1/settings/` | NAP, social, stats |
| GET | `/api/v1/menus/` | `header` and `footer` |
| GET | `/api/v1/pages/` | Published pages + SEO |
| GET | `/api/v1/pages/{slug}/` | Use `home` for `/` |
| GET | `/api/v1/content/{type}/` | Published CPT entries |
| GET | `/api/v1/content/{type}/{slug}/` | One entry + SEO + sections |
| GET | `/api/v1/testimonials/` | Visible quotes |
| GET | `/api/v1/faqs/` | |
| GET | `/api/v1/blog/` | |
| GET | `/api/v1/blog/{slug}/` | |
| GET | `/api/v1/forms/{slug}/` | Field schema |
| POST | `/api/v1/forms/{slug}/` | Lead ingest |
| POST | `/api/v1/leads/` | Alias of `appointment` |
| GET | `/sitemap.xml` | |
| GET | `/robots.txt` | |

POST body for `appointment`: `student_name`, `parent_name`, `mobile`, `current_class`, `interested_course`, `residential`, `message`, plus honeypot `website` (must be empty).
