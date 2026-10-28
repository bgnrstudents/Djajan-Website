<?php

/*
|--------------------------------------------------------------------------
| CORS
|--------------------------------------------------------------------------
| Aplikasi Flutter versi Web (Chrome) berjalan di origin yang berbeda dari
| Laravel (mis. localhost:5xxxx vs localhost:8000). Tanpa header CORS,
| browser menolak memuat GAMBAR lewat Image.network milik Flutter, sehingga
| muncul "Gambar tidak bisa dimuat" walaupun file-nya ada.
|
| File di folder public/uploads/ disajikan langsung oleh web server (tanpa
| melewati Laravel), jadi tidak bisa diberi header. Karena itu aplikasi
| mengambil gambar lewat route /file_gambar/{folder}/{file} (lihat
| routes/web.php) dan route itu didaftarkan di sini.
*/

return [
    'paths' => ['api/*', 'file_gambar/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,
];
