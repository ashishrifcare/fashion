<?php
define('DATA_FILE', 'submissions.json');

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$coupon = trim($input['coupon'] ?? '');

if (!$coupon || !file_exists(DATA_FILE)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

$data = json_decode(file_get_contents(DATA_FILE), true);
$updated = false;

foreach ($data as &$entry) {
    if ($entry['coupon'] === $coupon && !$entry['isScanned']) {
        $entry['isScanned'] = true;
        $updated = true;
        break;
    }
}

if ($updated) {
    file_put_contents(DATA_FILE, json_encode($data, JSON_PRETTY_PRINT));
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'already-scanned']);
}
?>
