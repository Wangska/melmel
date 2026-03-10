<?php
require_once 'config.php';

header('Content-Type: application/json; charset=utf-8');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'You must be logged in to rate.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit;
}

$hike_id = isset($_POST['hike_id']) ? (int) $_POST['hike_id'] : 0;
$rating  = isset($_POST['rating']) ? (int) $_POST['rating'] : 0;
$difficulty_rating = isset($_POST['difficulty_rating']) ? (int) $_POST['difficulty_rating'] : 0;
$duration_rating   = isset($_POST['duration_rating']) ? (int) $_POST['duration_rating'] : 0;

if ($hike_id < 1 || $rating < 1 || $rating > 5) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid hike or rating value.']);
    exit;
}

try {
    // Ensure hike exists
    $stmt = $pdo->prepare("SELECT id FROM hikes WHERE id = ?");
    $stmt->execute([$hike_id]);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Hike not found.']);
        exit;
    }

    $user_id = (int) $_SESSION['user_id'];

    // Normalize optional sub-ratings
    $difficulty_rating = ($difficulty_rating >= 1 && $difficulty_rating <= 5) ? $difficulty_rating : null;
    $duration_rating   = ($duration_rating >= 1 && $duration_rating <= 5) ? $duration_rating   : null;

    // Update existing rating or insert new
    $stmt = $pdo->prepare("
        UPDATE hike_ratings 
        SET rating = ?, difficulty_rating = ?, duration_rating = ?, updated_at = NOW() 
        WHERE hike_id = ? AND user_id = ?
    ");
    $stmt->execute([$rating, $difficulty_rating, $duration_rating, $hike_id, $user_id]);

    if ($stmt->rowCount() === 0) {
        $stmt = $pdo->prepare("
            INSERT INTO hike_ratings (hike_id, user_id, rating, difficulty_rating, duration_rating) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$hike_id, $user_id, $rating, $difficulty_rating, $duration_rating]);
    }

    // Return updated summary
    $stmt = $pdo->prepare("
        SELECT 
            AVG(rating) AS avg_rating, 
            AVG(difficulty_rating) AS avg_difficulty,
            AVG(duration_rating) AS avg_duration,
            COUNT(*) AS rating_count 
        FROM hike_ratings 
        WHERE hike_id = ?
    ");
    $stmt->execute([$hike_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $avg      = $row && $row['avg_rating'] !== null ? (float) $row['avg_rating'] : 0;
    $avgDiff  = $row && $row['avg_difficulty'] !== null ? (float) $row['avg_difficulty'] : 0;
    $avgDur   = $row && $row['avg_duration'] !== null ? (float) $row['avg_duration'] : 0;
    $count    = $row ? (int) $row['rating_count'] : 0;

    echo json_encode([
        'success'          => true,
        'avg_rating'       => $avg,
        'avg_difficulty'   => $avgDiff,
        'avg_duration'     => $avgDur,
        'rating_count'     => $count,
        'user_rating'      => $rating,
        'user_difficulty'  => $difficulty_rating,
        'user_duration'    => $duration_rating,
        'message'          => 'Thanks for rating this hike!'
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Unable to save rating right now.']);
}

