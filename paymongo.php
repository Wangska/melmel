<?php
/**
 * PayMongo helper for GCash payments.
 * Requires paymongo_config.php with PAYMONGO_PUBLIC_KEY and PAYMONGO_SECRET_KEY set.
 */

require_once __DIR__ . '/paymongo_config.php';

class PayMongo {

    private static function getPublicKey() {
        return PAYMONGO_PUBLIC_KEY;
    }

    private static function getSecretKey() {
        return PAYMONGO_SECRET_KEY;
    }

    /**
     * Create a GCash Source. Customer will be redirected to GCash to authorize payment.
     *
     * @param int   $amount_peso   Amount in Philippine Peso (e.g. 450 for ₱450)
     * @param string $success_url  Full URL to redirect after successful payment
     * @param string $failed_url   Full URL to redirect after failed/cancelled payment
     * @param array $billing      Optional: name, email, phone
     * @param array $metadata     Optional key-value pairs (strings) to attach to the source
     * @return array ['success' => bool, 'redirect_url' => string|null, 'source_id' => string|null, 'error' => string|null]
     */
    public static function createGcashSource($amount_peso, $success_url, $failed_url, $billing = [], $metadata = []) {
        $key = self::getPublicKey();
        if (empty($key)) {
            return ['success' => false, 'redirect_url' => null, 'source_id' => null, 'error' => 'PayMongo public key not configured.'];
        }

        // PayMongo amounts are in centavos (100 = 1 PHP)
        $amount_centavos = (int) round($amount_peso * 100);
        if ($amount_centavos < 10000) {
            return ['success' => false, 'redirect_url' => null, 'source_id' => null, 'error' => 'Minimum amount is ₱100.00'];
        }

        $attributes = [
            'type'     => 'gcash',
            'amount'   => $amount_centavos,
            'currency' => 'PHP',
            'redirect' => [
                'success' => $success_url,
                'failed'  => $failed_url,
            ],
        ];

        if (!empty($billing)) {
            $attributes['billing'] = array_intersect_key($billing, array_flip(['name', 'email', 'phone', 'address']));
            if (isset($attributes['billing']['address']) && is_array($attributes['billing']['address'])) {
                $attributes['billing']['address'] = array_intersect_key(
                    $attributes['billing']['address'],
                    array_flip(['line1', 'line2', 'city', 'state', 'postal_code', 'country'])
                );
            }
        }

        if (!empty($metadata)) {
            $attributes['metadata'] = array_map('strval', $metadata);
        }

        $body = [
            'data' => [
                'attributes' => $attributes,
            ],
        ];

        $ch = curl_init(PAYMONGO_API_URL . '/v1/sources');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS    => json_encode($body),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Basic ' . base64_encode($key . ':'),
            ],
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return ['success' => false, 'redirect_url' => null, 'source_id' => null, 'error' => 'Request failed: ' . $curlError];
        }

        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['success' => false, 'redirect_url' => null, 'source_id' => null, 'error' => 'Invalid API response.'];
        }

        if ($httpCode >= 400) {
            $errMsg = isset($data['errors'][0]['detail']) ? $data['errors'][0]['detail'] : 'API error ' . $httpCode;
            return ['success' => false, 'redirect_url' => null, 'source_id' => null, 'error' => $errMsg];
        }

        $attrs = $data['data']['attributes'] ?? [];
        $redirect = $attrs['redirect'] ?? [];
        $redirectUrl = $redirect['checkout_url'] ?? $redirect['url'] ?? null;
        $sourceId = $data['data']['id'] ?? null;

        return [
            'success'      => true,
            'redirect_url' => $redirectUrl,
            'source_id'    => $sourceId,
            'error'        => null,
        ];
    }

    /**
     * Create a Payment from a chargeable Source (call from webhook when source.chargeable fires).
     *
     * @param string $source_id     PayMongo Source ID (e.g. src_xxx)
     * @param int    $amount_peso   Same amount as the source, in peso
     * @return array ['success' => bool, 'payment_id' => string|null, 'error' => string|null]
     */
    public static function createPaymentFromSource($source_id, $amount_peso) {
        $key = self::getSecretKey();
        if (empty($key)) {
            return ['success' => false, 'payment_id' => null, 'error' => 'PayMongo secret key not configured.'];
        }

        $amount_centavos = (int) round($amount_peso * 100);

        $body = [
            'data' => [
                'attributes' => [
                    'amount'    => $amount_centavos,
                    'currency'  => 'PHP',
                    'source'    => [
                        'id'   => $source_id,
                        'type' => 'source',
                    ],
                ],
            ],
        ];

        $ch = curl_init(PAYMONGO_API_URL . '/v1/payments');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS    => json_encode($body),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Basic ' . base64_encode($key . ':'),
            ],
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return ['success' => false, 'payment_id' => null, 'error' => 'Request failed: ' . $curlError];
        }

        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['success' => false, 'payment_id' => null, 'error' => 'Invalid API response.'];
        }

        if ($httpCode >= 400) {
            $errMsg = isset($data['errors'][0]['detail']) ? $data['errors'][0]['detail'] : 'API error ' . $httpCode;
            return ['success' => false, 'payment_id' => null, 'error' => $errMsg];
        }

        $paymentId = $data['data']['id'] ?? null;
        return ['success' => true, 'payment_id' => $paymentId, 'error' => null];
    }

    /**
     * Verify webhook signature (optional). Use when handling PayMongo webhooks.
     * Get webhook secret from PayMongo dashboard.
     *
     * @param string $payload   Raw request body
     * @param string $signature X-Paymongo-Signature header value
     * @param string $secret    Webhook secret (whsec_...)
     * @return bool
     */
    public static function verifyWebhookSignature($payload, $signature, $secret) {
        if (empty($secret) || empty($signature)) {
            return false;
        }
        $expected = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expected, $signature);
    }
}
