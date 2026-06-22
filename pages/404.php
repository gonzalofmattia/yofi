<?php
http_response_code(404);
$page_title = 'Página no encontrada | ' . SITE_NAME;
$meta_description = 'La página que buscás no existe o fue movida.';
?>
<section class="max-w-xl mx-auto px-6 py-24 text-center">
    <p class="text-sm uppercase tracking-[0.2em] text-earth mb-3">Error 404</p>
    <h1 class="text-3xl sm:text-4xl font-extrabold text-dark">No encontramos esta página</h1>
    <p class="mt-4 text-earth">Puede que el enlace haya cambiado o ya no esté disponible.</p>
    <a href="<?= htmlspecialchars(SITE_URL, ENT_QUOTES, 'UTF-8') ?>" class="inline-flex items-center justify-center mt-8 h-12 px-8 rounded-full bg-primary text-dark font-bold text-sm hover:brightness-95 transition">
        Volver al inicio
    </a>
</section>
