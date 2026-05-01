# ThessNest Premium Email Templates

This document contains the premium English email templates for the ThessNest platform. All templates are designed to be highly professional, welcoming, and strictly formatted for use in the **Homey Theme Options > Email Management** section.

> **Note:** Do NOT modify the shortcodes (e.g., `{user_login_register}`). The system uses these to dynamically insert user and booking data.

---

## Global Email Footer

*Paste this HTML code into the footer section of your Email Management settings. It replaces the default Favethemes footer with ThessNest's official copyright and physical address.*

```html
<p style="margin: 0 0 10px;">Copyright &copy; 2026 ThessNest, All rights reserved.</p>
<p style="margin: 0 0 10px;">Please do not reply to this email. You are receiving this email because you are registered at <a href="https://thessnest.com" style="color: #1b2a4a; text-decoration: none;">ThessNest</a>.</p>

<p style="margin: 0 0 10px;">Our mailing address is:</p>
<p style="margin: 0 0 10px;">ThessNest<br>
Aristotelous 24A<br>
54623 Thessaloniki, Greece</p>
```

---

## 1. New User Registration

**Subject:**
```text
Welcome to {site_title} - Your Account Details
```

**Message:**
```html
Dear {user_login_register},<br><br>

Welcome to <strong>{site_title}</strong>! We are thrilled to have you join our community.<br><br>

Your account has been successfully created. You can now start exploring the best accommodations in Thessaloniki, save your favorite listings, and seamlessly manage your bookings.<br><br>

<strong>Your Account Details:</strong><br>
Username: {user_login_register}<br>
Password: {user_password}<br><br>

<em>Note: For your security, we highly recommend changing your password after your first login.</em><br><br>

<strong>Next Steps:</strong><br>
1. <strong>Verify your email:</strong> Please confirm your email address by clicking <a href="{profile_url}" style="color: #0056b3; font-weight: bold;">this verification link</a>.<br>
2. <strong>Complete your profile:</strong> You can access your dashboard and manage your account <a href="{profile_url}">right here</a>.<br><br>

If you have any questions or need assistance finding your perfect home, our support team is always here to help.<br><br>

Warm regards,<br>
<strong>The ThessNest Team</strong>
```

---

## 2. New Host Registration

**Subject:**
```text
Welcome to {site_title} - Your Host Account is Ready!
```

**Message:**
```html
Dear {user_login_register},<br><br>

Welcome to <strong>{site_title}</strong>! We are delighted to partner with you.<br><br>

Your Host account has been successfully created. You can now start listing your properties, connecting with reliable tenants, and managing your bookings with ease.<br><br>

<strong>Your Account Details:</strong><br>
Username: {user_login_register}<br>
Password: {user_password}<br><br>

<em>Note: For your security, we highly recommend changing your password after your first login.</em><br><br>

<strong>Next Steps:</strong><br>
1. <strong>Verify your email:</strong> Please confirm your email address by clicking <a href="{profile_url}" style="color: #0056b3; font-weight: bold;">this verification link</a>.<br>
2. <strong>Create your first listing:</strong> Access your host dashboard to complete your profile and add your first property <a href="{profile_url}">right here</a>.<br><br>

If you need any guidance on optimizing your listings or using our platform, our dedicated host support team is always available to assist you.<br><br>

Warm regards,<br>
<strong>The ThessNest Team</strong>
```

---

## 3. New Registered Admin Notification

**Subject:**
```text
Welcome to {site_title} - Your Admin Account is Ready!
```

**Message:**
```html
Dear {user_login_register},<br><br>

Welcome to the executive team at <strong>{site_title}</strong>!<br><br>

Your Administrator account has been successfully created. With this account, you have top-level access to manage the platform, oversee listings, and assist our users.<br><br>

<strong>Your Account Details:</strong><br>
Username: {user_login_register}<br>
Email: {user_email_register}<br>
Role: Administrator<br><br>

<em>Note: As an administrator, your account security is critical. Please ensure you use a strong password and keep your credentials strictly confidential.</em><br><br>

<strong>Next Steps:</strong><br>
You can log in and access the administrative dashboard securely via <a href="{profile_url}" style="color: #0056b3; font-weight: bold;">this link</a>.<br><br>

We are excited to have you on board to help grow and manage the ThessNest community.<br><br>

Warm regards,<br>
<strong>ThessNest Management</strong>
```

---

## 4. New Reservation Created By Admin (Notification to Host)

**Subject:**
```text
New Reservation Assigned to Your Property - {site_title}
```

**Message:**
```html
Dear Host,<br><br>

Great news! Our administrative team has registered a new reservation for your property on <strong>{site_title}</strong>.<br><br>

<strong>Reservation Notes / Guest Message:</strong><br>
<em>"{guest_message}"</em><br>
<a href="{message_link}" style="color: #1b2a4a; font-weight: bold;">Click here to reply or view conversation</a><br><br>

<strong>Next Steps:</strong><br>
Please review the booking details assigned to you and confirm the availability to finalize this reservation.<br><br>

<a href="{reservation_detail_url}" style="display: inline-block; padding: 10px 20px; background-color: #1b2a4a; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 10px; margin-bottom: 10px;">Review and Confirm Reservation</a><br><br>

Thank you for your continued partnership with us.<br><br>

Warm regards,<br>
<strong>The ThessNest Team</strong>
```

---

*(Please provide the original text of the next email templates you want to update, and they will be added here!)*
