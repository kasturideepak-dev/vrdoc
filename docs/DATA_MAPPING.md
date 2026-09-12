# Public PHP templates → MySQL

The production site is **PHP templates** in `views/public/site/`, reading MySQL directly (same host as `/admin/`).

A JSON API at `/api/v1/` still exists if you need it. Trailing slashes required.

## Mapping

| Frontend section | Post type / table | API |
|---|---|---|
| Site NAP, social, WhatsApp, stats | `settings` | `GET /api/v1/settings/` and `GET /api/v1/bootstrap/` |
| Header / footer nav | `menus`, `menu_items` | `GET /api/v1/menus/` |
| Home / About / Courses / Results / Contact / landings SEO | `pages` + `seo_metadata` | `GET /api/v1/pages/` and `GET /api/v1/pages/{slug}/` |
| Faculty grid | CPT `faculty` | `GET /api/v1/content/faculty/` |
| Course cards + landings | CPT `courses` | `GET /api/v1/content/courses/` and `/api/v1/content/courses/{slug}/` |
| Results hall of fame | CPT `achievers` | `GET /api/v1/content/achievers/` |
| World of BiPC explorer | CPT `careers` | `GET /api/v1/content/careers/` |
| Academic subjects | CPT `departments` | `GET /api/v1/content/departments/` |
| Campus address | CPT `campuses` | `GET /api/v1/content/campuses/` |
| Results college gallery | CPT `colleges` | `GET /api/v1/content/colleges/` |
| About timeline | CPT `timeline` | `GET /api/v1/content/timeline/` |
| Home / landing quotes | `testimonials` | `GET /api/v1/testimonials/` |
| FAQs | `faqs` | `GET /api/v1/faqs/` |
| Blog (future) | `blog_posts` | `GET /api/v1/blog/` |
| Appointment form | `forms.slug = appointment` | `GET /api/v1/forms/appointment/` · `POST /api/v1/forms/appointment/` (alias `POST /api/v1/leads/`) |
| Sitemap / robots | generated | `GET /sitemap.xml` · `GET /robots.txt` |

`GET /api/v1/bootstrap/` returns settings, menus, and all list endpoints in one payload.

## Form payload (appointment)

```json
{
  "student_name": "Ada",
  "parent_name": "Parent",
  "mobile": "9876543210",
  "current_class": "Inter 2nd Year",
  "interested_course": "Long-Term NEET",
  "residential": "Yes",
  "website": ""
}
```

`website` is the honeypot. Submissions land in **Admin → Leads** and, if configured, Google Sheets.
