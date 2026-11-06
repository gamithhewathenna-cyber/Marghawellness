<?php
declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';
maintenance_gate();

$page_slug        = 'home';
$page_title       = setting('seo_home_title');
$page_description = setting('seo_home_description');
$body_class       = 'page-home';
$preload_image    = 'hero-meditation.jpg';

partial('header', compact('page_title', 'page_description', 'body_class', 'preload_image'));

// Homepage sections — order matches the approved design.
partial('home/hero',        ['slides'   => get_hero_slides()]);
partial('home/margha-way');
partial('home/wellness',    ['retreats' => get_retreats()]);
partial('home/process');
partial('home/journeys',    ['journeys' => get_journeys(), 'paths' => get_journey_paths()]);
partial('home/destination');
partial('home/journal',     ['posts'    => get_posts()]);
partial('home/founders');
partial('home/cta');

partial('footer');
