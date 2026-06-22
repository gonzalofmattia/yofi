<?php
require_once __DIR__ . '/../src/php/content.php';

$page_title = 'Guía de talles | ' . SITE_NAME;
$meta_description = 'Guía de talles Yofi para elegir la medida correcta en ropa infantil. Tablas por edad y consejos de compra.';

$info_page_title = 'Guía de talles';
$info_page_intro = 'En Yofi diseñamos con calce cómodo y holgado. Usá estas referencias y, ante la duda, elegí un talle más.';
$info_page_sections = [
    [
        'title' => 'Consejos generales',
        'html' => '<ul class="list-disc pl-5 space-y-1"><li>Medí a tu chico con ropa liviana y compará con la tabla.</li><li>Si está entre dos talles, preferí el más grande.</li><li>En abrigos y buzos, un poco de holgura ayuda a usar capas abajo.</li><li>En la ficha de cada producto encontrás medidas específicas cuando están disponibles.</li></ul>',
    ],
    [
        'title' => 'Bebés y MINI (0 a 24 meses)',
        'html' => '<div class="overflow-x-auto"><table class="w-full text-sm border border-cream"><thead><tr class="bg-cream/60"><th class="p-2 text-left">Talle</th><th class="p-2 text-left">Altura ref.</th><th class="p-2 text-left">Peso ref.</th></tr></thead><tbody><tr class="border-t border-cream"><td class="p-2">RN</td><td class="p-2">hasta 56 cm</td><td class="p-2">hasta 4 kg</td></tr><tr class="border-t border-cream"><td class="p-2">3M</td><td class="p-2">57–62 cm</td><td class="p-2">4–6 kg</td></tr><tr class="border-t border-cream"><td class="p-2">6M</td><td class="p-2">63–68 cm</td><td class="p-2">6–8 kg</td></tr><tr class="border-t border-cream"><td class="p-2">12M</td><td class="p-2">69–80 cm</td><td class="p-2">8–11 kg</td></tr><tr class="border-t border-cream"><td class="p-2">18M</td><td class="p-2">81–86 cm</td><td class="p-2">11–13 kg</td></tr><tr class="border-t border-cream"><td class="p-2">24M</td><td class="p-2">87–92 cm</td><td class="p-2">13–15 kg</td></tr></tbody></table></div>',
    ],
    [
        'title' => 'Niños 2 a 12 años',
        'html' => '<div class="overflow-x-auto"><table class="w-full text-sm border border-cream"><thead><tr class="bg-cream/60"><th class="p-2 text-left">Talle</th><th class="p-2 text-left">Altura ref.</th><th class="p-2 text-left">Edad ref.</th></tr></thead><tbody><tr class="border-t border-cream"><td class="p-2">2</td><td class="p-2">92–98 cm</td><td class="p-2">2 años</td></tr><tr class="border-t border-cream"><td class="p-2">4</td><td class="p-2">104–110 cm</td><td class="p-2">4 años</td></tr><tr class="border-t border-cream"><td class="p-2">6</td><td class="p-2">116–122 cm</td><td class="p-2">6 años</td></tr><tr class="border-t border-cream"><td class="p-2">8</td><td class="p-2">128–134 cm</td><td class="p-2">8 años</td></tr><tr class="border-t border-cream"><td class="p-2">10</td><td class="p-2">140–146 cm</td><td class="p-2">10 años</td></tr><tr class="border-t border-cream"><td class="p-2">12</td><td class="p-2">152–158 cm</td><td class="p-2">12 años</td></tr></tbody></table></div><p class="mt-3 text-sm">Las medidas son orientativas: cada marca y modelo puede variar levemente.</p>',
    ],
    [
        'title' => '¿Necesitás ayuda?',
        'html' => '<p>Escribinos por WhatsApp con altura, peso y edad de tu chico y te recomendamos el talle ideal para el producto que te interesa.</p>',
    ],
];

include __DIR__ . '/../partials/info-page-shell.php';
