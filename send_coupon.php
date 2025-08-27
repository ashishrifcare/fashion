<?php
require_once 'phpqrcode/qrlib.php';
require_once __DIR__ . '/vendor/autoload.php'; // Composer autoload

use Twilio\Rest\Client;

define('DATA_FILE', 'submissions.json');

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$country = 'India';

// Validate required fields
if (!$name || !$email || !$phone) {
    die('Missing required fields. Please go back and complete the form.');
}

// Generate unique coupon code
$couponCode = "INRADE" . rand(1000, 9999);

// Generate QR code image
$qrDir = "qrcodes";
if (!file_exists($qrDir)) mkdir($qrDir);
$qrFile = "$qrDir/{$couponCode}.png";
QRcode::png($couponCode, $qrFile, QR_ECLEVEL_H, 4);

// ---------------- EMAIL SECTION ---------------- //
$subject = "🎉 Your Exclusive Discount Coupon from India Export Fashion";
$message = "
<html>
  <body style='font-family: Arial, sans-serif;'>
    <h2>Hi " . htmlspecialchars($name) . ",</h2>
    <p>Thank you for registering for the <strong>India Export Fashion</strong> event.</p>
    <p><strong>Your discount coupon is:</strong></p>
    <img src='cid:qrimage' style='width:150px; height:auto; margin-top:10px;' />
    <hr>
    <p>📍 International Trade Expo Centre, Sector 62,Noida, Uttar Pradesh 201301</p>
    <p>📅 15–17 Aug 2025 | 🕙 10:00 AM to 10:00 PM</p>
    <p>📧 <a href='mailto:indiaexportfashion@gmail.com'>indiaexportfashion@gmail.com</a></p>
    <p style='margin-top: 20px;'>Best Regards,<br><strong>India Export Fashion Team</strong></p>
  </body>
</html>";

// Email headers and body (with embedded QR code)
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

// Send email
mail($email, $subject, $body, $headers);

// ---------------- WHATSAPP SECTION (Twilio API) ---------------- //

// ✅ Use your actual Twilio credentials here:
$sid    = "SKba15cecc2b6ffd8734b919219aff73a9";      
$token  = "zfnv3KVgPIOJ0U8qktPnADKn4Kx9sOZq";         
$from   = "whatsapp:+14155238886";          

$client = new Client($sid, $token);

// Format phone to international format (assumes Indian number input)
$to = 'whatsapp:+91' . preg_replace('/\D/', '', $phone); // E.g. +919999999999

$whatsappBody = "👋 Hi $name,\n\n🎉 Thank you for registering for *India Export Fashion*.\n\n🎟️ Your discount coupon is: *$couponCode*\n📍 South Delhi, Luxury 5 Star Exhibition - Vasant Kunj\n📅 01–03 Aug 2025 | 🕙 10:00 AM to 10:00 PM\n\n🧧 Show this code at entry.\n\n❤️ India Export Fashion Team";

try {
    $whatsappMessage = $client->messages->create(
        $to,
        [
            'from' => $from,
            'body' => $whatsappBody
        ]
    );
    // Optional: Log message SID or status
    // file_put_contents('twilio_log.txt', $whatsappMessage->sid);
} catch (Exception $e) {
    error_log("WhatsApp Message Failed: " . $e->getMessage());
    // Optional: Show a fallback or warning
}

// ---------------- SAVE TO FILE ---------------- //
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

// Redirect to thank you page
header("Location: thankyou.php");
exit;
?>
