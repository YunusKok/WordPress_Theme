# ThessNest Theme Setup & Configuration Guide

This document is prepared to standardize the installation, configuration, and deployment processes of the ThessNest WordPress theme. Please follow the steps in order for a seamless setup.

---

## 1. System Prerequisites
To ensure system stability, make sure your hosting environment meets the following minimum requirements:
- **WordPress:** v6.0+
- **PHP:** v7.4+ (v8.0+ recommended)
- **Database:** MySQL 5.7+ or MariaDB 10.3+
- **Security:** Valid SSL certificate (HTTPS is mandatory)

---

## 2. One-Click Setup (Recommended)
To minimize development time and eliminate manual errors, we highly recommend using the theme's built-in setup wizard.

1. Navigate to the **ThessNest Dashboard** from your WordPress admin menu.
2. Scroll to the bottom of the page to find the **"🚀 One-Click Theme Setup"** module.
3. Click the **Run Auto-Setup** button to initialize the process.

> [!TIP]
> **What does Auto-Setup do?**
> - Sets the Permalink structure to `/%postname%/`.
> - Generates required core pages (*Home, Dashboard, Add Listing, About Us, Contact*) with their respective page templates.
> - Configures the "Home" page as the static Front Page.
> - Creates the "Main Menu", appends core pages, and assigns it to the Header location.
> - Injects necessary taxonomies (Neighborhoods, Amenities, Target Groups) into the system.
> - Populates 4 mock data entries (Dummy Properties) for UI/UX testing.

*Note: If you have successfully completed the Auto-Setup, you can skip Step 3 (Manual Setup) and proceed directly to Step 4.*

---

## 3. Manual Setup
*Execute these steps only if you did not use or encountered issues with the Auto-Setup wizard.*

### 3.1. Permalinks
Flushing permalinks is required for Custom Post Type (CPT) routing to work correctly.
1. Navigate to **Settings > Permalinks**.
2. Select the **Post name** option and save changes.

### 3.2. Static Front Page Configuration
1. Go to **Pages > Add New**, create an empty page named "Home", and publish it (the `front-page.php` template will trigger automatically).
2. Navigate to **Settings > Reading**.
3. Change the **"Your homepage displays"** setting to **"A static page"**.
4. Select your newly created "Home" page from the **Homepage** dropdown and save.

### 3.3. Navigation Menu (Routing)
1. Go to **Appearance > Menus** and create a new menu named "Main Menu".
2. Under **Display location**, check **"Primary Menu (Currently set to: Header)"**.
3. Add the core pages (Home, About Us, Contact, etc.) to the menu.
4. For the property listing page, use **Custom Links** to add a link with the URL `/properties/` and the Link Text "Our Solutions".
5. Save changes. (You can also create a separate menu for the "Footer Menu" location if needed).

### 3.4. Generating Core Pages
Create the following pages and assign the specified templates:
- **Dashboard:** Title: `Dashboard`, Slug: `dashboard`, Template: `Frontend Dashboard`
- **Add Listing:** Title: `Add Listing`, Slug: `add-listing`, Template: `Add Listing`
- **About Us:** Title: `About Us`, Template: `About Page`

> [!WARNING]
> Due to hardcoded routing, the slugs for the Dashboard and Add Listing pages MUST be exactly `dashboard` and `add-listing`. Additionally, ensure you delete any conflicting pages left over from previous themes.

---

## 4. Plugin and Theme Configuration

### 4.1. Core Dependencies
Upon activating the theme, install and activate the following core dependencies via the TGM Plugin Activation prompt:
- **Redux Framework** (For the Theme Options Panel - *Critical*)
- **WP Mail SMTP** (For transactional emails - *Critical*)
- **Loco Translate** (For i18n/localization - *Critical*)

### 4.2. ThessNest Options Panel (Redux)
Global theme settings are managed via the Redux Framework. Navigate to **ThessNest > ThessNest Options** and configure the following:
- **General:** Google Maps API Key integration.
- **Logos & Favicon:** Site logos and favicon definitions.
- **Booking & Price:** Rental rules (min/max days), deposit rates, and currency settings.
- **Styling:** Global color palette (Accent color) and UI component settings (Dark mode, etc.).
- **Live Chat:** Third-party chat script integrations (e.g., Tidio, Tawk.to).

### 4.3. Customizer Settings (Hero Section)
1. Navigate to **Appearance > Customize > Homepage Settings > Hero Section**.
2. Upload the Hero Background Image (min. 1920x1080px recommended) and configure typography settings.

---

## 5. Data and Content Management

### 5.1. Taxonomies
For the Search & Filter system to function, you must input predefined data into the following taxonomies:
- **Neighborhoods:** City/District hierarchy (e.g., Ladadika, Kalamaria).
- **Amenities:** Property features (e.g., Wi-Fi, Air Conditioning, Balcony).
- **Target Groups:** Target audience (e.g., Student, Digital Nomad).

### 5.2. User Roles & Access Control (ACL)
The system incorporates two primary Custom User Roles:
- **Landlord:** Has privileges to create/edit properties and manage KYC documentation.
- **Tenant:** Has privileges to bookmark properties, submit booking requests, and use the messaging system.

---

## 6. Infrastructure and Integration

### 6.1. SMTP Mail Integration
SMTP configuration is mandatory for asynchronous system notifications (Booking requests, KYC approvals, Contact forms) to be delivered reliably.
1. Enter your corporate email credentials (or use providers like SendGrid/Mailgun) via the **WP Mail SMTP** plugin.
2. Verify the connection by sending a test email from the Tools tab.

### 6.2. Localization (i18n)
ThessNest is developed to be fully translation-ready (based on `.pot` files).
- You can translate the theme into your target language using the **Loco Translate** plugin. You only need to translate the missing/required strings.

---

## 7. Pre-Flight Checklist (Deployment Validation)

Verify the following steps before deploying the system to Production:
- [ ] Auto-Setup completed OR manual configuration successfully applied.
- [ ] Permalink flush executed (`/%postname%/`).
- [ ] Redux Panel settings (especially the Google Maps API key) configured.
- [ ] Hero image optimized and uploaded.
- [ ] SMTP test passed successfully (emails are not landing in spam).
- [ ] End-to-end booking flow tested with Custom User Roles (Landlord/Tenant).
- [ ] Security (SSL) activated and mixed-content errors resolved.
