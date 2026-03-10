# PayMongo GCash Integration

## 1. Add your API keys

Edit **`paymongo_config.php`** and set:

- **`PAYMONGO_PUBLIC_KEY`** – from [PayMongo Dashboard](https://dashboard.paymongo.com) (e.g. `pk_test_...` or `pk_live_...`)
- **`PAYMONGO_SECRET_KEY`** – secret key (e.g. `sk_test_...` or `sk_live_...`)
- **`PAYMONGO_WEBHOOK_SECRET`** – (optional) from Dashboard → Webhooks, for verifying webhooks
- **`PAYMONGO_SITE_URL`** – your site base URL (e.g. `https://yoursite.com` or `http://localhost/melmel`)

## 2. Flow

1. **Create a GCash Source** when the user chooses “Pay with GCash” (e.g. in `book.php`):

```php
require_once 'paymongo.php';

$amount_peso = 450; // total from booking
$success_url = PAYMONGO_SITE_URL . '/payment_success.php?booking_id=' . $booking_id;
$failed_url  = PAYMONGO_SITE_URL . '/payment_failed.php';

$result = PayMongo::createGcashSource($amount_peso, $success_url, $failed_url, [
    'name'  => $customer_name,
    'email' => $customer_email,
], ['booking_id' => (string) $booking_id]);

if ($result['success'] && !empty($result['redirect_url'])) {
    header('Location: ' . $result['redirect_url']);
    exit;
}
// else show $result['error']
```

2. User is sent to PayMongo’s GCash checkout, then redirected to **`payment_success.php`** or **`payment_failed.php`**.

3. When the customer completes payment, PayMongo sends a **`source.chargeable`** webhook to your server. **`paymongo_webhook.php`** creates the Payment from the source. In Dashboard → Webhooks, add an endpoint pointing to:

   `https://yoursite.com/melmel/paymongo_webhook.php`  
   and subscribe to **source.chargeable**.

## 3. Files

| File | Purpose |
|------|--------|
| `paymongo_config.php` | API keys and site URL (edit this) |
| `paymongo.php` | `createGcashSource()`, `createPaymentFromSource()`, webhook verification |
| `paymongo_webhook.php` | Handles `source.chargeable`, creates Payment (optional: update booking) |
| `payment_success.php` | Shown after successful GCash redirect |
| `payment_failed.php` | Shown after failed/cancelled GCash redirect |

## 4. Optional: link booking to payment

To store PayMongo source on the booking and update status when payment is created:

1. Add columns, e.g. `payment_source_id` (VARCHAR) and `payment_status` (VARCHAR), to `bookings`.
2. When creating the source, pass `booking_id` in metadata (as in the example above).
3. In `paymongo_webhook.php`, read `booking_id` from the source metadata and update the booking (e.g. set `payment_status = 'paid'` or `status = 'confirmed'`).

Amounts in PayMongo are in **centavos** (₱100 = 10000). The helper converts peso to centavos for you.
