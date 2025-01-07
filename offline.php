<!DOCTYPE HTML>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <link rel="apple-touch-icon" sizes="57x57" href="<?= base_url() ?>assets/manifest_asset/ios/57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="<?= base_url() ?>assets/manifest_asset/ios/60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="<?= base_url() ?>assets/manifest_asset/ios/72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="<?= base_url() ?>assets/manifest_asset/ios/76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="<?= base_url() ?>assets/manifest_asset/ios/114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="<?= base_url() ?>assets/manifest_asset/ios/120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="<?= base_url() ?>assets/manifest_asset/ios/144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="<?= base_url() ?>assets/manifest_asset/ios/152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url() ?>assets/manifest_asset/ios/180.png">
    <link rel="icon" type="image/png" sizes="512x512" href="<?= base_url() ?>assets/manifest_asset/android/android-launchericon-512-512.png">
    <link rel="icon" type="image/png" sizes="192x192" href="<?= base_url() ?>assets/manifest_asset/android/android-launchericon-192-192.png">
    <link rel="manifest" href="<?= base_url() ?>_manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">

    <title>E Pasien</title>
    <?php
    $base_url = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on" || @$_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') ? "https" : "http");
    $base_url .= "://" . $_SERVER['HTTP_HOST'];
    $base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);
    ?>
    <!-- CSS files -->
    <link href="<?= $base_url; ?>dist/css/tabler.css" rel="stylesheet" />
    <link href="<?= $base_url; ?>dist/css/tabler-flags.min.css" rel="stylesheet" />
    <link href="<?= $base_url; ?>dist/css/tabler-payments.min.css" rel="stylesheet" />
    <link href="<?= $base_url; ?>dist/css/tabler-vendors.min.css" rel="stylesheet" />
    <link href="<?= $base_url; ?>dist/css/demo.min.css" rel="stylesheet" />
    <link href="<?= $base_url; ?>dist/css/itm.css" rel="stylesheet" />
    <link rel="manifest" href="<?= $base_url; ?>_manifest.json" data-pwa-version="set_in_manifest_and_pwa_js">
    <!-- Fontawesome -->
    <link href="<?= $base_url; ?>dist/libs/fontawesome/css/all.css" rel="stylesheet" />
</head>

<body class="theme-light" data-highlight="blue2">


    <div id="page">

        <!-- header and footer bar go here-->

        <div class="page-content pb-0">

            <div data-card-height="cover-card" class="card bg-transparent">
                <div class="card-center text-center">
                    <h1 class="fa-3x font-900 mb-0 color-highlight" style="margin-top: 10rem;">KAMU SEDANG OFFLINE</h1>
                    <img src="<?= $base_url; ?>assets/manifest_asset/5.svg" class="preload-img rounded-s img-fluid mt-5" style="width: 15rem;" alt="" srcset="">
                    <br>
                    <br>
                    <h4 class="text-secondary">Kamu sekarang sedang tidak terhubung ke server silahkan tunggu beberapa
                        saat atau coba lagi</h4>
                    <br>
                    <br>
                    <a href="<?= $base_url; ?>index.php" class="back-button mb-4 btn btn-full ml-4 mr-4 btn-m bg-highlight rounded-sm font-900 text-uppercase scale-box">Coba
                        Lagi</a>
                    <br>
                    <br>
                    <p class="opacity-60 font-10">Copyright <span class="copyright-year"></span> Technomedic. All rights
                        reserved</p>
                </div>
            </div>

        </div>
    </div>

    <script src="<?= $base_url ?>dist/libs/jquery/jquery.min.js"></script>
    <!-- Tabler Core -->
    <script src="<?= $base_url ?>dist/js/tabler.min.js"></script>
    <script src="<?= $base_url ?>dist/js/demo.min.js"></script>
</body>