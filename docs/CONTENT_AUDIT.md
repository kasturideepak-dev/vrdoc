# Part 1 — Content audit

Crawled 10 September 2026.

- Source of truth: https://vrdoctors.in/ (WordPress theme; page bodies are in the theme, not `wp-json` content)
- Clone: https://vr-doctors-clone.vercel.app/ (Next.js; production deploy is older than local source)

Sitemap pages on vrdoctors.in:

`/`, `/about/`, `/bipc-careers/`, `/courses/`, `/results/`, `/contact/`, `/best-bipc-college-in-hyderabad-neet-residential/`, `/long-term-neet-program-hyderabad/`, `/short-term-neet-program-hyderabad/`, plus leftover `/hello-world/` (WordPress default).

## Diff table

| Page | Status | What needs to change | Priority |
|---|---|---|---|
| Home `/` | Outdated | Clone `<title>` is “Best Residential NEET Coaching in Hyderabad”; live is “BiPC Junior College in Hyderabad with NEET Coaching \| VR Doctors”. Meta description on live is the BiPC junior-college line. Stats/testimonials/campus/approach match. WhatsApp FAB still uses VR Jr number `15559412484`. | P0 |
| About `/about` | Match (copy) / Outdated (SEO + typos) | Body matches. Live Yoast title is the weak “About - VR Doctors”. Faculty card “Sr Chemisty” → “Sr. Chemistry”. | P1 |
| World of BiPC `/bipc-careers` | Outdated (SEO) | Same explorer UI; live title/description are the “Career in BiPC…” Yoast strings. Clone title was “BiPC Careers \| VR Doctors Academy”. | P1 |
| Courses `/courses` | Outdated (SEO) | Same three-program layout. Live title: “NEET Coaching Courses in Hyderabad \| VR Doctors Academy”. | P1 |
| Results `/results` | Outdated (typos) | Achievers match. Fix “Governamanet Medical College” and “Bidar institute…”. Live title is “Results - VR Doctors” (Yoast default); use the stronger clone title. | P1 |
| Contact `/contact` | Outdated (form) | Live still renders Contact Form 7 (name/email/subject/message). Clone has the admissions callback form — **keep the clone form** (it is the intended product). NAP matches footer. | P0 |
| BiPC + NEET residential `/best-bipc-college-in-hyderabad-neet-residential` | **Missing on Vercel (404)** | Page exists in local Next source and is live on vrdoctors.in. Must ship in the Next deploy. | P0 |
| Long-term `/long-term-neet-program-hyderabad` | **Missing on Vercel (404)** + **Outdated in local source** | Live (7 Sep 2026): **8–10 month NEET-only** for Intermediate completers (Focus-45). Local clone still described a **2-year Intermediate + NEET** programme. | P0 |
| Short-term `/short-term-neet-program-hyderabad` | **Missing on Vercel (404)** + **Outdated in local source** | Live: **45–60 day crash course** (Jan–Apr, no IPE). Local clone still described a **1-year repeater**. | P0 |
| `/hello-world/` | Extra on live | WordPress default post. 301 to `/`. Not needed on the clone. | P2 |
| Nav Courses dropdown | Outdated on Vercel HTML | Live nav lists the three landings. Clone source has the dropdown; Vercel 404s the targets. | P0 |
| Footer NAP | Match | Plot No 29, Mathrusree Nagar, Hafeezpet, Miyapur, 500049. Phones 9256925640–43. `admissions@vrdoctors.in`. Social: Facebook `VR.Jr.College`, Instagram `vr_junior.college`, YouTube `@VR_JuniorCollege`. Add the missing space after “29,”. | P2 |
| Program-page CTA NAP | Outdated | Course landing CTAs used Plot No **30** (VR Jr College HO) and phones **7097098877 / 7097098811**. Align to Plot No 29 + 9256 numbers. | P0 |
| Blog | Neither site has real posts | Live only has “Hello world!”. No blog UI on the clone. CMS includes a blog module for later. | P3 |

## Sections on live not in the clone layout

None of the core marketing sections are missing on pages that exist. The gap is **three course landings 404 on Vercel**, plus **rewritten long/short-term copy** on live that the clone had not absorbed.

Live contact still shows a leftover CF7 block the clone already replaced with a proper admissions form — do not port CF7 backwards.

## Corrected copy (page by page)

### Global

- Brand: **VR Doctors Academy**
- Tagline: Vision Into Reality
- Phones: +91 9256 9256 40 / 41 / 42 / 43
- Email: admissions@vrdoctors.in (program pages also mentioned info@vrdoctorsacademy.com)
- WhatsApp: 919256925640
- Address: Plot No 29, Mathrusree Nagar, Hafeezpet, Miyapur, Hyderabad, Telangana 500049
- Maps: https://maps.app.goo.gl/3qrkSSCw6kiYNkwH8

### Home SEO

- Title: `BiPC Junior College in Hyderabad with NEET Coaching | VR Doctors`
- Description: `VR Doctors Academy is a top residential BiPC junior college in Hyderabad offering integrated NEET coaching, expert faculty & proven results. Book a visit today.`

### Long-term (live, 7 Sep 2026)

- Title: `Long-Term NEET Coaching in Hyderabad | VR Doctors Academy`
- Description: `VR Doctors Academy's Long-Term NEET Coaching in Hyderabad is an 8–10 month residential NEET course (Focus-45) in Hyderabad for Intermediate completers. Book a campus visit today.`
- Eligibility: completed Intermediate (BiPC)
- Duration: 8–10 months
- Structure: NEET-only, NCERT, no IPE
- Daily: 5 hours class + 5–6 hours supervised study

### Short-term (live, 7 Sep 2026)

- Title: `Short-Term NEET Coaching in Hyderabad | VR Doctors Academy`
- Description: `VR Doctors Academy's Short-Term NEET Coaching in Hyderabad is a 45–60 day residential NEET crash course in Hyderabad. Book a campus visit today.`
- Duration: 45–60 days (typically January to April)
- Daily: 6 hours class + 5–6 hours supervised study
- Mentor ratio: ~15:1

### Faculty (About)

| Name | Role | Experience |
|---|---|---|
| KVR Sir | Academic Head | 37+ years |
| G Ashok | Sr. Physics | 22+ years |
| B Nagesh | Sr. Botany | 20+ years |
| M Srinath | Sr. Zoology | 8+ years |
| P Malyadri | Sr. Chemistry | 18+ years |
| K Prabhakar Reddy | Sr. Zoology | 16+ years |

### Home testimonials

- Anumalla Akshitha — Osmania Medical College — 617/720
- Annangi Akhila — Osmania Medical College — Govt seat
- Bisaoi Vamshi Krishna — Gandhi Medical College — AIR 507
- Dodla Vaishnavi — Government Medical College, Siddipet — MBBS seat

These strings are now in `database/seed.php`.
