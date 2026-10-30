<?php
require __DIR__ . '/lib.php';

$slug = $_GET['slug'] ?? '';
$post = preg_match('/^[a-zA-Z0-9-]+$/', $slug) ? find_post($slug) : null;
if (!$post) {
    header("HTTP/1.0 404 Not Found");
    include($_SERVER['DOCUMENT_ROOT'] . '/404.php');
    exit;
}
$blogs = load_posts();

$articleHtml = md($post['body']) . faq_html($post['faqs']) . (!empty($post['show_cta']) ? cta_html() : '');
$readMin = max(1, (int)ceil(str_word_count(strip_tags($articleHtml)) / 200));
$seoTitle = !empty($post['seo_title']) ? $post['seo_title'] : $post['title'];
$imgUrl = img_url($post['image']);
?>
<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <base href="/">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo h($seoTitle); ?></title>

    <meta name="description" content="<?php echo h($post['description']); ?>">
    <?php if (!empty($post['keywords'])): ?>
    <meta name="keywords" content="<?php echo h($post['keywords']); ?>">
    <?php endif; ?>

    <meta property="og:title" content="<?php echo h($seoTitle); ?>">
    <meta property="og:description" content="<?php echo h($post['description']); ?>">
    <?php if ($imgUrl): ?>
    <meta property="og:image" content="<?php echo h(abs_url($imgUrl)); ?>">
    <?php endif; ?>
    <meta property="og:type" content="article">
    <link rel="canonical" href="<?php echo SITE_URL; ?>/blog/<?php echo h($post['slug']); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/header-links.php'; ?>

    <!-- Blog post design (sab .post-wrap ke andar scoped) -->
    <link rel="stylesheet" href="/blog/assets/post.css?v=1">

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/header.php'; ?>

    <div class="pbmit-title-bar-wrapper">
        <div class="container">
            <div class="pbmit-title-bar-content">
                <div class="pbmit-title-bar-content-inner">
                    <div class="pbmit-tbar">
                        <div class="pbmit-tbar-inner container">
                            <h2 class="pbmit-tbar-title">Blog</h2>
                        </div>
                    </div>
                    <div class="pbmit-breadcrumb">
                        <div class="pbmit-breadcrumb-inner">
                            <span><a title="" href="/" class="home"><span>Home</span></a></span>
                            <span class="sep"><i class="pbmit-base-icon-angle-right"></i></span>
                            <span><a href="/blog"><span>Blog</span></a></span>
                            <span class="sep"><i class="pbmit-base-icon-angle-right"></i></span>
                            <span><span class="post-root post post-post current-item">Article</span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<div class="post-wrap">
    <main>
        <article class="article-hero">
            <div class="container">
                <header class="article-header">
                    <span class="article-category"><?php echo h($post['category']); ?></span>
                    <h1 class="article-title"><?php echo h($post['title']); ?></h1>
                    <div class="article-meta">
                        <div class="meta-item"><i class="far fa-calendar"></i>
                            <span><?php echo date("F d, Y", strtotime($post['date'])); ?></span></div>
                        <div class="meta-item"><i class="far fa-clock"></i>
                            <span><?php echo $readMin; ?> min read</span></div>
                    </div>
                </header>
            </div>
        </article>

        <section class="article-content">
            <div class="container">
                <?php if ($imgUrl): ?>
                <div class="featured-image">
                    <img src="<?php echo h($imgUrl); ?>" alt="<?php echo h($post['title']); ?>">
                </div>
                <?php endif; ?>

                <div id="toc" class="toc"></div>

                <div class="article-body" id="articleBody">
                    <?php echo $articleHtml; ?>
                </div>
            </div>
        </section>
    </main>

    <section class="related-posts">
        <div class="container">
            <h2 class="related-title">Related Articles</h2>
            <div class="related-grid">
                <?php
                $count = 0;
                foreach ($blogs as $related) {
                    if ($related['category'] === $post['category'] && $related['slug'] !== $post['slug']) {
                        $count++; ?>
                        <a href="/blog/<?php echo h($related['slug']); ?>" class="related-card">
                            <?php if ($related['image']): ?>
                            <img src="<?php echo h(img_url($related['image'])); ?>" alt="<?php echo h($related['title']); ?>">
                            <?php endif; ?>
                            <div class="related-content">
                                <h3><?php echo h($related['title']); ?></h3>
                                <span><?php echo date("F d, Y", strtotime($related['date'])); ?></span>
                            </div>
                        </a>
                    <?php }
                    if ($count === 3) break;
                }
                if ($count === 0) echo "<p>No related articles found.</p>";
                ?>
            </div>
        </div>
    </section>
</div>

<?php echo schema_json($post); ?>

<script>
    // Table of Contents (H2 headings se)
    window.addEventListener("load", function () {
        var article = document.getElementById("articleBody"), toc = document.getElementById("toc");
        if (!article || !toc) return;
        var hs = article.querySelectorAll("h2");
        var items = [];
        hs.forEach(function (el, i) {
            if (el.closest(".faq-section")) return;
            el.id = "section-" + i;
            items.push('<li><a href="#section-' + i + '">' + el.innerText + '</a></li>');
        });
        if (!items.length) { toc.style.display = "none"; return; }
        toc.innerHTML = "<strong>Table of Contents</strong><ul>" + items.join("") + "</ul>";
    });
</script>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/footer.php'; ?>
