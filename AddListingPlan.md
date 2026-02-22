# Professional Frontend Submission (Level 3) Implementation Plan

## Goal Description
The objective is to create a fully professional "Add Listing" system where users (e.g., landlords, property managers) can register, log in, and submit new [property](file:///c:/Users/ASUS/Desktop/wordpress%20tema/inc/core-functionality.php#18-73) listings directly from the frontend of the website. Crucially, this system must integrate seamlessly with our custom **iOS Liquid Glassmorphism** design system, avoiding heavy third-party directory plugins (like HivePress) that would overwrite or break our custom CSS.

To achieve this, we will build a **Custom Frontend Submission Engine** using WordPress native functions (`wp_insert_post`), AJAX, and secure form handlers.

## User Review Required
> [!IMPORTANT]  
> Are you okay with creating a custom "Landlord" user role? This means when someone registers to add a listing, they will be given the role of Landlord rather than Subscriber.
> 
> Also, default behavior will set newly submitted properties to **"Pending Review"** so you as the admin can check them before they go live on the site. Please confirm if you want this, or if you prefer them to be published instantly.

## Proposed Architecture & Changes

The implementation will be divided into 4 core phases.

### Phase 1: User Roles & Authentication
We need a secure way for users to log in and manage their listings.
- Register a custom `landlord` user role with specific capabilities (can edit their own properties, but cannot access the main WP admin dashboard).
- Create a custom Login/Registration modal or page using our glassmorphism design.

### Phase 2: The "Add Listing" Frontend Template
Create a beautiful, multi-step (or single long-scroll) frontend form.
#### [NEW] `template-add-listing.php`
- A dedicated page template containing the HTML/PHP form.
- Form fields will include: Property Title, Description, Rent Price, Neighborhood (Dropdown), Amenities (Checkboxes), Target Group (Dropdown), and Image Upload (Gallery).
- Fully styled with Tailwind utility classes and the theme's glassmorphism tokens.

### Phase 3: The Submission Engine (Backend Logic)
Handling the data when the user clicks "Submit".
#### [MODIFY] [functions.php](file:///c:/Users/ASUS/Desktop/wordpress%20tema/functions.php) (or a dynamic include like `inc/submission-engine.php`)
- **AJAX Handler / Form Processor:** A secure PHP function hooked into WordPress that receives the form data.
- **Sanitization:** Cleans all text and numerical inputs to prevent malicious code (XSS).
- **Post Creation:** Uses `wp_insert_post()` to create a new [property](file:///c:/Users/ASUS/Desktop/wordpress%20tema/inc/core-functionality.php#18-73) post.
- **Taxonomy Assignment:** Uses `wp_set_object_terms()` to attach the selected Amenities, Neighborhoods, etc.
- **Meta Data:** Saves the price and other custom fields (`update_post_meta`).
- **Media Upload:** Securely handles image uploads and sets the WordPress Featured Image.

### Phase 4: Connecting the UI
Connecting the existing buttons to the new system.
#### [MODIFY] [header.php](file:///c:/Users/ASUS/Desktop/wordpress%20tema/header.php)
- Update the `<a href="#" class="btn-add-listing">` links. If the user is logged out, clicking tests them to the Login page. If logged in, it redirects them to the Add Listing page.

## Verification Plan
### Manual Verification
1. I will create a test user.
2. We will navigate to the `/add-listing` page and fill out the form, uploading a test image.
3. We will verify that the submission returns a success message.
4. We will check the WordPress Admin Dashboard -> Properties to ensure the new listing sits in the "Pending" status and has all the correct data (Price, Amenities, Image) attached perfectly.
