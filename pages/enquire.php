<?php
/**
 * Enquire page — the same form as the pop-up, as a normal page.
 * Used when JavaScript is off (and after a no-JavaScript submit). Loaded by page.php.
 */
$body_class = 'page-enquire';
$notice     = $_SESSION['enquire_notice'] ?? null;
$old        = $_SESSION['enquire_old'] ?? [];
unset($_SESSION['enquire_notice'], $_SESSION['enquire_old']);

partial('header', compact('page_title', 'body_class'));
partial('enquire', ['mode' => 'page', 'notice' => $notice, 'old' => $old]);
partial('footer');
