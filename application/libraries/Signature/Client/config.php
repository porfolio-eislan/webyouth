<?php

return [
    /*
     |--------------------------------------------------------------------------
     | E-Sign Host
     |--------------------------------------------------------------------------
     | Pastikan IP Esign Client Service sudah sesuai
     */
    // 'host' => 'https://esign.manggaraibaratkab.go.id',
    'host' => 'http://10.0.8.200',
    // 'host' => 'http://esign.banyumaskab.go.id',
    /*
     |--------------------------------------------------------------------------
     | E-Sign Default Credentials
     |--------------------------------------------------------------------------
     | Basic Auth credentials yang didapatkan dari halaman Esign Clinet Service Dashboard
     | Default esign - qwerty
     */
    'client_id' => 'esign',
    'client_secret' => 'qwerty'
];
