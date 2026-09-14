<?php
// /api/bookings endpoint — PHP + MySQL
// Apache/Nginx üstünde /api/bookings adresini bu dosyaya yönlendirin.

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); echo json_encode(['error' => 'Method not allowed']); exit;
}

// === DB ayarları ===
$DB_HOST = 'localhost';
$DB_NAME = 'omars_hair';
$DB_USER = 'omars_user';
$DB_PASS = 'CHANGE_ME';

// === E-posta bildirimi ===
$NOTIFY_EMAIL = 'omar@example.com';

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) { http_response_code(400); echo json_encode(['error'=>'Invalid JSON']); exit; }

$full_name = trim($data['full_name'] ?? '');
$phone     = trim($data['phone'] ?? '');
$service   = trim($data['service'] ?? '');
$pdate     = $data['preferred_date'] ?? null;
$ptime     = $data['preferred_time'] ?? null;
$notes     = $data['notes'] ?? null;

// Doğrulama
if (mb_strlen($full_name) < 2 || mb_strlen($full_name) > 100) { http_response_code(422); echo json_encode(['error'=>'Ad soyad geçersiz']); exit; }
if (mb_strlen($phone) < 7   || mb_strlen($phone) > 30)        { http_response_code(422); echo json_encode(['error'=>'Telefon geçersiz']); exit; }
if (mb_strlen($service) < 2 || mb_strlen($service) > 100)     { http_response_code(422); echo json_encode(['error'=>'Hizmet geçersiz']); exit; }
if ($notes && mb_strlen($notes) > 1000)                       { http_response_code(422); echo json_encode(['error'=>'Notlar çok uzun']); exit; }

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $id = bin2hex(random_bytes(16));
    $stmt = $pdo->prepare("INSERT INTO bookings (id, full_name, phone, service, preferred_date, preferred_time, notes) VALUES (?,?,?,?,?,?,?)");
    $stmt->execute([$id, $full_name, $phone, $service, $pdate ?: null, $ptime ?: null, $notes ?: null]);

    // E-posta bildirimi (opsiyonel)
    @mail(
        $NOTIFY_EMAIL,
        "Yeni Randevu Talebi — $full_name",
        "Ad: $full_name\nTelefon: $phone\nHizmet: $service\nTarih: $pdate $ptime\nNot: $notes",
        "From: noreply@omarshair.com\r\nContent-Type: text/plain; charset=utf-8"
    );

    echo json_encode(['ok' => true, 'id' => $id]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Sunucu hatası']);
}
