Create WELCOME10 Coupon

Location: WooCommerce → Coupons → Add New

Coupon Settings:

•
Code: WELCOME10

•
Description: 10% welcome discount for first-time newsletter subscribers

•
Discount Type: Percentage

•
Coupon Amount: 10

•
Usage Restrictions:

•
Limit usage to X items: 1 (one-time use per customer)

•
Usage limit per user: 1



•
Expiry Date: Leave blank (no expiration) or set to 1 year from now

•
Minimum Spend: 0 (no minimum)

•
Maximum Spend: Leave blank (unlimited)


Create Newsletter Form (WPForms)

Location: WPForms → All Forms → Add New Form

Form Name: Newsletter Signup

Form Fields:

1.
Email (required)

2.
First Name (optional)

3.
Checkbox: "I agree to receive promotional emails" (required)

Form Settings:

•
Success Message: "Thank you! Check your email for your 10% welcome discount code: WELCOME10"

•
Confirmation Email: Enable

•
Email Recipient: info@lacasa.market

Add Newsletter Form to Pages

Locations to Add:

1.
Homepage footer

2.
Contact page footer

3.
Sidebar (optional)

Configure Email Automation (SureMail SMTP)

Location: SureMail SMTP → Dashboard

Email Template:

Plain Text


Subject: Welcome to Lacasa Market - Your 10% Discount Code

Dear [First Name],

Thank you for subscribing to Lacasa Market's newsletter!

As a welcome gift, we're giving you an exclusive 10% discount on your first order.

Your Discount Code: WELCOME10

Use this code at checkout to save 10% on your first purchase. This code is valid for one-time use only.

Shop Now: https://test.lacasa.market/shop/

If you have any questions, feel free to contact us at info@lacasa.market

Best regards,
Lacasa Market Team


Configuration Steps:

1.
Go to SureMail SMTP → Dashboard

2.
Create new email template

3.
Name: "Newsletter Welcome Email"

4.
Add template content (above )

5.
Configure recipient: [email from form]

6.
Set trigger: Form submission

7.
Save and test

TESTING CHECKLIST

Homepage Tests




CTA button text is "Shop Fresh Start Sales"




CTA button links to /shop/




Trust badges display correctly




Scroll animations trigger smoothly




Page responsive on mobile (320px)




Page responsive on tablet (768px)




Page responsive on desktop (1024px+)




All links work correctly




Page loads in < 3 seconds




No console errors

Contact Page Tests




Hero image displays correctly




Contact information is accurate




Contact form appears on page




Form validation works




Form submission succeeds




Success message displays




Confirmation email is sent




FAQ accordion expands/collapses




CTA buttons work




Scroll animations trigger




Page responsive on all devices




Page loads in < 3 seconds




No console errors

Newsletter Tests




Newsletter form appears on homepage




Newsletter form appears on contact page




Form validation works




Form submission succeeds




Success message displays




Welcome email is sent




Email includes WELCOME10 code




Email is deliverable (not spam)

Promo Code Tests




WELCOME10 code exists in WooCommerce




Code applies 10% discount




Code works only once per customer




Code works on first order only




Discount displays correctly in cart




Discount displays correctly in checkout




No errors during checkout with code



