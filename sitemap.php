<?php
// Blog sitemap: sirf live posts (draft / future-scheduled nahi). URL: /blog/sitemap.xml
require __DIR__ . '/lib.php';
header('Content-Type: application/xml; charset=utf-8');
$posts = load_posts();
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
$latest = $posts ? date('Y-m-d', strtotime($posts[0]['date'])) : date('Y-m-d');
echo '  <url><loc>' . SITE_URL . '/blog/</loc><lastmod>' . $latest . '</lastmod></url>' . "\n";
foreach ($posts as $p) {
    $ts = strtotime($p['date']);
    echo '  <url><loc>' . h(SITE_URL . '/blog/' . $p['slug']) . '</loc><lastmod>' . date('Y-m-d', $ts ?: time()) . '</lastmod></url>' . "\n";
}
echo '</urlset>' . "\n";
