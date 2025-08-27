<?php
define('DATA_FILE', 'submissions.json');
define('ROWS_PER_PAGE', 20);

function loadData($search = '') {
    $data = [];
    if (file_exists(DATA_FILE)) {
        $json = file_get_contents(DATA_FILE);
        $data = json_decode($json, true) ?: [];
    }
    if ($search !== '') {
        $search = strtolower(trim($search));
        $data = array_filter($data, function ($entry) use ($search) {
            return strpos(strtolower($entry['coupon']), $search) !== false ||
                   strpos(strtolower($entry['email']), $search) !== false ||
                   strpos(strtolower($entry['phone']), $search) !== false ||
                   strpos(strtolower($entry['name']), $search) !== false;
        });
    }

    // Show latest entries first
    $data = array_reverse($data);

    return array_values($data);
}

// Handle AJAX table reload
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

    $data = loadData($search);
    $total = count($data);
    $totalPages = max(1, ceil($total / ROWS_PER_PAGE));

    $start = ($page - 1) * ROWS_PER_PAGE;
    $pageData = array_slice($data, $start, ROWS_PER_PAGE);

    ob_start();
    if (empty($pageData)) {
        echo '<p class="empty">No submissions found.</p>';
    } else {
        echo '<table>
            <thead>
              <tr>
                <th>#</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Country</th>
                <th>Coupon Code</th>
                <th>Submitted At</th>
                <th>Scanned</th>
              </tr>
            </thead>
            <tbody>';
        foreach ($pageData as $index => $entry) {
            echo '<tr>
                <td>' . ($start + $index + 1) . '</td>
                <td>' . htmlspecialchars($entry['name']) . '</td>
                <td>' . htmlspecialchars($entry['phone']) . '</td>
                <td>' . htmlspecialchars($entry['email']) . '</td>
                <td>' . htmlspecialchars($entry['country']) . '</td>
                <td>' . htmlspecialchars($entry['coupon']) . '</td>
                <td>' . htmlspecialchars($entry['submitted_at']) . '</td>
                <td>' . (!empty($entry['isScanned']) ? '✅' : '❌') . '</td>
              </tr>';
        }
        echo '</tbody></table>';
    }

    // Pagination
    if ($totalPages > 1) {
        echo '<div class="pagination">';
        if ($page > 1) {
            echo '<button onclick="loadPage(' . ($page - 1) . ')">Prev</button>';
        }
        for ($i = 1; $i <= $totalPages; $i++) {
            $active = ($i === $page) ? 'active' : '';
            echo '<button class="' . $active . '" onclick="loadPage(' . $i . ')">' . $i . '</button>';
        }
        if ($page < $totalPages) {
            echo '<button onclick="loadPage(' . ($page + 1) . ')">Next</button>';
        }
        echo '</div>';
    }

    echo ob_get_clean();
    exit;
}

// Handle QR scan
if (isset($_GET['scan']) && !empty($_GET['code'])) {
    $code = trim($_GET['code']);
    $data = [];
    if (file_exists(DATA_FILE)) {
        $data = json_decode(file_get_contents(DATA_FILE), true) ?: [];
    }

    foreach ($data as &$entry) {
        if (strcasecmp($entry['coupon'], $code) == 0) {
            $alreadyScanned = !empty($entry['isScanned']);
            if (!$alreadyScanned) {
                $entry['isScanned'] = true;
                file_put_contents(DATA_FILE, json_encode($data, JSON_PRETTY_PRINT));
            }
            echo json_encode([
                'status' => 'found',
                'isScanned' => $alreadyScanned,
                'name' => $entry['name'],
                'phone' => $entry['phone'],
                'email' => $entry['email'],
                'country' => $entry['country'],
                'coupon' => $entry['coupon']
            ]);
            exit;
        }
    }
    echo json_encode(['status' => 'not_found']);
    exit;
}

$data = loadData();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>India Export Fashion Submissions</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<script src="https://unpkg.com/html5-qrcode"></script>
<style>
  body { font-family: 'Segoe UI', sans-serif; padding: 20px; background: linear-gradient(135deg, #f9f9f9, #eef1f7); color: #333; margin: 0; }
  h2 { text-align: center; margin-bottom: 20px; font-size: 28px; color: #111; }
  .search-bar { text-align: center; margin-bottom: 20px; display: flex; justify-content: center; gap: 10px; }
  .search-bar input { padding: 14px 18px; max-width: 500px; border-radius: 10px; border: 1px solid #ccc; font-size: 18px; width: 100%; }
  .search-bar button { padding: 14px 18px; background: #e74c3c; border: none; color: white; border-radius: 8px; cursor: pointer; font-size: 16px; }
  table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
  th, td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
  th { background: #4e54c8; color: white; }
  tr:nth-child(even) { background-color: #f8f8f8; }
  .empty { font-size: 18px; color: #777; text-align: center; margin-top: 30px; }
  .pagination { text-align: center; margin-top: 15px; }
  .pagination button { padding: 8px 12px; margin: 0 3px; border: none; border-radius: 5px; background: #4e54c8; color: white; cursor: pointer; }
  .pagination button.active { background: #222; }
  #scanModal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); align-items:center; justify-content:center; z-index:9999; }
  #scanModal .modal-content { background:white; padding:20px; border-radius:12px; max-width:500px; width:100%; position:relative; }
  #scanModal .close-btn { position:absolute; top:10px; right:15px; cursor:pointer; font-size:20px; }
</style>
</head>
<body>
  <h2>India Export Fashion Submissions</h2>

  <div style="text-align:center; margin-bottom:20px;">
    <button onclick="openScanModal()" style="padding:14px 20px; background:#27ae60; color:white; border:none; border-radius:8px; font-size:16px; cursor:pointer;">
        📷 Scan Now
    </button>
  </div>

  <div class="search-bar">
      <input type="text" id="searchInput" placeholder="Search by coupon, email, phone, name">
      <button onclick="clearSearch()">Clear</button>
  </div>

  <div id="tableContainer"></div>

  <div id="scanModal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeScanModal()">❌</span>
        <h3>Scan Coupon QR</h3>
        <div id="reader" style="width:100%;"></div>
        <div id="scanResult" style="margin-top:15px; font-size:16px;"></div>
        <button style="padding:10px 15px;background:#27ae60;color:white;border:none;border-radius:5px;cursor:pointer;margin-top:10px;" onclick="closeScanModal()">OK</button>
    </div>
  </div>

<script>
let currentPage = 1;
let html5QrCode;

function loadPage(page = 1) {
    const search = document.getElementById("searchInput").value;
    fetch("?ajax=1&search=" + encodeURIComponent(search) + "&page=" + page)
        .then(response => response.text())
        .then(html => {
            document.getElementById("tableContainer").innerHTML = html;
            currentPage = page;
        });
}

function clearSearch() {
    document.getElementById("searchInput").value = "";
    loadPage(1);
}

document.getElementById("searchInput").addEventListener("input", function() {
    loadPage(1);
});

function openScanModal() {
    document.getElementById("scanModal").style.display = "flex";
    document.getElementById("scanResult").innerHTML = "";
    document.getElementById("reader").style.display = "block";

    html5QrCode = new Html5Qrcode("reader");
    html5QrCode.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: 250 },
        (decodedText) => {
            handleScannedCode(decodedText);
        }
    );
}

function closeScanModal() {
    if (html5QrCode) {
        html5QrCode.stop().then(() => {
            html5QrCode = null;
            document.getElementById("scanModal").style.display = "none";
            location.reload();
        }).catch(err => {
            console.error("Error stopping QR code scanner:", err);
            html5QrCode = null;
            document.getElementById("scanModal").style.display = "none";
            location.reload();
        });
    } else {
        document.getElementById("scanModal").style.display = "none";
        location.reload();
    }
}

function handleScannedCode(couponCode) {
    if (html5QrCode) {
        html5QrCode.stop().then(() => {
            html5QrCode = null;
            document.getElementById("reader").style.display = "none";
        });
    }

    fetch("?scan=1&code=" + encodeURIComponent(couponCode))
        .then(res => res.json())
        .then(data => {
            let resultDiv = document.getElementById("scanResult");
            if (data.status === "found") {
                let statusText = data.isScanned ? "✅ Already Scanned" : "✅ Scanned Successfully";
                resultDiv.innerHTML = `
                    <p><b>Name:</b> ${data.name}</p>
                    <p><b>Phone:</b> ${data.phone}</p>
                    <p><b>Email:</b> ${data.email}</p>
                    <p><b>Country:</b> ${data.country}</p>
                    <p><b>Coupon:</b> ${data.coupon}</p>
                    <p style="color:green; font-weight:bold;">${statusText}</p>
                `;
                loadPage(currentPage);
            } else {
                resultDiv.innerHTML = `<p style="color:red;">❌ Coupon not found</p>`;
            }
        });
}

loadPage(1);
</script>
</body>
</html>
