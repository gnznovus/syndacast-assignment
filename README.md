# Syndacast WordPress Developer Skills Assessment

Two-page responsive hotel website built for the Syndacast WordPress Developer Skills Assessment.

## Scope

- Home
- Contact Us
- Desktop implementation closely follows the supplied Figma design
- Tablet and mobile layouts are adapted responsively
- Main content is editable through WordPress Admin
- Contact Us includes a working contact form

## Local Development

### Requirements

- Docker Desktop / Docker Engine with Docker Compose

### Setup

```bash
cp .env.example .env
docker compose up -d
```

On the first run, MySQL imports the sanitized finished-site snapshot and the setup container installs the required plugins, activates the MUU theme, and updates the local site URL. Allow a short time for setup to finish.

To follow the setup process:

```bash
docker compose logs -f setup
```

Setup is complete once the `setup` container finishes successfully.

### Reviewer URLs

```text
Frontend: http://localhost:8080/
Admin:    http://localhost:8080/wp-admin/
```

If `WORDPRESS_PORT` is changed in `.env`, use that port instead of `8080`.

The Contact Us page is available from the site's navigation after setup.

### Reviewer Dashboard Access

```text
URL:      http://localhost:8080/wp-admin/
Username: reviewer
Password: muu-reviewer
```

These are intentionally public local-review credentials. Do not reuse them for a deployed website.

The database snapshot includes the completed Elementor pages, WordPress attachment records, Contact Form 7 configuration, theme settings, and the reviewer administrator. Contact submissions, comments, revisions, analytics/security logs, plugin caches, and personal account data are excluded.

If Docker volumes from an earlier installation already exist, reset them before testing the packaged snapshot:

```bash
docker compose down -v
docker compose up -d
```

The custom theme is mounted from:

```text
wp-content/themes/muu-hotel
```

The Docker setup service activates **MUU Hotel Assessment** automatically.

## Development Approach

The project uses a lightweight custom WordPress theme for the global visual system, shared layout, responsive behavior, and WordPress integration. Elementor Free is used for editable main page content, avoiding Elementor Pro-only functionality. Theme-owned MUU Controller shortcodes provide the custom navigation, expandable left tab, decorative shape, and Contact Form 7 presentation that Elementor Free does not include.

This split keeps the implementation maintainable and design-focused while ensuring the assessment's main content can be managed through WordPress Admin.

## Contact Form Behavior

Contact Form 7 handles form validation and submission processing, while Flamingo stores submitted messages in WordPress Admin for review.

In the packaged local Docker environment, outgoing email/SMTP is intentionally not configured. Because of this, Contact Form 7 may display its red mail-delivery error after submission.

**This is expected in the local review environment.**

The submission itself is still processed and stored successfully in:

**WordPress Admin → Flamingo → Inbound Messages**

A production deployment would require a configured WordPress mail transport or SMTP provider for reliable outbound email notifications.

## SEO, Performance & Accessibility

SEO readiness is treated as part of the theme architecture rather than being dependent on a specific SEO plugin.

The implementation aims to provide:

- semantic HTML5 landmarks and a logical heading hierarchy
- native WordPress document title support
- crawlable page content and clean internal navigation
- meaningful image alternative text managed through the WordPress Media Library
- WordPress responsive image support (`srcset` and `sizes`) where applicable
- lightweight theme assets with minimal JavaScript and third-party dependencies
- responsive layouts designed with Core Web Vitals in mind
- keyboard-friendly navigation and accessible form markup
- compatibility with common WordPress SEO plugins without duplicating or conflicting with their metadata output

Structured data will only be added where the required real business information is available; placeholder hotel data will not be exposed as production schema.

## Plugins

### Core plugins

Required for the implemented page-building and contact-form workflow:

- Elementor (Free)
- Contact Form 7
- Flamingo

Contact Form 7 handles validation and submission processing. Flamingo stores submitted contact messages in WordPress Admin.

### Nice-to-have plugins

Currently used in the local WordPress installation but not required by the MUU theme:

- All-In-One Security (AIOS)
- Burst Statistics
- UpdraftPlus
- WP-Optimize

The Docker setup service installs and activates the core plugins automatically. The optional plugins provide security hardening, privacy-friendly analytics, backups, and performance tooling and may be installed manually if desired. SEO fundamentals remain at theme/content level so the site stays compatible with a dedicated SEO plugin if one is introduced later.

## Design Reference

The supplied Figma/FigJam desktop layouts are treated as the primary visual reference. Responsive tablet/mobile layouts are adapted using the same visual language and standard responsive UX practices.

Typography follows the font assets supplied with the assessment where licensing permits their use in the submitted source.

## Implementation Priorities

1. Desktop Figma accuracy
2. Responsive tablet/mobile UX
3. WordPress-editable content
4. Working contact form
5. SEO, performance and accessibility fundamentals
6. Clean, maintainable implementation
7. Visual polish and QA
8. Documentation

## Status

Home and Contact Us implementations are complete and ready for final browser QA.
