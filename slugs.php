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

if (!$all_slugs) {
    echo "Error: Could not read slugs file.";
    exit();
}

// because mobile, and some long strings seemed like a good idea at the time

$slugs = array_filter($all_slugs, fn($slug) => mb_strlen(trim($slug)) <= 34);

if (empty($slugs)) {
    echo "No suitable slugs found.";
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

