<?php
require __DIR__ . '/lib.php';
$blogs = load_posts();   // content/posts/*.json se, newest first
?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <base href="/">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Interior Design Blog | Stylfixx - Expert Tips & Trends</title>
    <meta name="robots" content="index, follow">
    <meta name="description" content="Explore Stylfixx’s interior design blog for the latest trends, space-saving tips, and expert construction insights to inspire functional, stylish spaces.">
    <link rel="canonical" href="https://stylfixx.com/blog/">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/header-links.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-481459022"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'AW-481459022');
    </script>

    <link rel="stylesheet" href="/blog/assets/index.css?v=1">

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/header.php'; ?>

    <div class="pbmit-title-bar-wrapper">
        <div class="container">
            <div class="pbmit-title-bar-content">
                <div class="pbmit-title-bar-content-inner">
                    <div class="pbmit-tbar">
                        <div class="pbmit-tbar-inner container">
                            <h1 class="pbmit-tbar-title">Blog</h1>
                        </div>
                    </div>
                    <div class="pbmit-breadcrumb">
                        <div class="pbmit-breadcrumb-inner">
                            <span><a title="" href="/" class="home"><span>Home</span></a></span>
                            <span class="sep"><i class="pbmit-base-icon-angle-right"></i></span>
                            <span><span class="post-root post post-post current-item">Blog</span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<div class="page-content">
    <section class="blog-section">
        <div class="container">
            <div class="section-title">
                <h2>Latest Articles</h2>
            </div>

            <div class="categories-filter">
                <button class="category-btn active" data-category="all">All Posts</button>
                <?php
                $categories = [];
                foreach ($blogs as $blog) {
                    if (!in_array($blog['category'], $categories)) $categories[] = $blog['category'];
                }
                foreach ($categories as $category) {
                    echo '<button class="category-btn" data-category="' . h(strtolower(str_replace(' ', '-', $category))) . '">' . h($category) . '</button>';
                }
                ?>
            </div>

            <div class="blog-grid">
                <?php if (empty($blogs)): ?>
                    <div class="no-posts">
                        <i class="far fa-file-alt"></i>
                        <h3>No Articles Yet</h3>
                        <p>Check back soon for new interior design insights and tips!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($blogs as $b): ?>
                    <article class="blog-card" data-category="<?php echo h(strtolower(str_replace(' ', '-', $b['category']))); ?>">
                        <div class="card-image">
                            <?php if ($b['image']): ?>
                            <img src="<?php echo h(img_url($b['image'])); ?>" alt="<?php echo h($b['title']); ?>" loading="lazy">
                            <?php endif; ?>
                            <span class="card-category"><?php echo h($b['category']); ?></span>
                        </div>
                        <div class="card-content">
                            <div class="card-date">
                                <i class="far fa-calendar"></i>
                                <?php echo date("d M Y", strtotime($b['date'])); ?>
                            </div>
                            <h3 class="card-title">
                                <a href="/blog/<?php echo h($b['slug']); ?>"><?php echo h($b['title']); ?></a>
                            </h3>
                            <a href="/blog/<?php echo h($b['slug']); ?>" class="card-read-more">
                                Read Article <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterButtons = document.querySelectorAll('.category-btn');
        const blogCards = document.querySelectorAll('.blog-card');

        blogCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            setTimeout(() => { card.style.opacity = '1'; card.style.transform = 'translateY(0)'; }, 100 + index * 100);
        });

        filterButtons.forEach(button => {
            button.addEventListener('click', function () {
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                const filterValue = this.getAttribute('data-category');
                blogCards.forEach(card => {
                    const cardCategory = card.getAttribute('data-category');
                    if (filterValue === 'all' || filterValue === cardCategory) {
                        card.style.display = 'flex';
                        setTimeout(() => { card.style.opacity = '1'; card.style.transform = 'translateY(0)'; }, 10);
                    } else {
                        card.style.opacity = '0';
                        card.style.transform = 'translateY(20px)';
                        setTimeout(() => { card.style.display = 'none'; }, 300);
                    }
                });
            });
        });
    });
</script>
