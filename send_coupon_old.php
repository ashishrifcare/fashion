<?php
require_once 'phpqrcode/qrlib.php';

define('DATA_FILE', 'submissions.json');

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$country = 'India';

if (!$name || !$email || !$phone) {
    die('Missing required fields. Please go back and complete the form.');
}

$couponCode = "CHANDIGARH" . rand(1000, 9999);

// Generate QR code image
$qrDir = "qrcodes";
if (!file_exists($qrDir)) mkdir($qrDir);
$qrFile = "$qrDir/{$couponCode}.png";
QRcode::png($couponCode, $qrFile, QR_ECLEVEL_H, 4);

// Email Content
$subject = "🎉 Your Exclusive Discount Coupon from India Export Fashion";
$message = "
<html>
  <body style='font-family: Arial, sans-serif;'>
    <h2>Hi " . htmlspecialchars($name) . ",</h2>
    <p>Thank you for registering for the <strong>India Export Fashion</strong> event.</p>
    <p><strong>Your discount coupon is:</strong></p>
    <img src='cid:qrimage' style='width:150px; height:auto; margin-top:10px;' />
    <hr>
    <p>📍 South Delhi, Luxury 5 Star Exhibition in Vasant Kunj</p>
    <p>📅 01–03 Aug 2025 | 🕙 10:00 AM to 10:00 PM</p>
    <p>📧 <a href='mailto:indiaexportfashion@gmail.com'>indiaexportfashion@gmail.com</a></p>
    <p style='margin-top: 20px;'>Best Regards,<br><strong>India Export Fashion Team</strong></p>
  </body>
</html>";

// Headers
$boundary = md5(time());
$headers = "MIME-Version: 1.0\r\n";
$headers .= "From: India Export Fashion <support@indiaexportfashion.com>\r\n";
$headers .= "Content-Type: multipart/related; boundary=\"$boundary\"\r\n";

$qrImage = chunk_split(base64_encode(file_get_contents($qrFile)));

$body = "--$boundary\r\n";
$body .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";
$body .= $message . "\r\n";

$body .= "--$boundary\r\n";
$body .= "Content-Type: image/png; name=\"{$couponCode}.png\"\r\n";
$body .= "Content-Transfer-Encoding: base64\r\n";
$body .= "Content-ID: <qrimage>\r\n";
$body .= "Content-Disposition: inline; filename=\"{$couponCode}.png\"\r\n\r\n";
$body .= $qrImage . "\r\n";
$body .= "--$boundary--";

// Send Email
mail($email, $subject, $body, $headers);

// Save submission
$entry = [
    'name' => $name,
    'phone' => $phone,
    'email' => $email,
    'country' => $country,
    'coupon' => $couponCode,
    'submitted_at' => date('Y-m-d H:i:s'),
    'isScanned' => false
];

$data = [];
if (file_exists(DATA_FILE)) {
    $existing = file_get_contents(DATA_FILE);
    $data = json_decode($existing, true) ?: [];
}
$data[] = $entry;
file_put_contents(DATA_FILE, json_encode($data, JSON_PRETTY_PRINT));

header("Location: thankyou.php");
exit;
?>
