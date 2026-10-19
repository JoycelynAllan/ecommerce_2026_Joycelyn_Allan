<?php

require_once "controllers/ProductController.php";

include 'views/layout/header.php';

$controller = new ProductController();
$filtered   = false;

if (isset($_GET['cat']) && filter_var($_GET['cat'], FILTER_VALIDATE_INT)) {
    $products = $controller->getProductsByCategory((int)$_GET['cat']);
    $heading  = 'Products in this category';
    $filtered = true;
} elseif (isset($_GET['brand']) && filter_var($_GET['brand'], FILTER_VALIDATE_INT)) {
    $products = $controller->getProductsByBrand((int)$_GET['brand']);
    $heading  = 'Products from this brand';
    $filtered = true;
} else {
    $products = $controller->getFeaturedProducts(8);
    $heading  = 'Featured Products';
}

$slides = $filtered ? [] : array_slice($controller->getAllProducts(), 0, 6);

?>

<div class="page-layout">
    <?php include 'views/layout/sidebar.php'; ?>

    <main>
        <?php if (!empty($slides)): ?>
            <section class="slider" id="slider" aria-roledescription="carousel" aria-label="Latest products">
                <div class="slides">
                    <?php foreach ($slides as $s): ?>
                        <article class="slide">
                            <div class="slide-text">
                                <span class="slide-tag"><?= htmlspecialchars($s['cat_name']) ?> &middot; <?= htmlspecialchars($s['brand_name']) ?></span>
                                <h2><?= htmlspecialchars($s['product_title']) ?></h2>
                                <p class="slide-price">$<?= number_format($s['product_price'], 2) ?></p>
                                <div class="slide-actions">
                                    <a class="hero-btn" href="<?= $base ?>/views/single_product.php?pro_id=<?= (int)$s['product_id'] ?>">View Details</a>
                                    <?php if (!is_admin()): ?>
                                        <a class="hero-btn ghost" href="<?= $base ?>/actions/add_to_cart_action.php?add_cart=<?= (int)$s['product_id'] ?>">Add to Cart</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="slide-media">
                                <?php if (!empty($s['product_image'])): ?>
                                    <img src="<?= $base ?>/images/products/<?= htmlspecialchars($s['product_image']) ?>" alt="<?= htmlspecialchars($s['product_title']) ?>">
                                <?php else: ?>
                                    <div class="no-image"><?= htmlspecialchars(strtoupper(substr($s['product_title'], 0, 1))) ?></div>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <button type="button" class="slider-arrow prev" aria-label="Previous slide">&#10094;</button>
                <button type="button" class="slider-arrow next" aria-label="Next slide">&#10095;</button>
                <div class="slider-dots"></div>
            </section>
        <?php elseif (!$filtered): ?>
            <section class="hero">
                <div class="hero-text">
                    <h2>Find something you will love</h2>
                    <p>Browse our products, filter by category or brand, and add your favourites to your cart.</p>
                    <a class="hero-btn" href="views/all_products.php">Shop all products</a>
                </div>
            </section>
        <?php endif; ?>

        <div class="section-head">
            <h3><?= htmlspecialchars($heading) ?></h3>
            <span class="result-count"><?= count($products) ?> product(s)</span>
        </div>

        <?php if (empty($products)): ?>
            <p>No products found.</p>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($products as $p): ?>
                    <article class="product-card">
                        <a class="card-media" href="<?= $base ?>/views/single_product.php?pro_id=<?= (int)$p['product_id'] ?>">
                            <?php if (!empty($p['product_image'])): ?>
                                <img src="<?= $base ?>/images/products/<?= htmlspecialchars($p['product_image']) ?>" alt="<?= htmlspecialchars($p['product_title']) ?>" loading="lazy">
                            <?php else: ?>
                                <div class="no-image">No image</div>
                            <?php endif; ?>
                        </a>

                        <div class="card-body">
                            <?php if (!empty($p['cat_name'])): ?>
                                <span class="card-tag"><?= htmlspecialchars($p['cat_name']) ?></span>
                            <?php endif; ?>
                            <h4><a class="card-title" href="<?= $base ?>/views/single_product.php?pro_id=<?= (int)$p['product_id'] ?>"><?= htmlspecialchars($p['product_title']) ?></a></h4>
                            <p class="price">$<?= number_format($p['product_price'], 2) ?></p>
                        </div>

                        <div class="card-actions">
                            <?php if (is_admin()): ?>
                                <a class="btn-add" href="<?= $base ?>/views/admin/product.php?edit_id=<?= (int)$p['product_id'] ?>">Edit</a>
                            <?php else: ?>
                                <a class="btn-add" href="<?= $base ?>/actions/add_to_cart_action.php?add_cart=<?= (int)$p['product_id'] ?>">Add to Cart</a>
                            <?php endif; ?>
                            <a class="btn-outline" href="<?= $base ?>/views/single_product.php?pro_id=<?= (int)$p['product_id'] ?>">Details</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

<script>
    (function () {
        var slider = document.getElementById('slider');

        if (!slider) {
            return;
        }

        var track = slider.querySelector('.slides');
        var slides = slider.querySelectorAll('.slide');
        var dotsWrap = slider.querySelector('.slider-dots');
        var prev = slider.querySelector('.prev');
        var next = slider.querySelector('.next');
        var index = 0;
        var timer = null;

        function show(n) {
            index = (n + slides.length) % slides.length;
            track.style.transform = 'translateX(-' + (index * 100) + '%)';

            for (var j = 0; j < dotsWrap.children.length; j++) {
                dotsWrap.children[j].classList.toggle('active', j === index);
            }
        }

        function stop() {
            clearInterval(timer);
        }

        function start() {
            var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            if (slides.length > 1 && !reduce) {
                timer = setInterval(function () {
                    show(index + 1);
                }, 5000);
            }
        }

        function restart() {
            stop();
            start();
        }

        if (slides.length < 2) {
            prev.hidden = true;
            next.hidden = true;
            dotsWrap.hidden = true;
        }

        for (var i = 0; i < slides.length; i++) {
            (function (n) {
                var dot = document.createElement('button');
                dot.type = 'button';
                dot.className = 'slider-dot';
                dot.setAttribute('aria-label', 'Go to slide ' + (n + 1));
                dot.addEventListener('click', function () {
                    show(n);
                    restart();
                });
                dotsWrap.appendChild(dot);
            })(i);
        }

        prev.addEventListener('click', function () {
            show(index - 1);
            restart();
        });

        next.addEventListener('click', function () {
            show(index + 1);
            restart();
        });

        slider.addEventListener('mouseenter', stop);
        slider.addEventListener('mouseleave', start);

        show(0);
        start();
    })();
</script>

<?php include 'views/layout/footer.php'; ?>