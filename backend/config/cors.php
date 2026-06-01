<?php

return [
    "paths" => ["api/*", "sanctum/csrf-cookie"],

    "allowed_methods" => ["*"],

    // Jalur 1: Daftarkan origin secara eksplisit (Sangat Direkomendasikan)
    "allowed_origins" => ["http://localhost:3000"],

    "allowed_origins_patterns" => [],

    "allowed_headers" => ["*"],

    "exposed_headers" => [],

    "max_age" => 0,

    // Ubah jadi true jika frontend mengirimkan cookie/session (misal: Nuxt Auth / Sanctum)
    "supports_credentials" => true,
];
