# ThessNest — WordPress Directory Theme for Mid-Term Housing

[![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-0073aa.svg)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-8892BF.svg)](https://www.php.net/)
[![License](https://img.shields.io/badge/License-GPL%20v2-green.svg)](https://www.gnu.org/licenses/gpl-2.0.html)
[![Version](https://img.shields.io/badge/Version-2.0.0-orange.svg)](https://thessnest.com)

A premium WordPress directory theme built for mid-term housing platforms. Designed specifically for Erasmus students and Digital Nomads in Thessaloniki, ThessNest combines an iOS-inspired glassmorphism design system with a full-featured booking and property management engine.

---

## Requirements

| Requirement | Minimum |
|---|---|
| WordPress | 6.0 |
| PHP | 7.4 |
| MySQL | 5.7 / MariaDB 10.3 |

---

## Features

### Booking & Availability
- Booking engine with manual or instant-approval modes
- Two-way iCal sync with Airbnb, Booking.com, and HousingAnywhere
- Interactive availability calendar for landlords
- Seasonal and custom-period pricing rules

### Legal & Compliance
- Accommodation proof generator for Schengen and National (Type D) visa applications
- Digital lease agreement system with auto-generation on booking confirmation
- Automated PDF invoicing with sequential numbering

### Payments
- Native Stripe API integration (no WooCommerce required)
- Native PayPal REST API integration
- Host payout management with minimum threshold controls
- Platform commission configuration

### Trust & Verification
- KYC host identity verification with document upload
- Verified Landlord badge system
- Google reCAPTCHA v3 on all forms

### Reviews
- Multi-criteria review system: Cleanliness, Communication, Location, Value, Check-in
- Verified-tenant-only reviews
- Automatic average rating calculation per property

### User Experience
- Frontend property submission with multi-step form
- Landlord and Tenant custom user roles
- Frontend dashboard with analytics, bookings, messages, and payouts
- Roommate matching engine
- Direct messaging between tenants and landlords
- Wishlist / saved properties
- Neighborhood guides

### Search & Discovery
- Advanced AJAX-powered property search with live filtering
- Leaflet.js map view with property pins
- Filter by neighborhood, amenity, price range, target group, and availability

### Design System
- iOS Liquid Glass design system built on CSS custom properties
- Three header styles: Modern (floating pill), Classic, Transparent
- Mobile-first responsive layout
- Dark mode support
- Spring-physics animations via custom cubic-bezier curves

---

## Installation

1. Upload the `thessnest` folder to `/wp-content/themes/`.
2. Activate the theme via **Appearance > Themes**.
3. Install required plugins from the notification banner (ThessNest Core, Redux Framework).
4. Go to **ThessNest Dashboard** and click **Run Auto-Setup** to create pages, menus, and demo content automatically.
5. Configure the theme via **ThessNest Options** in the admin sidebar.

---

## Folder Structure

```
thessnest/
├── assets/                  # Static images and icons
├── demo-data/               # One-click demo import files (XML, JSON)
├── inc/                     # Theme configuration files
│   ├── admin-front-page.php # Admin dashboard page
│   ├── admin-menu.php       # Admin menu registration
│   ├── customizer.php       # WordPress Customizer settings
│   ├── customizer-front-page.php
│   ├── demo-setup.php       # One-click setup handler
│   ├── ocdi-config.php      # One Click Demo Import configuration
│   ├── redux-config.php     # Redux Framework options panel
│   ├── redux-helpers.php    # Redux helper functions
│   ├── seo-tags.php         # SEO meta tag output
│   ├── tgm-config.php       # TGM Plugin Activation config
│   └── elementor/           # Elementor widget definitions
├── js/                      # Frontend JavaScript
├── languages/               # Translation files (.pot)
├── plugins/
│   └── thessnest-core/      # Companion plugin (CPTs, booking, payments)
├── template-parts/          # Reusable template components
├── demo-data/               # OCDI demo content
├── 404.php
├── archive-property.php
├── comments.php
├── footer.php
├── front-page.php
├── functions.php
├── header.php
├── index.php
├── page.php
├── search.php
├── single-property.php
├── singular.php
├── style.css                # Theme header + design system variables
├── template-about.php
├── template-add-listing.php
├── template-checkout.php
└── template-dashboard.php
```

---

## Child Theme

To safely customize ThessNest without losing changes on updates, use a child theme.

**`style.css`** (child theme):
```css
/*
Theme Name:   ThessNest Child
Template:     thessnest
Version:      1.0.0
*/
```

**`functions.php`** (child theme):
```php
<?php
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'thessnest-child-style',
        get_stylesheet_uri(),
        array( 'thessnest-style' ),
        wp_get_theme()->get( 'Version' )
    );
} );
```

All hooks, filters, and functions in `functions.php` are prefixed with `thessnest_` and wrapped in `function_exists()` checks, making them safely overridable from a child theme.

---

## Companion Plugin

Core functionality (Custom Post Types, booking engine, payments, iCal sync, invoicing) lives in the **ThessNest Core** plugin located at `plugins/thessnest-core/`. This separation follows Envato ThemeForest's Plugin Territory guidelines — the theme activates correctly without the plugin, and the plugin can be updated independently.

---

## Translation

The theme is translation-ready. The `.pot` template file is located at `languages/thessnest.pot` and contains all 800+ translatable strings.

To add a translation:
1. Install [Loco Translate](https://wordpress.org/plugins/loco-translate/).
2. Go to **Loco Translate > Themes > ThessNest**.
3. Click **New Language**, select your locale, and start translating.

Alternatively, open `languages/thessnest.pot` in [Poedit](https://poedit.net/) to generate `.po` and `.mo` files manually.

---

## Third-Party Resources

| Resource | License | Purpose |
|---|---|---|
| [Swiper.js](https://swiperjs.com) | MIT | Property image carousels |
| [Flatpickr](https://flatpickr.js.org) | MIT | Date picker |
| [Leaflet.js](https://leafletjs.com) | BSD-2-Clause | Interactive maps |
| [Inter Typeface](https://rsms.me/inter/) | SIL OFL 1.1 | UI font (via Google Fonts) |
| [Redux Framework](https://redux.io) | GPL v3 | Theme options panel |
| [TGM Plugin Activation](http://tgmpluginactivation.com) | GPL v2 | Plugin install notices |

---

## License

ThessNest is licensed under the **GNU General Public License v2.0 or later**.

```
Copyright (C) 2026 ThessNest Team <hello@thessnest.com>

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
```

See [LICENSE](LICENSE) for the full license text.

> **Note for ThemeForest buyers:** Your purchase grants you a Regular or Extended License under Envato's licensing terms. The underlying code is GPL v2, which means you may modify it freely for your own use. Redistribution or resale of the theme itself is not permitted under the Envato license.

---

## Support & Documentation

- Full setup guide: `setup_instructions.md`
- Support: [hello@thessnest.com](mailto:hello@thessnest.com)
- Demo: [https://thessnest.com/demo](https://thessnest.com/demo)
