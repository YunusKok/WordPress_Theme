# ThessNest Known Issues & Troubleshooting

This document tracks known server and environment issues, their root causes, and solutions for the ThessNest theme and ecosystem.

## 1. SEO Plugin Crash (WordPress "Critical Error" / White Screen)
**Issue:**
When writing long content on a page (e.g., the About page) while an SEO plugin like SureRank, Rank Math, or Yoast SEO is active, WordPress crashes and displays the "There has been a critical error on this website" screen.

**Root Cause:**
The hosting server is missing the required PHP extensions `mbstring` (Multibyte String) and `gd` (or `imagick`), which modern plugins use to process strings and image files. Without these extensions, the SEO plugin triggers a PHP Fatal Error (e.g., `Call to undefined function mb_strrpos()`), bringing the whole page down.

**Solutions:**

- **Quick Fix (Temporary):** 
  Deactivate the SEO plugin (SureRank, Rank Math, etc.) from the WordPress Admin Dashboard. The site will immediately recover and function normally, but search engine optimization tools will be disabled.

- **Permanent Fix (Recommended):** 
  Log into your hosting server's control panel (cPanel, Plesk, Hostinger Panel, etc.), navigate to the **PHP Settings / Select PHP Version** page, and check/enable the `mbstring` and `gd` parameters under PHP Extensions. Once enabled, SEO plugins can be reactivated securely and the site will not crash.
