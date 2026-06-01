<?php

declare(strict_types=1);

return [
    /*
    | Commission rate charged to vendors on each order line, as a decimal.
    | 0.15 = 15% platform cut. Used by CheckoutManager to compute
    | vendor_earnings.commission and vendor_earnings.net.
    */
    'commission_rate' => (float) env('MARKETPLACE_COMMISSION_RATE', 0.15),

    'currency' => env('MARKETPLACE_CURRENCY', 'usd'),

    'products' => [
        'max_images_per_product' => 6,
        'image_thumb_width' => 200,
        'image_thumb_height' => 200,
        'image_medium_width' => 600,
        'image_medium_height' => 600,
    ],

    'roles' => [
        'customer' => 'customer',
        'vendor' => 'vendor',
        'admin' => 'admin',
    ],
];
