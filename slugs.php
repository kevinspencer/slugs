<?php
//
// some of these shamelessly borrowed from the great Merlin Mann
//
// https://gist.github.com/merlinmann/d4c137662eea4b27ed0b0a506c467044
//

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: 0");
header("Pragma: no-cache");

session_start();

$slug_file = $_SERVER['DOCUMENT_ROOT'] . '/slugs.txt';
$all_slugs = file($slug_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// let's allow/ignore commented out lines
$all_slugs = array_filter($all_slugs, function($line) {
    $line = trim($line);
    return !(str_starts_with($line, '#') || str_starts_with($line, '//'));
});

if (!$all_slugs) {
    exit();
}

// because mobile, and some long strings seemed like a good idea at the time

$slugs = array_filter($all_slugs, fn($slug) => mb_strlen(trim($slug)) <= 34);

if (empty($slugs)) {
    exit();
}

$last_slug = $_SESSION['last_quote'] ?? null;

if (count($slugs) > 1 && $last_slug !== null) {
    $slugs = array_values(array_filter($slugs, fn($slug) => trim($slug) !== trim($last_slug)));
}

$random_slug = $slugs[array_rand($slugs)];
$_SESSION['last_quote'] = $random_slug;

echo $random_slug;
?>

