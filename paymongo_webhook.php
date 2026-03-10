<?php
/**
 * PayMongo webhook handler for source.chargeable (GCash authorized).
 * In PayMongo Dashboard: Webhooks → Add endpoint → URL: https://yoursite.com/melmel/paymongo_webhook.php
 * Subscribe to: source.chargeable
 *
 * Add your webhook signing secret in paymongo_config.php as PAYMONGO_WEBHOOK_SECRET (whsec_...).
 * Optional: add payment_source_id column to bookings table to link and update booking status.
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/paymongo.php';

// Webhook secret from PayMongo dashboard (add to paymongo_config.php)
$webhookSecret = defined('PAYMONGO_WEBHOOK_SECRET') ? PAYMONGO_WEBHOOK_SECRET : '';

$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_PAYMONGO_SIGNATURE'] ?? '';

if ($webhookSecret && !PayMongo::verifyWebhookSignature($payload, $signature, $webhookSecret)) {
    http_response_code(401);
    exit('Invalid signature');
}

$event = json_decode($payload, true);
$type = $event['data']['attributes']['type'] ?? '';
$data = $event['data'] ?? [];

if ($type !== 'source.chargeable') {
    http_response_code(200);
    exit('OK');
}

$sourceId = $data['id'] ?? null;
$attrs = $data['attributes'] ?? [];
$amountCentavos = (int) ($attrs['amount'] ?? 0);
$amountPeso = $amountCentavos / 100;

if (!$sourceId) {
    http_response_code(400);
    exit('Missing source id');
}

$result = PayMongo::createPaymentFromSource($sourceId, $amountPeso);

if (!$result['success']) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => $result['error']]);
    exit;
}

// Optional: update booking if you store payment_source_id when creating the source
// e.g. UPDATE bookings SET status = 'paid', payment_id = ? WHERE payment_source_id = ?
// You can get booking_id from source metadata if you set it when creating the source.

http_response_code(200);
header('Content-Type: application/json');
echo json_encode(['payment_id' => $result['payment_id']]);
