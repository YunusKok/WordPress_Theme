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

## 5. New Reservation Sent By Admin (Notification to Guest)

**Subject:**
```text
Reservation Confirmation: Your Booking Request at {site_title}
```

**Message:**
```html
Dear Guest,<br><br>

Great news! Our administrative team has successfully placed a reservation request on your behalf for a property on <strong>{site_title}</strong>.<br><br>

<strong>Your Message to the Host:</strong><br>
<em>"{guest_message}"</em><br>
<a href="{message_link}" style="color: #1b2a4a; font-weight: bold;">Click here to send another message or view the conversation</a><br><br>

<strong>What Happens Next?</strong><br>
The property host will review your request and confirm availability shortly. Once confirmed, you will receive an update regarding the next steps for payment and check-in.<br><br>

<a href="{reservation_detail_url}" style="display: inline-block; padding: 10px 20px; background-color: #1b2a4a; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 10px; margin-bottom: 10px;">View Your Reservation Details</a><br><br>

Thank you for choosing ThessNest to find your perfect home.<br><br>

Warm regards,<br>
<strong>The ThessNest Team</strong>
```

---

## 6. New Reservation Received (Standard Notification to Host)

**Subject:**
```text
Action Required: New Reservation Request on {site_title}
```

**Message:**
```html
Dear Host,<br><br>

Great news! A guest has just submitted a new reservation request for your property on <strong>{site_title}</strong>.<br><br>

<strong>Message from the Guest:</strong><br>
<em>"{guest_message}"</em><br>
<a href="{message_link}" style="color: #1b2a4a; font-weight: bold;">Click here to reply to the guest</a><br><br>

<strong>Next Steps:</strong><br>
Please review the booking details and confirm your availability as soon as possible to secure this reservation. Fast response times help boost your property ranking and provide an excellent experience for our community.<br><br>

<a href="{reservation_detail_url}" style="display: inline-block; padding: 10px 20px; background-color: #1b2a4a; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 10px; margin-bottom: 10px;">Review and Confirm Reservation</a><br><br>

Thank you for hosting with ThessNest.<br><br>

Warm regards,<br>
<strong>The ThessNest Team</strong>
```

---

## 7. New Reservation Sent (Standard Notification to Guest)

**Subject:**
```text
Reservation Received: Your Booking Request at {site_title}
```

**Message:**
```html
Dear Guest,<br><br>

Thank you for choosing <strong>ThessNest</strong>! We have successfully received your reservation request.<br><br>

<strong>Your Message to the Host:</strong><br>
<em>"{guest_message}"</em><br>
<a href="{message_link}" style="color: #1b2a4a; font-weight: bold;">Click here to send another message or view the conversation</a><br><br>

<strong>What Happens Next?</strong><br>
Your request has been forwarded to the host for review. Once the host confirms their availability, you will receive an update regarding the next steps to secure your booking.<br><br>

<a href="{reservation_detail_url}" style="display: inline-block; padding: 10px 20px; background-color: #1b2a4a; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 10px; margin-bottom: 10px;">View Your Reservation Details</a><br><br>

We look forward to helping you find your perfect home.<br><br>

Warm regards,<br>
<strong>The ThessNest Team</strong>
```

---

## 8. Confirm Reservation (Notification to Guest for Payment)

**Subject:**
```text
Good News! Your Reservation on {site_title} is Confirmed
```

**Message:**
```html
Dear Guest,<br><br>

Great news! The host has successfully confirmed availability for your reservation on <strong>{site_title}</strong>.<br><br>

<strong>Next Steps: Secure Your Booking</strong><br>
To finalize your reservation and secure your selected dates, please complete the required payment. <em>Please note that your booking is not fully secured until the payment process is successfully completed.</em><br><br>

<a href="{reservation_detail_url}" style="display: inline-block; padding: 12px 25px; background-color: #1b2a4a; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 10px; margin-bottom: 10px; font-size: 16px;">Complete Payment Now</a><br><br>

If you have any questions or need further assistance, feel free to contact the host through our platform.<br><br>

We are thrilled to host you!<br><br>

Warm regards,<br>
<strong>The ThessNest Team</strong>
```

---

## 9. Reservation Booked (Notification to Guest upon Payment)

**Subject:**
```text
Booking Confirmed! Your Stay at {site_title} is Locked In
```

**Message:**
```html
Dear Guest,<br><br>

Congratulations! We have successfully received your payment, and your reservation on <strong>{site_title}</strong> is now fully confirmed.<br><br>

<strong>Get Ready for Your Trip!</strong><br>
Your selected dates are locked in, and the host is looking forward to welcoming you. You can review all the important details regarding your check-in, property rules, and the host's contact information via the link below.<br><br>

<a href="{reservation_detail_url}" style="display: inline-block; padding: 12px 25px; background-color: #1b2a4a; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 10px; margin-bottom: 10px; font-size: 16px;">View Booking Details & Itinerary</a><br><br>

If you have any specific requests or questions before your arrival, please feel free to message your host directly through the platform.<br><br>

Thank you for booking with ThessNest. We wish you a wonderful stay!<br><br>

Warm regards,<br>
<strong>The ThessNest Team</strong>
```

---

## 10. Reservation Booked (Notification to Admin upon Payment)

**Subject:**
```text
[Admin Alert] Booking Successfully Completed on {site_title}
```

**Message:**
```html
Hello Admin,<br><br>

Great news! A reservation has just been successfully booked and paid for on <strong>{site_title}</strong>.<br><br>

<strong>Transaction Complete:</strong><br>
The payment has been processed, and the selected dates are now officially locked in for the guest. You can review the full transaction and booking details in your administrative dashboard.<br><br>

<a href="{reservation_detail_url}" style="display: inline-block; padding: 10px 20px; background-color: #1b2a4a; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 10px; margin-bottom: 10px;">Review Booking in Dashboard</a><br><br>

Best regards,<br>
<strong>{site_title} System Notifier</strong>
```

---

## 11. Reservation Declined (Notification to Guest)

**Subject:**
```text
Update on Your Reservation Request at {site_title}
```

**Message:**
```html
Dear Guest,<br><br>

We are writing to inform you that the host was unfortunately unable to accept your reservation request for their property on <strong>{site_title}</strong> at this time.<br><br>

<strong>Don't Worry, We Have Other Options!</strong><br>
While this specific request couldn't be accommodated, Thessaloniki has many other beautiful homes waiting for you. We encourage you to explore other available properties that match your dates.<br><br>

<a href="{reservation_detail_url}" style="display: inline-block; padding: 10px 20px; background-color: #f8f9fa; color: #1b2a4a; text-decoration: none; border: 1px solid #1b2a4a; border-radius: 5px; font-weight: bold; margin-top: 10px; margin-bottom: 10px;">View Request Details</a><br><br>

If you need any assistance finding a new place to stay, our support team is always here to help you.<br><br>

Warm regards,<br>
<strong>The ThessNest Team</strong>
```

---

## 12. Reservation Cancelled

**Subject:**
```text
Cancellation Notice: Reservation at {site_title}
```

**Message:**
```html
Hello,<br><br>

This is an automated notification to inform you that a reservation on <strong>{site_title}</strong> has been officially cancelled.<br><br>

<strong>What Does This Mean?</strong><br>
The dates for this booking have been released, and the reservation is no longer active. If any payments were processed, applicable refunds will be handled according to our standard cancellation policy.<br><br>

<a href="{reservation_detail_url}" style="display: inline-block; padding: 10px 20px; background-color: #f8f9fa; color: #1b2a4a; text-decoration: none; border: 1px solid #1b2a4a; border-radius: 5px; font-weight: bold; margin-top: 10px; margin-bottom: 10px;">View Cancellation Details</a><br><br>

If you believe this cancellation was made in error or if you need assistance with rebooking, please contact our support team immediately.<br><br>

Warm regards,<br>
<strong>The ThessNest Team</strong>
```

---

## 13. Local Payment Received (Notification to Host / Admin)

**Subject:**
```text
Action Required: Verify Local Payment Received - {site_title}
```

**Message:**
```html
Hello,<br><br>

A guest has submitted a reservation request on <strong>{site_title}</strong> and selected "Local Payment / Bank Transfer" as their payment method.<br><br>

<strong>Action Required:</strong><br>
Please verify your bank account to confirm receipt of the funds. Once the payment has cleared, you must manually mark this reservation as "Booked" in your dashboard to finalize the guest's booking.<br><br>

<a href="{reservation_detail_url}" style="display: inline-block; padding: 10px 20px; background-color: #1b2a4a; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 10px; margin-bottom: 10px;">Verify & Update Reservation Status</a><br><br>

Best regards,<br>
<strong>{site_title} System Notifier</strong>
```

---

## 14. Local Payment Sent (Notification to Guest)

**Subject:**
```text
Payment Processing: Your Reservation at {site_title}
```

**Message:**
```html
Dear Guest,<br><br>

Thank you for choosing to proceed with your booking on <strong>{site_title}</strong>. We have received your request to pay via "Local Payment / Bank Transfer".<br><br>

<strong>What's Next?</strong><br>
Our administrative team is currently waiting to receive and verify your payment. Once the funds have successfully cleared into our account, your reservation will be officially marked as "Booked", and your dates will be locked in.<br><br>

<em>Please ensure you have completed the bank transfer according to the provided instructions to avoid any delays or cancellation of your request.</em><br><br>

<a href="{reservation_detail_url}" style="display: inline-block; padding: 10px 20px; background-color: #1b2a4a; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 10px; margin-bottom: 10px;">Review Payment Details</a><br><br>

If you have already sent the payment, no further action is required. We will notify you as soon as it is verified.<br><br>

Warm regards,<br>
<strong>The ThessNest Team</strong>
```

---

*(Please provide the original text of the next email templates you want to update, and they will be added here!)*
