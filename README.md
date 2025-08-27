# Fashion Coupon System

This project implements a **coupon system** for the Fashion store.

## 📌 Workflow

1. **User Form Submission**
   - User fills out a form on the store website:  
     👉 [https://spelass.com/fashion](https://spelass.com/fashion)

2. **Coupon Generation & Delivery**
   - After submitting the form, a **unique coupon code** is generated.
   - The coupon is sent to the user via:
     - ✉️ **Email** (SMTP integration)  
     - 📲 **WhatsApp API** (for instant delivery)

3. **Coupon Redemption at Store**
   - When the user visits the physical store:
     - Admin scans the **coupon QR/Code**.
     - The system verifies the coupon validity.
     - Admin can view user details linked to the coupon.

4. **Admin Panel**
   - All user submissions can be tracked here:  
     👉 [https://spelass.com/fashion/view_submissions.php](https://spelass.com/fashion/view_submissions.php)

---

## ⚙️ Features
- 🎫 Unique coupon code generation  
- 📧 Email + 📲 WhatsApp API integration  
- 🔍 Coupon verification by admin  
- 📊 Submission records in admin panel  
- 🔒 Secure form & coupon validation  

---

## 🚀 Tech Stack
- **Frontend:** HTML, CSS, JavaScript  
- **Backend:** PHP  
- **Database:** MySQL  
- **APIs:** Email (SMTP), WhatsApp API  

---

## 🔐 Security Notes
- Coupon codes are **unique & time-limited**.  
- Admin verification ensures **no misuse**.  
- All submissions stored securely in database.  

---

## 📂 Project Links
- 🌐 User Form: [https://spelass.com/fashion](https://spelass.com/fashion)  
- 🛠️ Admin Panel: [https://spelass.com/fashion/view_submissions.php](https://spelass.com/fashion/view_submissions.php)  
