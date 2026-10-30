<?php
// Blog ka common code: posts load karna, markdown render, schema, CTA
error_reporting(error_reporting() & ~E_DEPRECATED);

define('POSTS_DIR', __DIR__ . '/content/posts/');
define('SITE_URL', 'https://stylfixx.com');

require_once __DIR__ . '/Parsedown.php';

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// Image path: Decap "/blog/images/x.webp" deta hai, purane style me sirf "x.webp"
function img_url($image) {
    if ($image === '' || $image === null) return '';
    if (preg_match('#^(https?:)?//#', $image) || $image[0] === '/') return $image;
    return '/blog/images/' . rawurlencode($image);
}
function abs_url($path) {
    return preg_match('#^https?://#', $path) ? $path : SITE_URL . $path;
}

// Sab posts (newest first). Draft posts site par nahi dikhte.
function load_posts() {
    $posts = [];
    foreach (glob(POSTS_DIR . '*.json') as $file) {
        $d = json_decode(file_get_contents($file), true);
        if (!is_array($d) || !empty($d['draft'])) continue;
        $d['slug'] = basename($file, '.json');   // URL = file ka naam
        $d += ['title' => '', 'category' => 'General', 'image' => '', 'date' => '2000-01-01',
               'description' => '', 'body' => '', 'faqs' => [], 'show_cta' => true];
        $posts[] = $d;
    }
    usort($posts, function ($a, $b) { return strtotime($b['date']) <=> strtotime($a['date']); });
    return $posts;
}

function find_post($slug) {
    foreach (load_posts() as $p) if ($p['slug'] === $slug) return $p;
    return null;
}

function md($text) {
    static $pd = null;
    if (!$pd) { $pd = new Parsedown(); $pd->setSafeMode(false); }  // trusted editors, HTML allowed
    return $pd->text((string)$text);
}

function faq_html($faqs) {
    if (empty($faqs)) return '';
    $o = '<div class="faq-section"><h2>FAQs</h2>';
    foreach ($faqs as $i => $f) {
        if (empty($f['question'])) continue;
        $o .= '<details class="faq-item" name="faq"' . ($i === 0 ? ' open' : '') . '><summary><h3>'
            . h($f['question']) . '</h3></summary><div class="faq-answer">'
            . md($f['answer'] ?? '') . '</div></details>';
    }
    return $o . '</div>';
}

function cta_html() {
    return '<div class="civil-works-cta"><div class="cta-icon"><i class="fas fa-hard-hat"></i></div>
<div class="cta-content"><h3>Planning Your Project?</h3>
<p>Get expert guidance on design, materials, and cost planning from the <a href="https://stylfixx.com/civil-works.php">Stylfixx team</a>.</p>
<div class="cta-buttons">
<a href="https://stylfixx.com/civil-works.php" class="cta-button primary"><i class="fas fa-tools"></i> Explore Our Services</a>
<a href="https://stylfixx.com/contact-us.php" class="cta-button secondary"><i class="fas fa-phone"></i> Contact Us</a>
</div></div></div>';
}

function schema_json($post) {
    $flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG;
    $url = SITE_URL . '/blog/' . $post['slug'];
    $article = [
        '@context' => 'https://schema.org', '@type' => 'Article',
        'headline' => $post['title'], 'description' => $post['description'],
        'datePublished' => date('Y-m-d', strtotime($post['date'])),
        'dateModified' => date('Y-m-d', strtotime($post['date'])),
        'author' => ['@type' => 'Organization', 'name' => 'Stylfixx'],
        'publisher' => ['@type' => 'Organization', 'name' => 'Stylfixx',
            'logo' => ['@type' => 'ImageObject', 'url' => SITE_URL . '/images/logo.png']],
        'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $url],
    ];
    if ($post['image']) $article['image'] = [abs_url(img_url($post['image']))];
    $out = '<script type="application/ld+json">' . json_encode($article, $flags) . '</script>';

    $qs = [];
    foreach ($post['faqs'] as $f) {
        if (empty($f['question'])) continue;
        $qs[] = ['@type' => 'Question', 'name' => $f['question'],
                 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['answer'] ?? '']];
    }
    if ($qs) {
        $out .= '<script type="application/ld+json">' . json_encode(
            ['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $qs], $flags) . '</script>';
    }
    return $out;
}
