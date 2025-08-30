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

define('APP_VERSION', '0.25');

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
$slugs = array_values(array_unique($slugs)); // drop duplicates
$slugs = array_filter($slugs, fn($slug) => mb_strlen($slug) <= 34);

if (empty($slugs)) {
    exit();
}

// build or refresh shuffle queue
if (
    !isset($_SESSION['slug_queue']) ||
    empty($_SESSION['slug_queue']) ||
    array_diff($slugs, $_SESSION['slug_queue']) // new slugs added to file
) {
    $_SESSION['slug_queue'] = $slugs;
    shuffle($_SESSION['slug_queue']);
}

// take next from queue
$random_slug = array_shift($_SESSION['slug_queue']);
$_SESSION['last_quote'] = $random_slug;

echo $random_slug;

