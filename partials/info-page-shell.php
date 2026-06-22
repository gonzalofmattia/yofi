<?php
/**
 * Layout compartido para páginas informativas estáticas.
 * Requiere: $info_page_title, $info_page_intro, $info_page_sections (array de ['title' => '', 'html' => ''])
 */
?>
<section class="bg-cream/40 border-b border-cream">
    <div class="max-w-3xl mx-auto px-6 py-12 sm:py-16 text-center">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-dark"><?= htmlspecialchars($info_page_title, ENT_QUOTES, 'UTF-8') ?></h1>
        <?php if (!empty($info_page_intro)): ?>
        <p class="mt-4 text-base text-earth max-w-2xl mx-auto"><?= htmlspecialchars($info_page_intro, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>
</section>

<section class="max-w-3xl mx-auto px-6 py-12 sm:py-16">
    <div class="space-y-10">
        <?php foreach ($info_page_sections as $section): ?>
        <article>
            <?php if (!empty($section['title'])): ?>
            <h2 class="text-xl font-extrabold text-dark mb-3"><?= htmlspecialchars($section['title'], ENT_QUOTES, 'UTF-8') ?></h2>
            <?php endif; ?>
            <div class="prose prose-sm max-w-none text-dark/80 space-y-3 leading-relaxed">
                <?= $section['html'] ?>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
</section>
