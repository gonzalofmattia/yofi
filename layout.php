<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/src/php/content.php';

$empresaConfig = empresa_config_get_all();
$freeShippingThreshold = free_shipping_threshold();
$preheaderShippingText = free_shipping_preheader_text();
$whatsappFloatHref = whatsapp_href($empresaConfig['whatsapp'] ?? '');
$instagramHref = normalize_social_url($empresaConfig['instagram'] ?? '', 'instagram');
$facebookHref = normalize_social_url($empresaConfig['facebook'] ?? '', 'facebook');

if (!isset($page_file) || !file_exists($page_file)) {
    http_response_code(404);
    $page_file = __DIR__ . '/pages/404.php';
    $page_id = '404';
}

$page_title = $page_title ?? SITE_NAME;
$meta_description = $meta_description ?? (defined('SITE_DESCRIPTION') ? SITE_DESCRIPTION : '');
$meta_keywords = $meta_keywords ?? (defined('SITE_KEYWORDS') ? SITE_KEYWORDS : '');

ob_start();
if (file_exists($page_file)) {
    include $page_file;
} else {
    echo '<div class="container mx-auto px-4 py-16 text-center"><h1 class="text-2xl font-bold text-dark">Página no encontrada</h1></div>';
}
$page_content = trim(ob_get_clean());

?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?></title>
    <?php if ($meta_description !== ''): ?>
    <meta name="description" content="<?php echo htmlspecialchars($meta_description, ENT_QUOTES, 'UTF-8'); ?>">
    <?php endif; ?>
    <?php if ($meta_keywords !== ''): ?>
    <meta name="keywords" content="<?php echo htmlspecialchars($meta_keywords, ENT_QUOTES, 'UTF-8'); ?>">
    <?php endif; ?>

    <?php include __DIR__ . '/partials/favicon-head.php'; ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary:   '#FAAF7D',
            secondary: '#96AFC8',
            accent:    '#E1644B',
            earth:     '#7D7D64',
            cream:     '#FAE1C8',
            dark:      '#2C2A27',
          },
          fontFamily: {
            sans: ['Nunito', 'sans-serif'],
          }
        }
      }
    }
    </script>

    <style>
        :root {
            --color-primary: #FAAF7D;
            --color-secondary: #96AFC8;
            --color-accent: #E1644B;
            --color-earth: #7D7D64;
            --color-cream: #FAE1C8;
            --color-dark: #2C2A27;
        }
        body {
            font-family: 'Nunito', sans-serif;
        }
    </style>
</head>
<body class="bg-white text-dark antialiased" data-page="<?php echo htmlspecialchars($page_id ?? 'page', ENT_QUOTES, 'UTF-8'); ?>" data-base-path="<?php echo htmlspecialchars(BASE_PATH, ENT_QUOTES, 'UTF-8'); ?>" data-free-shipping-threshold="<?php echo (int)$freeShippingThreshold; ?>">
    <div class="min-h-screen flex flex-col">
        <?php include __DIR__ . '/partials/header.php'; ?>

        <main id="contenido" class="flex-1" tabindex="-1">
            <?php echo $page_content; ?>
        </main>

        <?php include __DIR__ . '/partials/footer.php'; ?>
    </div>

    <?php include __DIR__ . '/partials/cart-drawer.php'; ?>
    <?php include __DIR__ . '/partials/whatsapp-float.php'; ?>

    <script src="<?php echo asset_path('js/cart.js'); ?>"></script>
</body>
</html>
