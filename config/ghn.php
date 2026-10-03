<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cấu hình API Giao Hàng Nhanh (GHN) Dev / Sandbox (5sao.ghn.dev)
    |--------------------------------------------------------------------------
    |
    | Tất cả thông số Token, Shop ID, Base URL được quản lý từ file .env.
    |
    */
    'base_url' => env('GHN_BASE_URL', 'https://dev-online-gateway.ghn.vn/shiip/public-api'),
    'token' => env('GHN_TOKEN', '754a4815-aa9a-11f1-a973-aee5264794df'),
    'shop_id' => (int) env('GHN_SHOP_ID', 217563),
    'from_district_id' => (int) env('GHN_FROM_DISTRICT_ID', 3440),
    'verify_ssl' => filter_var(env('GHN_VERIFY_SSL', false), FILTER_VALIDATE_BOOLEAN),
];
