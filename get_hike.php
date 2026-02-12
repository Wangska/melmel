<?php
/**
 * Returns a single hike as JSON for the detail modal.
 * GET id = hike id
 */
require_once 'config.php';

header('Content-Type: application/json; charset=utf-8');

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id < 1) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid id']);
    exit;
}

$stmt = $pdo->prepare("SELECT id, name, location, difficulty, duration_hours_min, duration_hours_max, price, description, image_url FROM hikes WHERE id = ?");
$stmt->execute([$id]);
$hike = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$hike) {
    http_response_code(404);
    echo json_encode(['error' => 'Hike not found']);
    exit;
}

// Escape for safe use in HTML
$hike['name_esc'] = htmlspecialchars($hike['name'], ENT_QUOTES, 'UTF-8');
$hike['location_esc'] = htmlspecialchars($hike['location'], ENT_QUOTES, 'UTF-8');
$hike['difficulty_esc'] = htmlspecialchars($hike['difficulty'], ENT_QUOTES, 'UTF-8');
$hike['description_esc'] = nl2br(htmlspecialchars($hike['description'], ENT_QUOTES, 'UTF-8'));
$hike['image_url_esc'] = htmlspecialchars($hike['image_url'], ENT_QUOTES, 'UTF-8');
$hike['difficulty_class'] = 'difficulty-' . strtolower(str_replace(' ', '-', $hike['difficulty']));

echo json_encode($hike);
