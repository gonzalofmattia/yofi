<footer class="w-full text-white mt-auto" style="background-color: #2C2A27;" data-component="footer">
    <?php
    $empresa = $empresaConfig ?? empresa_config_get_all();
    $instagramHref = $instagramHref ?? normalize_social_url($empresa['instagram'] ?? '', 'instagram');
    $facebookHref = $facebookHref ?? normalize_social_url($empresa['facebook'] ?? '', 'facebook');
    $whatsappHref = whatsapp_href($empresa['whatsapp'] ?? '');
    $emailContacto = trim($empresa['email_contacto'] ?? '');
    $telefono = trim($empresa['telefono'] ?? '');
    $direccion = trim($empresa['direccion'] ?? '');
    $horario = trim($empresa['horario_atencion'] ?? '');
    $hasSocial = $instagramHref !== '' || $facebookHref !== '';
    ?>
    <div class="py-12 px-8">
        <div class="flex items-center justify-between gap-6">
            <a href="<?= SITE_URL ?>" class="flex-shrink-0" aria-label="Yofi">
                <img src="<?= asset_path('img/logo-yofi.png') ?>"
                     alt="Yofi" height="32" class="h-8 w-auto">
            </a>
            <?php if ($hasSocial): ?>
            <div class="flex items-center gap-4">
                <?php if ($instagramHref !== ''): ?>
                <a href="<?= htmlspecialchars($instagramHref, ENT_QUOTES, 'UTF-8') ?>" class="text-white hover:text-primary transition-colors" aria-label="Instagram" target="_blank" rel="noopener noreferrer">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                    </svg>
                </a>
                <?php endif; ?>
                <?php if ($facebookHref !== ''): ?>
                <a href="<?= htmlspecialchars($facebookHref, ENT_QUOTES, 'UTF-8') ?>" class="text-white hover:text-primary transition-colors" aria-label="Facebook" target="_blank" rel="noopener noreferrer">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-10">
            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider mb-4 text-white">Ayuda</h3>
                <ul class="space-y-2 text-sm text-white/60">
                    <li><a href="<?= htmlspecialchars(page_path('preguntas-frecuentes'), ENT_QUOTES, 'UTF-8') ?>" class="hover:text-white transition-colors">Preguntas frecuentes</a></li>
                    <li><a href="<?= htmlspecialchars(page_path('envios-devoluciones'), ENT_QUOTES, 'UTF-8') ?>" class="hover:text-white transition-colors">Envíos y devoluciones</a></li>
                    <li><a href="<?= htmlspecialchars(page_path('guia-talles'), ENT_QUOTES, 'UTF-8') ?>" class="hover:text-white transition-colors">Guía de talles</a></li>
                    <li><a href="<?= htmlspecialchars(page_path('contacto'), ENT_QUOTES, 'UTF-8') ?>" class="hover:text-white transition-colors">Contacto</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider mb-4 text-white">Mi cuenta</h3>
                <ul class="space-y-2 text-sm text-white/60">
                    <li><a href="<?php echo page_path('login'); ?>" class="hover:text-white transition-colors">Ingresar</a></li>
                    <li><a href="<?php echo page_path('registro'); ?>" class="hover:text-white transition-colors">Crear cuenta</a></li>
                    <li><a href="<?php echo page_path('mi-cuenta'); ?>" class="hover:text-white transition-colors">Mis pedidos</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider mb-4 text-white">Contacto</h3>
                <ul class="space-y-2 text-sm text-white/60">
                    <?php if ($whatsappHref !== ''): ?>
                    <li>
                        <a href="<?= htmlspecialchars($whatsappHref, ENT_QUOTES, 'UTF-8') ?>" class="hover:text-white transition-colors" target="_blank" rel="noopener noreferrer">
                            WhatsApp: <?= htmlspecialchars(trim($empresa['whatsapp'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if ($emailContacto !== ''): ?>
                    <li>
                        <a href="mailto:<?= htmlspecialchars($emailContacto, ENT_QUOTES, 'UTF-8') ?>" class="hover:text-white transition-colors">
                            <?= htmlspecialchars($emailContacto, ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if ($telefono !== ''): ?>
                    <li>
                        <a href="tel:<?= htmlspecialchars(preg_replace('/[^\d+]/', '', $telefono) ?? '', ENT_QUOTES, 'UTF-8') ?>" class="hover:text-white transition-colors">
                            <?= htmlspecialchars($telefono, ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if ($direccion !== ''): ?>
                    <li><?= htmlspecialchars($direccion, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endif; ?>
                    <?php if ($horario !== ''): ?>
                    <li><?= nl2br(htmlspecialchars($horario, ENT_QUOTES, 'UTF-8')) ?></li>
                    <?php endif; ?>
                    <?php if ($whatsappHref === '' && $emailContacto === '' && $telefono === '' && $direccion === '' && $horario === ''): ?>
                    <li><a href="<?= htmlspecialchars(page_path('contacto'), ENT_QUOTES, 'UTF-8') ?>" class="hover:text-white transition-colors">Ver formas de contacto</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="border-t border-white/10">
        <div class="py-6 px-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <p class="text-xs text-white/50">Medios de pago</p>
                    <div class="flex flex-wrap items-center gap-2 mt-2">
                        <span class="inline-flex items-center justify-center px-3 py-1.5 bg-white rounded text-xs font-semibold text-dark">Mercado Pago</span>
                        <span class="inline-flex items-center justify-center px-3 py-1.5 bg-white rounded text-xs font-semibold text-dark">Visa</span>
                        <span class="inline-flex items-center justify-center px-3 py-1.5 bg-white rounded text-xs font-semibold text-dark">Mastercard</span>
                    </div>
                </div>
                <p class="text-xs text-white/60">Envíos por Zipnova</p>
            </div>
            <p class="text-center text-xs text-white/40 mt-6">
                © <?= date('Y') ?> <?php echo SITE_NAME; ?>. Todos los derechos reservados.
            </p>
        </div>
    </div>
</footer>
