<?php
/**
 * PayMongo API configuration for GCash payments.
 * Add your API keys from https://dashboard.paymongo.com
 *
 * Use test keys (pk_test_..., sk_test_...) for development.
 * Use live keys (pk_live_..., sk_live_...) for production.
 */

// Public key – used to create Sources (GCash checkout). Safe to use in frontend.
define('PAYMONGO_PUBLIC_KEY', 'pk_test_HgdnEYY8nBQXWBXj8ay3t4Wt');

// Secret key – used to create Payments and in webhooks. Keep private, server-side only.
define('PAYMONGO_SECRET_KEY', 'sk_test_2cfusHz7fGebV2YKHBZMMyR9');

// Webhook signing secret (whsec_...) from PayMongo Dashboard → Webhooks. Optional but recommended.
define('PAYMONGO_WEBHOOK_SECRET', '');

// Base URL of your site (for redirect URLs). No trailing slash.
// e.g. https://yoursite.com or http://localhost/melmel
define('PAYMONGO_SITE_URL', defined('SITE_URL') ? SITE_URL : 'http://localhost/melmel');

// PayMongo API base URL
define('PAYMONGO_API_URL', 'https://api.paymongo.com');
