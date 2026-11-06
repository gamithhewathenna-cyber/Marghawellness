<?php
/** @var string $page_title  @var string $page_description  @var string $body_class */
$page_title       = $page_title ?? APP_NAME;
$page_description = $page_description ?? setting('seo_default_description');
$body_class       = $body_class ?? '';
?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($page_title) ?></title>
    <meta name="description" content="<?= e($page_description) ?>">
    <meta name="theme-color" content="#1a2a20">
    <meta property="og:title" content="<?= e($page_title) ?>">
    <meta property="og:description" content="<?= e($page_description) ?>">
    <meta property="og:image" content="<?= e(img('hero-meditation.jpg')) ?>">
    <link rel="icon" href="<?= e(url('assets/images/favicon.svg')) ?>" type="image/svg+xml">
    <link rel="preload" href="<?= e(url('assets/fonts/cormorant-garamond-latin-400-normal.woff2')) ?>" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?= e(url('assets/fonts/jost-latin-300-normal.woff2')) ?>" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="<?= e(asset('css/fonts.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
    <?php if (!empty($preload_image)): ?>
    <link rel="preload" as="image" href="<?= e(img($preload_image)) ?>">
    <?php endif; ?>
</head>
<body class="<?= e($body_class) ?>">
<a class="skip-link" href="#main">Skip to content</a>

<header class="site-header" id="siteHeader">
    <div class="container site-header__inner">
        <a class="logo" href="<?= e(url()) ?>" aria-label="Mārghā — home">MĀRGHĀ</a>

        <nav class="main-nav" id="mainNav" aria-label="Main">
            <ul>
                <?php foreach (MAIN_NAV as $slug): ?>
                <li><a href="<?= e(url($slug)) ?>"<?= current_slug() === $slug ? ' aria-current="page"' : '' ?>><?= e(PAGES[$slug][0]) ?></a></li>
                <?php endforeach; ?>
            </ul>
            <a class="btn btn--rust main-nav__cta" href="<?= e(url('enquire')) ?>">Enquire Now</a>
        </nav>

        <a class="btn btn--rust site-header__cta" href="<?= e(url('enquire')) ?>">Enquire Now</a>

        <button class="nav-toggle" id="navToggle" aria-label="Open menu" aria-expanded="false" aria-controls="mainNav">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>

<main id="main">
