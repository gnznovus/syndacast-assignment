# Syndacast WordPress Developer Skills Assessment

Responsive two-page hotel website built for the Syndacast WordPress Developer Skills Assessment.

## Project Overview

The implementation contains:

- **Home**
- **Contact Us**
- desktop layouts closely following the supplied Figma/FigJam design
- responsive tablet and mobile adaptations
- WordPress-editable main page content
- a working Contact Form 7 form with submissions stored through Flamingo
- theme-owned reusable MUU components for UI that Elementor Free does not provide

The project uses a lightweight custom WordPress theme with Elementor Free for editable page content. The repository also contains a sanitized finished-site database snapshot and committed media so a reviewer can reconstruct the completed site locally.

## Quick Start

### Requirements

- Docker Desktop / Docker Engine with Docker Compose
- internet access during the first setup so required WordPress plugins can be installed automatically

### Setup

```bash
cp .env.example .env
docker compose up -d
```

On first run:

1. MySQL imports the packaged sanitized site snapshot.
2. WordPress starts using the committed MUU theme and uploads.
3. The setup container installs and activates the required plugins.
4. The MUU theme is activated.
5. The local site URL is updated for the configured port.
6. WordPress rewrite rules are flushed.

Follow setup progress with:

```bash
docker compose logs -f setup
```

Setup is complete when the `setup` container exits successfully.

### Reviewer Access

| | URL / Value |
| --- | --- |
| Frontend | `http://localhost:8080/` |
| Admin | `http://localhost:8080/wp-admin/` |
| Username | `reviewer` |
| Password | `muu-reviewer` |

If `WORDPRESS_PORT` is changed in `.env`, use that port instead of `8080`.

The WordPress service is intentionally bound to `127.0.0.1`, keeping the packaged reviewer account local to the host machine. These credentials exist only for assessment review and must not be reused for a deployed website.

If Docker volumes from an older run already exist, rebuild from the packaged snapshot with:

```bash
docker compose down -v
docker compose up -d
```

## Development Approach

The site uses a **custom theme + Elementor Free** approach.

Elementor owns the main editable page content, while the theme owns the shared visual system, responsive behavior, fonts, WordPress integration, and reusable components that would otherwise require Elementor Pro or duplicated page markup.

This keeps page content editable for non-developers without moving core component behavior into page-builder-specific hacks.

### Theme Structure

```text
wp-content/themes/muu-hotel/
├── assets/
│   ├── css/
│   └── fonts/
├── inc/
│   └── muu-element-controller/
│       ├── assets/
│       │   ├── css/
│       │   ├── images/
│       │   └── js/
│       ├── includes/
│       │   ├── class-muu-admin-controller.php
│       │   ├── class-muu-element-controller.php
│       │   └── class-muu-footer-controller.php
│       └── bootstrap.php
├── templates/
├── footer.php
├── front-page.php
├── functions.php
├── header.php
├── page.php
├── screenshot.png
├── style.css
└── theme.json
```

The controller layer is separated by responsibility:

- **MUU Element Controller** — navigation/left-tab, orange-shape and contact-form shortcodes, frontend assets, fonts, and component settings contracts.
- **MUU Footer Controller** — footer settings contract and footer shortcode rendering.
- **MUU Admin Controller** — MUU Controller dashboard UI, AJAX panels, shortcode documentation, settings forms, and Media Library integration.

## MUU Controller

The theme adds **MUU Controller** to WordPress Admin as a single place to configure and document theme-owned components.

The admin UI provides:

- an Overview panel
- shortcode documentation
- Navigation and Footer component sections
- grouped component settings
- copyable shortcode examples
- Media Library integration for footer artwork

### Theme Shortcodes

| Shortcode | Purpose |
| --- | --- |
| `[muu_nav_lefttab]` | Responsive navigation with optional expandable left-tab content |
| `[muu_orange_shape]` | Decorative configurable orange artwork |
| `[muu_contact_form]` | Contact Form 7 presentation integrated with the MUU visual system |
| `[muu_footer]` | Reusable responsive footer |

Example navigation without the expandable left tab:

```text
[muu_nav_lefttab left-tab="false"]
```

Responsive color overrides are supported where the navigation sits over different desktop/mobile backgrounds:

```text
[muu_nav_lefttab left-tab="false" logo-color="#000" nav-color="#000" mobile-logo-color="#fff" mobile-nav-color="#fff"]
```

Additional usage and supported arguments are available directly from **WordPress Admin → MUU Controller → Shortcodes**.

## Responsive Implementation

Desktop follows the supplied design as closely as practical. Tablet and mobile layouts were adapted where the source design did not prescribe every responsive state.

Responsive work includes:

- navigation color changes based on desktop/mobile background context
- expandable navigation layout across desktop, tablet, and mobile
- tablet positioning adjustments for navigation content
- mobile full-width expanded navigation content
- responsive social/link positioning
- responsive footer composition
- mobile content simplification where long navigation copy would reduce usability
- responsive Elementor section/container adjustments on both pages

The final responsive Elementor state is persisted in the packaged database snapshot.

## Contact Form

Contact Form 7 handles validation and submission processing. Flamingo stores submitted messages in WordPress Admin at:

**Flamingo → Inbound Messages**

Outgoing SMTP/mail transport is intentionally not configured in the packaged local Docker environment. Contact Form 7 may therefore display a mail-delivery error after processing a submission locally.

The submission itself is still stored by Flamingo. A production deployment would require a configured WordPress mail transport or SMTP provider for reliable outbound notifications.

## Plugins

### Required

- **Elementor (Free)** — editable main page content and page composition
- **Contact Form 7** — contact-form validation and submission handling
- **Flamingo** — stores submitted contact messages in WordPress Admin

The Docker setup service installs and activates these plugins automatically.

### Optional / Development Environment

The development installation also used the following non-required plugins:

- All-In-One Security (AIOS)
- Burst Statistics
- UpdraftPlus
- WP-Optimize

They are not required by the MUU theme or the packaged reviewer setup.

## SEO, Performance & Accessibility

The implementation keeps these concerns primarily at theme/content level rather than requiring a specific optimization or SEO plugin.

Key considerations include:

- semantic HTML5 landmarks and logical heading structure
- native WordPress document-title support
- crawlable page content and internal navigation
- image alternative text managed through WordPress Media Library
- WordPress responsive image support where applicable
- lightweight theme-owned JavaScript/CSS
- locally supplied theme fonts
- keyboard-friendly navigation
- accessible form markup
- compatibility with common WordPress SEO plugins without duplicating their metadata output

Structured data is intentionally not populated with placeholder hotel information.

## Security & Reviewer Packaging

The reviewer package is designed for local assessment use:

- environment-specific values are read from `.env`; the real `.env` is not committed
- WordPress debug mode defaults to disabled
- the WordPress HTTP port binds to localhost only
- the MySQL service is not published to the host
- the database snapshot is sanitized for assessment delivery
- public reviewer credentials are explicitly local-review credentials

The snapshot includes the completed Elementor pages, attachment records, Contact Form 7 configuration, theme/controller settings, and reviewer administrator.

Contact submissions, comments, revisions, analytics/security logs, plugin caches, and personal account data are excluded from the packaged snapshot.

## Reproducibility Verification

The final package was tested from a separate fresh Git clone using new Docker containers and volumes.

The clean setup successfully reconstructed the completed site from repository contents, including the final responsive Elementor layouts, committed media/assets, theme settings, required plugins, and WordPress theme.

This test was performed independently from the original development Docker environment to ensure the repository itself contains the state required for review.

## Design Reference

The supplied Figma/FigJam desktop layouts are the primary visual reference. Tablet and mobile layouts use the same visual language with responsive adaptations where needed.

Typography uses font assets supplied with the assessment where licensing permits their inclusion in the submitted source.

## Repository Contents

The main reviewer-specific packaged data is:

```text
config/db/01-wordpress.sql        Sanitized finished-site database snapshot
wp-content/uploads/              Committed Media Library assets
wp-content/themes/muu-hotel/     Custom MUU Hotel theme
.env.example                     Local environment template
docker-compose.yml               Reproducible local reviewer environment
```

## Status

**Complete and submission-ready.**

Home and Contact Us have been implemented and reviewed across desktop, tablet, and mobile. The packaged repository has also been verified from a clean Git clone with fresh Docker volumes.
