<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>India Export Fashion</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Inter', sans-serif;
    }

    body {
      background: linear-gradient(135deg, #f7f7f7, #e2e8f0);
      color: #111;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 30px 15px;
    }

    .container {
      background: #fff;
      padding: 30px;
      border-radius: 16px;
      max-width: 500px;
      width: 100%;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .offer {
      text-align: center;
      margin-bottom: 20px;
    }

    .offer h1 {
      color: #e11d48;
      font-size: 28px;
      font-weight: 700;
      letter-spacing: 1px;
    }

    .offer h2 {
      color: #dc2626;
      font-size: 48px;
      font-weight: 800;
      margin: 10px 0;
    }

    .details, .address {
      text-align: center;
      font-size: 15px;
      font-weight: 500;
      margin-top: 10px;
    }

    .details strong {
      display: block;
      font-weight: 700;
      margin-top: 5px;
    }

    .address {
      margin-top: 20px;
      font-size: 14px;
    }

    .address p {
      margin: 5px 0;
    }

    .address a {
      display: inline-block;
      margin-top: 8px;
      text-decoration: none;
      color: #2563eb;
      font-weight: 600;
      background: #e0e7ff;
      padding: 8px 14px;
      border-radius: 6px;
      transition: background 0.3s ease;
    }

    .address a:hover {
      background: #c7d2fe;
    }

    form {
      margin-top: 25px;
    }

    label {
      display: block;
      font-weight: 600;
      margin-top: 15px;
      margin-bottom: 6px;
    }

    input, select {
      width: 100%;
      padding: 12px;
      border: 1px solid #d1d5db;
      border-radius: 8px;
      margin-bottom: 10px;
      font-size: 15px;
    }

    .submit-btn {
      background-color: #111827;
      color: #fff;
      border: none;
      padding: 14px;
      width: 100%;
      font-weight: 700;
      border-radius: 8px;
      cursor: pointer;
      transition: background 0.3s;
      margin-top: 20px;
    }

    .submit-btn:hover {
      background-color: #1f2937;
    }

    .submit-btn[disabled] {
      background-color: #9ca3af;
      cursor: not-allowed;
    }

    @media (max-width: 480px) {
      .offer h1 {
        font-size: 36px;
      }

      .offer h2 {
        font-size: 36px;
      }

      .container {
        padding: 20px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="offer">
      <h2>EXTRA FLAT</h2>
      <h2>₹300 OFF<SUP>*</SUP></h2>
    </div>
    <div class="details">
      <p>Only For Our Customers At</p>
      <strong></strong>
      <p>15th Aug to 17th Aug 2025 | 10:00 AM – 10:00 PM</p>
    </div>

    <div class="address">
      <p>📍 <strong>Address</strong></p>
      <p>International Trade Expo Centre</p>
      <p>Near Metro Station</p>
      <p>Sector 62,Noida, Uttar Pradesh 201301</p>
      <a href="https://share.google/hG3ZYeSXTI5yyZ9ld" target="_blank">📌 Open in Google Maps</a>
    </div>

    <form method="POST" action="send_coupon.php" onsubmit="return handleSubmit();">
      <label for="name">Full Name <span style="color:red;">*</span></label>
      <input type="text" id="name" name="name" placeholder="Enter your name" required>

      <label for="email">Email Address <span style="color:red;">*</span></label>
      <input type="email" id="email" name="email" placeholder="Enter your email" required>

      <label for="phone">WhatsApp Number <span style="color:red;">*</span></label>
      <select disabled>
        <option selected>+91 India</option>
      </select>
      <input
        type="tel"
        id="phone"
        name="phone"
        placeholder="Enter your 10-digit WhatsApp number"
        pattern="^\d{10}$"
        title="Please enter a valid 10-digit number"
        required
      >

      <button type="submit" class="submit-btn" id="submitBtn">SUBMIT</button>
    </form>
  </div>

  <script>
    function handleSubmit() {
      const phoneInput = document.getElementById("phone").value.trim();
      const isValidPhone = /^\d{10}$/.test(phoneInput);

      if (!isValidPhone) {
        alert("Please enter a valid 10-digit phone number (digits only).");
        return false;
      }

      const btn = document.getElementById("submitBtn");
      btn.disabled = true;
      btn.innerText = "Submitting...";
      return true;
    }
  </script>
</body>
</html>
