<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title><?= e(setting('maintenance_title')) ?> — <?= e(APP_NAME) ?></title>
    <link rel="icon" href="<?= e(url('assets/images/favicon.svg')) ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= e(asset('css/fonts.css')) ?>">
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body {
            margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 40px 20px;
            background: #1a2a20; color: #f4eee6; text-align: center;
            font: 300 17px/1.65 'Jost', 'Helvetica Neue', Arial, sans-serif;
        }
        main { max-width: 620px; }
        .logo { font: 600 34px 'Cormorant Garamond', Georgia, serif; letter-spacing: .18em; margin: 0 0 40px; }
        .line { width: 48px; height: 1px; background: #b8956a; margin: 0 auto 36px; }
        h1 { font: 400 clamp(34px, 6vw, 52px)/1.1 'Cormorant Garamond', Georgia, serif; margin: 0 0 22px; }
        p { margin: 0 0 14px; color: #ccbeb1; }
    </style>
</head>
<body>
<main>
    <p class="logo">MĀRGHĀ</p>
    <div class="line" aria-hidden="true"></div>
    <h1><?= e(setting('maintenance_title')) ?></h1>
    <?= paras(setting('maintenance_message')) ?>
</main>
</body>
</html>
