## Task 1:
# Role & Objective
Act as a Senior Full-Stack Developer specializing in custom WordPress theme development and property booking systems. We are building a custom accommodation booking platform strictly for Erasmus students and Digital Nomads, similar to the "Homey" theme, but highly optimized, lightweight, and purpose-built. 

# Tech Stack & Constraints
- WordPress Custom Theme (no page builder dependencies).
- Tailwind CSS for styling (specifically utilizing backdrop-filter utilities for iOS Glassmorphism).
- Vanilla JavaScript (or Alpine.js) for frontend interactivity.
- Clean PHP templates using a modular component structure.

---

# CORE ARCHITECTURE

## 1. User Roles & Native Meta Data 
Create two distinct user roles natively: "Host" and "Student/Renter".
Extend the native WordPress user meta to include these fields in the database:
- For Students: Nationality, Sending University, Receiving University, Funding Source, Age, Emergency Contact.
- For Hosts: Verified Status (Boolean), Response Rate, Total Listings.

## 2. Custom Post Types (CPT) & Taxonomies
Create a CPT called "Properties".
Create custom taxonomies for: Property Type, Neighborhoods.
Native Custom Fields for Properties: Bills Included (Boolean), Study Desk Available (Boolean), Minimum Stay (Months), Distance to Universities.

## 3. Booking Engine Logic
1. Pending Request -> 2. Host Approval -> 3. Payment Pending -> 4. Confirmed -> 5. Payout.

---

## Task 2: Search Bar & Modals Refactoring (UI/UX & Logic)
Refactor the hero section search bar modals. They must adhere to a modern "iOS Glassmorphism" design aesthetic (e.g., using Tailwind's `bg-white/30 backdrop-blur-md border border-white/20 shadow-lg` classes).

**1.1 Date Picker Logic Overhaul:**
- **Decouple Move-in and Move-out:** Currently, clicking either triggers a forced sequential selection starting with Move-in. This is bad UX.
- **Independent Selection:** If a user clicks "Move-in", open a modal exclusively for selecting the start date. If they click "Move-out", open a modal for the end date.
- **Partial Dates Allowed:** Allow users to search with ONLY a Move-in date, or ONLY a Move-out date. Many students know their arrival date but haven't decided on their departure date yet. The search query must handle open-ended date ranges.

**1.2 Guest Selector Modal:**
- Replace the standard dropdown or basic input field for "Guests" with a custom interactive modal.
- The modal must match the Glassmorphism design system.
- Include + and - counter buttons for "Adults" and "Students" (if applicable) for a modern, Airbnb-style user experience.

## Task 3: Security & reCAPTCHA Integration
- Check the custom Theme Options panel for a Google reCAPTCHA v3 integration.
- If it doesn't exist, build a Theme Settings toggle to enable/disable reCAPTCHA.
- Add fields for "Site Key" and "Secret Key".
- Enforce reCAPTCHA validation on the "Registration", "Login", and "Booking Request" forms to prevent bot spam.

## Task 4: Invoice System & PDF Generation
- Create a comprehensive Invoice System for the booking engine.
- **Invoice Generation:** Generate a professional PDF invoice for every confirmed booking.
- **Invoice Content:** The invoice must include:
  - Property Details (Address, Title)
  - Tenant Details (Name, Email)
  - Landlord Details (Name, Contact)
  - Dates (Check-in, Check-out)
  - Price Breakdown (Monthly Rent, Deposit, Total)
  - Payment Status (Paid/Pending)
- **Storage:** Store the generated PDF in the WordPress Media Library and link it to the booking record.
- **Download:** Provide a "Download Invoice" button for both the Tenant and the Host on the booking details page.
- **Email:** Automatically email the invoice to the tenant upon successful payment.

## Task 5: Theme Options Panel - Invoice Settings
- Create a new section in the Theme Options panel specifically for Invoice Settings.
- **Settings Fields:**
  - **Enable Invoicing:** Toggle to enable/disable the invoice system.
  - **Company Name:** Name of the hosting company/platform.
  - **Company Address:** Address of the company.
  - **Company Logo:** Upload field for the company logo (to be displayed on the invoice).
  - **Bank Details:** Fields for bank account information (for payout purposes).
  - **Payment Terms:** Default payment terms text (e.g., "Payment due within 30 days").
  - **Tax Information:** Tax ID or VAT number field.
  - **Invoice Prefix:** Prefix for invoice numbers (e.g., "INV-").
  - **Next Invoice Number:** Auto-incrementing field for the next invoice number.

## Task 6: 
# Role & Objective
Act as a Senior Full-Stack Developer. We are adding an automated "Proof of Accommodation (Visa Document)" PDF generator to our custom WordPress accommodation platform. This document is strictly for non-EU Erasmus students applying for a National D-Visa. It must look highly official, formal, and meet Schengen/Greek consulate standards.

# 1. Database Update: New User Meta
A visa document is legally invalid without passport numbers. 
Update the Student User Profile logic we built earlier to include a new required field: `passport_number` (Encrypted or securely stored if possible, but accessible for PDF generation).
Update the Host User Profile to include: `tax_id` (AFM in Greece) or `id_card_number`.

# 2. PDF Generation Engine (Tech Stack)
- Use `dompdf` (via Composer) or WordPress's native capabilities to convert a pure HTML/CSS template into a downloadable PDF. 
- Do NOT use heavy third-party WordPress PDF plugins. Build it natively inside the custom theme (`/inc/pdf-generator.php`).

# 3. Trigger & Security Logic
- The "Download Visa Document" button must ONLY appear on the Student's Dashboard IF the Booking Status is exactly `Confirmed` (meaning payment is secured).
- Security: Implement strict permission checks. Only the user who made the booking (or the Host/Admin) can trigger the AJAX request to download this specific PDF. Prevent direct URL access to the generated files.

# 4. The PDF Template (HTML/CSS Structure)
Create a clean, corporate HTML template for the PDF. It must include the following dynamic data mapped from the database:

**Header:**
- ThessNest Platform Logo (Placeholder URL for now).
- Title: "OFFICIAL PROOF OF ACCOMMODATION FOR VISA APPLICATION"
- Issue Date: [Current Date]
- Booking Reference ID: [Custom Post Type ID]

**Section 1: Host / Landlord Details**
- Full Name: [Host Name]
- ID/Tax Number: [Host Tax ID]
- Contact: [Host Email & Phone]

**Section 2: Tenant / Student Details**
- Full Name: [Student Name]
- Passport Number: [Student Passport Number]
- Nationality: [Student Nationality]
- Sending University: [Sending University]

**Section 3: Property Details**
- Exact Address: [Property Address, City, Zip]

**Section 4: Tenancy Agreement Summary**
- Move-in Date: [Check-in]
- Move-out Date: [Check-out]
- Financials: "The first month's rent and platform fees have been successfully paid and secured in escrow via the ThessNest platform."

**Footer:**
- A digitally generated signature/stamp of the platform: "Verified by ThessNest Operations."
- Platform company address and contact details.

# First Task Action
Start by writing the PHP script to integrate `dompdf`, create the AJAX endpoint for the download button, and draft the HTML template with the dynamic WordPress data tags. Explain where to place these files in the theme structure.