<?php

//
// Copyright 2025 Kevin Spencer <kevin@kevinspencer.org>
// Permission to use, copy, modify, distribute, and sell this software and its
// documentation for any purpose is hereby granted without fee, provided that
// the above copyright notice appear in all copies and that both that
// copyright notice and this permission notice appear in supporting
// documentation.  No representations are made about the suitability of this
// software for any purpose.  It is provided "as is" without express or
// implied warranty.
//

define('APP_VERSION', '0.28');
define('COOLDOWN_SIZE', 5); // how many recent slugs to avoid repeating

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: 0");
header("Pragma: no-cache");

session_start();

$slug_file = $_SERVER['DOCUMENT_ROOT'] . '/slugs.txt';
$all_slugs = file($slug_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// strip comments + normalize whitespace
$all_slugs = array_filter($all_slugs, function ($line) {
    $line = trim($line);
    return $line !== '' && !str_starts_with($line, '#') && !str_starts_with($line, '//');
});

if (!$all_slugs) {
    exit();
}

// normalize + filter for mobile-friendly length
$slugs = array_map('trim', $all_slugs);
$slugs = array_values(array_unique($slugs));
$slugs = array_filter($slugs, fn($slug) => mb_strlen($slug) <= 34);

if (empty($slugs)) {
    exit();
}

// initialize "unused" pool if not set or if all have been used
if (!isset($_SESSION['unused_slugs']) || empty($_SESSION['unused_slugs'])) {
    $_SESSION['unused_slugs'] = $slugs;
}

// initialize recent history if not set
if (!isset($_SESSION['recent_slugs'])) {
    $_SESSION['recent_slugs'] = [];
}

// pick a random slug from the unused pool
do {
    $index = array_rand($_SESSION['unused_slugs']);
    $random_slug = $_SESSION['unused_slugs'][$index];
} while (
    in_array($random_slug, $_SESSION['recent_slugs'], true) &&
    count($_SESSION['unused_slugs']) > count($_SESSION['recent_slugs'])
);

// remove the chosen slug from the pool
unset($_SESSION['unused_slugs'][$index]);

// update recent history
$_SESSION['recent_slugs'][] = $random_slug;
if (count($_SESSION['recent_slugs']) > COOLDOWN_SIZE) {
    array_shift($_SESSION['recent_slugs']); // keep only last N
}

// remember last shown (optional, for debugging/logging)
$_SESSION['last_quote'] = $random_slug;

echo $random_slug;

