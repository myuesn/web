<?php
/** Optional, non-destructive native-block starter pages. */
if (!defined('ABSPATH')) { exit; }

function wlf_block($name, $attrs, $html) {
    $json = $attrs ? ' ' . wp_json_encode($attrs, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '';
    return '<!-- wp:' . $name . $json . ' -->' . $html . '<!-- /wp:' . $name . ' -->' . "\n";
}

function wlf_group($class, $children) {
    return wlf_block('group', array('className' => $class), '<div class="wp-block-group ' . esc_attr($class) . '">' . $children . '</div>');
}

function wlf_copy($en, $es, $ru, $class = '') {
    $attrs = array('wlfEs' => $es, 'wlfRu' => $ru);
    if ($class !== '') { $attrs['className'] = $class; }
    return wlf_block('paragraph', $attrs, '<p' . ($class !== '' ? ' class="' . esc_attr($class) . '"' : '') . '>' . esc_html($en) . '</p>');
}

function wlf_heading($en, $es, $ru, $level = 2) {
    $attrs = array('wlfEs' => $es, 'wlfRu' => $ru);
    if ($level !== 2) { $attrs['level'] = $level; }
    return wlf_block('heading', $attrs, '<h' . $level . ' class="wp-block-heading">' . esc_html($en) . '</h' . $level . '>');
}

function wlf_button($en, $es, $ru, $url, $outline = false) {
    $attrs = array('wlfEs' => $es, 'wlfRu' => $ru);
    if ($outline) { $attrs['className'] = 'is-style-outline'; }
    return wlf_block('button', $attrs, '<div class="wp-block-button' . ($outline ? ' is-style-outline' : '') . '"><a class="wp-block-button__link wp-element-button" href="' . esc_url($url) . '">' . esc_html($en) . '</a></div>');
}

function wlf_buttons($children, $class = '') {
    return wlf_block('buttons', $class ? array('className' => $class) : array(), '<div class="wp-block-buttons' . ($class ? ' ' . esc_attr($class) : '') . '">' . $children . '</div>');
}

function wlf_image($url, $alt, $class = '') {
    $attrs = array('sizeSlug' => 'full', 'linkDestination' => 'none');
    if ($class) { $attrs['className'] = $class; }
    return wlf_block('image', $attrs, '<figure class="wp-block-image size-full' . ($class ? ' ' . esc_attr($class) : '') . '"><img src="' . esc_url($url) . '" alt="' . esc_attr($alt) . '"/></figure>');
}

function wlf_shortcode_block($code) {
    return wlf_block('shortcode', array(), '[' . $code . ']');
}

function wlf_intro($eyebrow, $title, $description) {
    return wlf_group('page-intro', wlf_group('container', wlf_copy($eyebrow[0], $eyebrow[1], $eyebrow[2], 'eyebrow') . wlf_heading($title[0], $title[1], $title[2], 1) . wlf_copy($description[0], $description[1], $description[2])));
}

function wlf_starter_cta() {
    return wlf_group('cta-band', wlf_group('container cta-inner',
        wlf_group('cta-copy', wlf_copy('YOUR NEXT SEASON STARTS WITH A CONVERSATION', 'SU PRÓXIMA TEMPORADA EMPIEZA CON UNA CONVERSACIÓN', 'ВАШ НОВЫЙ СЕЗОН НАЧИНАЕТСЯ С ОБСУЖДЕНИЯ', 'eyebrow') . wlf_heading('Let’s find the part you need.', 'Encontremos la pieza que necesita.', 'Найдём нужную вам деталь.')) .
        wlf_buttons(wlf_button('Send an enquiry →', 'Enviar solicitud →', 'Отправить запрос →', wlf_page_url('quote')))
    ));
}

function wlf_starter_content($role) {
    $products = wlf_page_url('products');
    $quote = wlf_page_url('quote');
    $hero = esc_url_raw(get_theme_mod('wlf_hero_image', wlf_asset('images/hero-farm.jpg')));
    if ($role === 'home') {
        $content = wlf_group('hero', wlf_image($hero, 'Tractor in an agricultural field', 'hero-background') .
            wlf_group('container hero-inner', wlf_group('hero-content',
                wlf_copy('WLF AGRI PARTS / TÜRKİYE', 'WLF AGRI PARTS / TÜRKİYE', 'WLF AGRI PARTS / ТУРЦИЯ', 'eyebrow') .
                wlf_heading('The right parts.', 'Las piezas adecuadas.', 'Нужные запчасти.', 1) .
                wlf_copy('For the next', 'Para la próxima', 'Для нового', 'hero-heading-line') .
                wlf_copy('harvest.', 'cosecha.', 'урожая.', 'hero-heading-line hero-heading-accent') .
                wlf_copy('Agricultural machinery parts for distributors, dealers and machinery businesses worldwide.', 'Repuestos de maquinaria agrícola para distribuidores, concesionarios y empresas del sector en todo el mundo.', 'Запчасти для сельхозтехники для дистрибьюторов, дилеров и профильных компаний по всему миру.', 'hero-description') .
                wlf_buttons(wlf_button('Explore products →', 'Ver productos →', 'Смотреть продукцию →', $products) . wlf_button('Request a quote →', 'Solicitar presupuesto →', 'Запросить предложение →', $quote, true), 'hero-actions') .
                wlf_copy('Tractors. Harvesters. Everything in between.', 'Tractores. Cosechadoras. Y mucho más.', 'Тракторы. Комбайны. И многое другое.', 'hero-meta')
            )));
        $content .= wlf_group('home-search-section', wlf_group('container', wlf_shortcode_block('wlf_home_search')));
        $categories = array(
            array('tractor', '01 /', array('Tractor parts', 'Repuestos para tractores', 'Запчасти для тракторов'), array('From the engine to the axle. Keep your workhorse moving.', 'Del motor al eje. Mantenga su tractor en marcha.', 'От двигателя до моста. Чтобы ваш трактор продолжал работать.')),
            array('harvester', '02 /', array('Harvester parts', 'Repuestos para cosechadoras', 'Запчасти для комбайнов'), array('For the cutting edge of your most important season.', 'Para estar a la altura de la temporada más importante.', 'Для бесперебойной работы в самый важный сезон.')),
            array('tillage', '03 /', array('Soil working parts', 'Repuestos para laboreo', 'Запчасти для почвообработки'), array('The first connection between your machine and the land.', 'La primera conexión entre su máquina y la tierra.', 'Первое звено между вашей техникой и землёй.')),
        );
        $cards = '';
        foreach ($categories as $category) {
            $cards .= wlf_group('category-card', wlf_copy($category[1], $category[1], $category[1], 'eyebrow') . wlf_heading($category[2][0], $category[2][1], $category[2][2], 3) . wlf_copy($category[3][0], $category[3][1], $category[3][2]) . wlf_buttons(wlf_button('Explore parts →', 'Ver repuestos →', 'Смотреть запчасти →', add_query_arg('category', $category[0], $products))));
        }
        $content .= wlf_group('section', wlf_group('container',
            wlf_group('section-heading', wlf_group('section-copy', wlf_copy('BUILT AROUND YOUR MACHINES', 'EN TORNO A SU MAQUINARIA', 'ДЛЯ ВАШЕЙ ТЕХНИКИ', 'eyebrow') . wlf_heading('One field. Many moving parts.', 'Un campo. Muchas piezas en movimiento.', 'Одно поле. Множество деталей.'))) .
            wlf_group('category-grid', $cards)
        ));
        $content .= wlf_group('section featured-section', wlf_group('container', wlf_copy('THE DETAILS MAKE THE DIFFERENCE', 'LOS DETALLES MARCAN LA DIFERENCIA', 'ДЕТАЛИ ИМЕЮТ ЗНАЧЕНИЕ', 'eyebrow') . wlf_heading('Small parts. Big possibilities.', 'Pequeñas piezas. Grandes posibilidades.', 'Малые детали. Большие возможности.') . wlf_shortcode_block('wlf_featured') . wlf_copy('Confirm references, model compatibility and availability with your enquiry.', 'Confirme las referencias, la compatibilidad y la disponibilidad en su solicitud.', 'Уточните артикулы, совместимость и наличие в своём запросе.', 'demo-notice')));
        $content .= wlf_group('split-section', wlf_group('container split-grid',
            wlf_group('split-copy', wlf_copy('STRAIGHTFORWARD. FROM THE START.', 'CLARIDAD DESDE EL PRINCIPIO.', 'ЯСНОСТЬ С САМОГО НАЧАЛА.', 'eyebrow') . wlf_heading('Less back and forth. More moving forward.', 'Menos idas y vueltas. Más avances.', 'Меньше лишних уточнений. Больше движения вперёд.') . wlf_copy('A good quotation starts with the right details. We bring the conversation back to what your business needs.', 'Un buen presupuesto empieza con los datos adecuados. Centramos la conversación en lo que su empresa necesita.', 'Хорошее предложение начинается с точных данных. Мы сосредотачиваемся на потребностях вашего бизнеса.')) .
            wlf_group('process-list',
                wlf_group('process-step', wlf_group('process-copy', wlf_heading('01 / Find your part', '01 / Encuentre su pieza', '01 / Найдите нужную деталь', 3) . wlf_copy('Search by OEM reference, WLF code or part name.', 'Busque por referencia OEM, código WLF o nombre de pieza.', 'Ищите по OEM-номеру, коду WLF или названию детали.'))) .
                wlf_group('process-step', wlf_group('process-copy', wlf_heading('02 / Tell us the details', '02 / Cuéntenos los detalles', '02 / Укажите подробности', 3) . wlf_copy('Add quantities, machine models and your delivery country.', 'Añada cantidades, modelos de máquina y país de entrega.', 'Укажите количество, модели техники и страну доставки.'))) .
                wlf_group('process-step', wlf_group('process-copy', wlf_heading('03 / Plan the next step', '03 / Planifique el siguiente paso', '03 / Спланируйте следующий шаг', 3) . wlf_copy('Discuss compatibility, availability and export requirements.', 'Comente la compatibilidad, disponibilidad y los requisitos de exportación.', 'Обсудите совместимость, наличие и требования к экспорту.')))
            )
        ));
        $content .= wlf_group('export-band', wlf_group('container', wlf_group('export-copy', wlf_copy('LOCAL ROOTS. INTERNATIONAL OUTLOOK.', 'RAÍCES LOCALES. VISIÓN INTERNACIONAL.', 'МЕСТНЫЕ КОРНИ. МЕЖДУНАРОДНЫЙ ВЗГЛЯД.', 'eyebrow') . wlf_heading('From Türkiye. To your next opportunity.', 'Desde Türkiye. Hacia su próxima oportunidad.', 'Из Турции. К вашим новым возможностям.') . wlf_copy('Tell us your market, your delivery destination and your requirements. Let’s build an enquiry that works across borders.', 'Indíquenos su mercado, destino de entrega y necesidades. Preparemos una solicitud que cruce fronteras.', 'Расскажите о вашем рынке, пункте доставки и требованиях. Подготовим запрос для международной поставки.'))));
        return $content . wlf_starter_cta();
    }
    if ($role === 'products') {
        return wlf_intro(array('THE WLF PARTS CATALOGUE', 'CATÁLOGO DE REPUESTOS WLF', 'КАТАЛОГ ЗАПЧАСТЕЙ WLF'), array('The right part is here.', 'La pieza adecuada está aquí.', 'Нужная деталь здесь.'), array('Search with a reference. Narrow it down by machine. Build your next enquiry.', 'Busque por referencia. Filtre por máquina. Prepare su próxima solicitud.', 'Ищите по артикулу. Уточняйте по технике. Составьте новый запрос.')) . wlf_shortcode_block('wlf_catalogue') . wlf_starter_cta();
    }
    if ($role === 'quote') {
        return wlf_intro(array('B2B ENQUIRIES', 'SOLICITUDES B2B', 'ЗАПРОСЫ B2B'), array('Let’s talk parts.', 'Hablemos de repuestos.', 'Обсудим запчасти.'), array('Your parts list. Your machine details. A clearer starting point for your quotation.', 'Su lista de repuestos. Los datos de su máquina. Un punto de partida más claro para su presupuesto.', 'Ваш список запчастей. Данные вашей техники. Понятная основа для подготовки предложения.')) .
            wlf_group('section', wlf_group('container quote-layout',
                wlf_group('quote-sidebar', wlf_copy('A STRONGER ENQUIRY STARTS HERE', 'UNA MEJOR SOLICITUD EMPIEZA AQUÍ', 'ТОЧНЫЙ ЗАПРОС НАЧИНАЕТСЯ ЗДЕСЬ', 'eyebrow') . wlf_heading('The details that matter.', 'Los detalles que importan.', 'Важные подробности.') . wlf_copy('Add parts from the catalogue or describe your requirement in the form.', 'Añada repuestos del catálogo o describa lo que necesita en el formulario.', 'Добавьте запчасти из каталога или опишите потребность в форме.') . wlf_heading('01 / Reference', '01 / Referencia', '01 / Артикул', 3) . wlf_copy('Include the OEM number, WLF code or part description.', 'Incluya el número OEM, código WLF o descripción de la pieza.', 'Укажите OEM-номер, код WLF или описание детали.') . wlf_heading('02 / Machine', '02 / Máquina', '02 / Техника', 3) . wlf_copy('Tell us the make, model and model year.', 'Indique la marca, el modelo y el año.', 'Укажите марку, модель и год выпуска.') . wlf_heading('03 / Delivery', '03 / Entrega', '03 / Доставка', 3) . wlf_copy('Add quantities, destination and any packaging requirements.', 'Añada cantidades, destino y requisitos de embalaje.', 'Укажите количество, пункт доставки и требования к упаковке.')) .
                wlf_shortcode_block('wlf_enquiry')
            ));
    }
    if ($role === 'about') {
        $content = wlf_intro(array('BEYOND THE PART NUMBER', 'MÁS ALLÁ DEL NÚMERO DE PIEZA', 'БОЛЬШЕ, ЧЕМ НОМЕР ДЕТАЛИ'), array('Your business. Our field.', 'Su negocio. Nuestro campo.', 'Ваш бизнес. Наша сфера.'), array('An export-focused approach to agricultural machinery parts.', 'Un enfoque de repuestos agrícolas orientado a la exportación.', 'Запчасти для сельхозтехники с ориентацией на экспорт.'));
        $content .= wlf_group('section', wlf_group('container about-grid',
            wlf_image($hero, 'Agricultural tractor in a field', 'about-image') .
            wlf_group('about-copy', wlf_copy('ROOTED IN AGRICULTURE', 'CON RAÍCES EN LA AGRICULTURA', 'КОРНИ В СЕЛЬСКОМ ХОЗЯЙСТВЕ', 'eyebrow') . wlf_heading('Every machine has a job. Every part has a purpose.', 'Cada máquina tiene su trabajo. Cada pieza, su función.', 'У каждой машины своя работа. У каждой детали — своё назначение.') . wlf_copy('WLF Agri Parts is being built around one idea: make it easier for agricultural businesses to start the right parts conversation.', 'WLF Agri Parts se desarrolla en torno a una idea: facilitar que las empresas agrícolas inicien la conversación adecuada sobre sus repuestos.', 'WLF Agri Parts создаётся вокруг одной идеи: помочь сельскохозяйственным компаниям начать предметный разговор о нужных запчастях.') . wlf_copy('Our focus is B2B enquiries for tractor, harvester and soil working components. We bring machine details, references and export requirements together in one clear request.', 'Nos centramos en solicitudes B2B de componentes para tractores, cosechadoras y equipos de laboreo. Reunimos datos de la máquina, referencias y requisitos de exportación en una solicitud clara.', 'Наше направление — B2B-запросы на компоненты для тракторов, комбайнов и почвообрабатывающей техники. Мы объединяем данные техники, артикулы и требования к экспорту в одном понятном запросе.') . wlf_buttons(wlf_button('Explore the catalogue →', 'Explorar el catálogo →', 'Посмотреть каталог →', $products)))
        ));
        $cards = wlf_group('value-card', wlf_copy('01 /', '01 /', '01 /') . wlf_heading('Compatibility first', 'La compatibilidad, primero', 'Сначала совместимость', 3) . wlf_copy('We ask for model details, OEM references and dimensions before suitability is confirmed.', 'Solicitamos datos del modelo, referencias OEM y dimensiones antes de confirmar la compatibilidad.', 'Перед подтверждением совместимости мы запрашиваем данные модели, OEM-номера и размеры.'));
        $cards .= wlf_group('value-card', wlf_copy('02 /', '02 /', '02 /') . wlf_heading('Clarity in every enquiry', 'Claridad en cada solicitud', 'Ясность в каждом запросе', 3) . wlf_copy('Quantities, requirements and delivery destinations belong in the same conversation.', 'Cantidades, requisitos y destinos de entrega forman parte de la misma conversación.', 'Количество, требования и пункты доставки обсуждаются вместе.'));
        $cards .= wlf_group('value-card', wlf_copy('03 /', '03 /', '03 /') . wlf_heading('An international outlook', 'Una visión internacional', 'Международный подход', 3) . wlf_copy('Export terms, packaging and documentation are discussed for each order individually.', 'Las condiciones de exportación, el embalaje y la documentación se acuerdan para cada pedido.', 'Условия экспорта, упаковка и документы обсуждаются отдельно для каждого заказа.'));
        return $content . wlf_group('section values-section', wlf_group('container', wlf_copy('WHAT GUIDES THE CONVERSATION', 'LO QUE GUÍA LA CONVERSACIÓN', 'ЧТО ВАЖНО ПРИ ОБСУЖДЕНИИ', 'eyebrow') . wlf_heading('The right details. A better start.', 'Los datos adecuados. Un mejor comienzo.', 'Точные данные. Уверенный старт.') . wlf_group('values-grid', $cards))) . wlf_starter_cta();
    }
    if ($role === 'contact') {
        $content = wlf_intro(array('START A CONVERSATION', 'INICIE UNA CONVERSACIÓN', 'НАЧНЁМ ОБСУЖДЕНИЕ'), array('Good business starts here.', 'Los buenos negocios empiezan aquí.', 'Хорошее сотрудничество начинается здесь.'), array('Tell us what you’re looking for. We’ll take the next step from there.', 'Cuéntenos qué busca. A partir de ahí, daremos el siguiente paso.', 'Расскажите, что вы ищете. Это поможет определить следующий шаг.'));
        $content .= wlf_group('section', wlf_group('container', wlf_shortcode_block('wlf_contact_details') .
            wlf_group('contact-panel', wlf_copy('NOT SURE WHERE TO START?', '¿NO SABE POR DÓNDE EMPEZAR?', 'НЕ ЗНАЕТЕ, С ЧЕГО НАЧАТЬ?', 'eyebrow') . wlf_heading('A part number is a good beginning.', 'Un número de pieza es un buen comienzo.', 'Номер детали — хорошее начало.') . wlf_copy('No reference? Include a machine model, description, quantity and destination country in your enquiry.', '¿No tiene referencia? Incluya el modelo, una descripción, la cantidad y el país de destino.', 'Нет артикула? Укажите модель техники, описание, количество и страну доставки.') . wlf_buttons(wlf_button('Request a quote →', 'Solicitar presupuesto →', 'Запросить предложение →', $quote))) .
            wlf_group('faq', wlf_copy('A FEW USEFUL ANSWERS', 'ALGUNAS RESPUESTAS ÚTILES', 'ПОЛЕЗНЫЕ ОТВЕТЫ', 'eyebrow') . wlf_heading('Before you get in touch.', 'Antes de ponerse en contacto.', 'Прежде чем связаться с нами.') .
                wlf_heading('Why are there no prices online?', '¿Por qué no hay precios en la web?', 'Почему на сайте нет цен?', 3) . wlf_copy('B2B quotations depend on the exact part, quantity, availability and export requirements. Send an enquiry for terms specific to your request.', 'Los presupuestos B2B dependen de la pieza exacta, la cantidad, la disponibilidad y los requisitos de exportación. Envíe una solicitud para recibir condiciones adaptadas.', 'Предложения B2B зависят от конкретной детали, количества, наличия и требований к экспорту. Отправьте запрос, чтобы получить индивидуальные условия.') .
                wlf_heading('How do I confirm that a part will fit?', '¿Cómo confirmo que una pieza es compatible?', 'Как подтвердить, что деталь подойдёт?', 3) . wlf_copy('Include the OEM reference, machine make, model and year. Compatibility is confirmed individually.', 'Incluya la referencia OEM, la marca, el modelo y el año. La compatibilidad se confirma en cada caso.', 'Укажите OEM-номер, марку, модель и год выпуска техники. Совместимость подтверждается индивидуально.') .
                wlf_heading('Can I send multiple parts in one enquiry?', '¿Puedo incluir varias piezas en una solicitud?', 'Можно ли включить несколько деталей в один запрос?', 3) . wlf_copy('Yes. Add parts to your enquiry list, adjust quantities and include additional references in the form.', 'Sí. Añada piezas a su lista, ajuste las cantidades e incluya referencias adicionales en el formulario.', 'Да. Добавьте детали в список, укажите количество и внесите дополнительные артикулы в форму.') .
                wlf_heading('What happens when I submit?', '¿Qué ocurre al enviar?', 'Что происходит после отправки?', 3) . wlf_copy('Your enquiry is saved in our private website administration and sent to the sales team by email. A confirmation means it has been received by the website, not that availability has been confirmed.', 'Su solicitud se guarda en la administración privada y se envía por correo al equipo comercial. La confirmación acredita la recepción en el sitio, no la disponibilidad.', 'Запрос сохраняется в закрытой панели сайта и отправляется отделу продаж по почте. Подтверждение означает получение сайтом, а не подтверждение наличия.'))
        ));
        return $content . wlf_starter_cta();
    }
    return '';
}

function wlf_add_setup_menu() {
    add_theme_page('WLF Başlangıç', 'WLF Başlangıç', 'edit_theme_options', 'wlf-getting-started', 'wlf_setup_page');
}
add_action('admin_menu', 'wlf_add_setup_menu');

function wlf_setup_page() {
    if (!current_user_can('edit_theme_options')) { return; }
    $ids = (array) get_option('wlf_page_ids', array());
    ?>
    <div class="wrap"><h1>WLF Agri Parts — Başlangıç</h1>
    <?php if (isset($_GET['wlf-created'])) : ?><div class="notice notice-success"><p>Başlangıç sayfaları hazır. Var olan sayfalarınızın içeriği değiştirilmedi.</p></div><?php endif; ?>
    <?php if (!function_exists('wlf_products_data')) : ?><div class="notice notice-warning"><p>Ürün yönetimi ve teklif kayıtları için WLF Agri Core eklentisini yükleyip etkinleştirin.</p></div><?php endif; ?>
    <p>İngilizce, İspanyolca ve Rusça tasarımı WordPress blok düzenleyicisinde yönetin. Türkçe yalnızca bu yönetim yardımında kullanılır.</p>
    <h2>1. Düzenlenebilir sayfaları oluşturun</h2><p>Beş yeni WLF sayfası oluşturulur. Mevcut sayfalar, ürünler, menüler, tema ayarları ve site adresi korunur. Sayfalar tekrar oluşturulmaz. Örnek ürünler otomatik eklenmez.</p>
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>"><input type="hidden" name="action" value="wlf_create_starter"><?php wp_nonce_field('wlf_create_starter'); ?>
    <p><label><input type="checkbox" name="wlf_set_front" value="1"> Yeni WLF Home sayfasını sitenin ana sayfası yap</label></p>
    <?php submit_button('WLF başlangıç sayfalarını hazırla'); ?></form>
    <h2>2. Sayfaları ve ürünleri yönetin</h2><p><strong>Sayfalar → Düzenle:</strong> Başlıkları, yazıları, görselleri ve düğmeleri normal WordPress bloklarıyla değiştirin. Bölümleri Liste görünümünde taşıyabilir veya silebilirsiniz. Bir metin ya da düğme bloğunu seçince sağdaki <strong>WLF — Dil çevirileri</strong> alanından İspanyolca ve Rusçasını düzenleyin. Boş çeviride İngilizce gösterilir. İngilizce metni değiştirdiğinizde çevirileri de gözden geçirin.</p>
    <p><strong>Ürünler:</strong> WLF Ürünler menüsünden ürünleri ekleyin, düzenleyin, taslağa alın veya çöp kutusuna taşıyın. Taslak ve silinmiş ürünler katalogda görünmez. Örnek katalog eklerseniz ürünleri gerçek bilgilerle değiştirin; örnek uyum veya stok bilgilerini gerçekmiş gibi yayımlamayın.</p>
    <p><strong>Teklif talepleri:</strong> WLF Teklifler bölümünde kayıtları kontrol edin. E-posta teslimini ayrıca sınayın; gerektiğinde hostinginizin önerdiği SMTP ayarını kullanın.</p>
    <h2>3. Telefon, WhatsApp ve logolar</h2><p><a href="<?php echo esc_url(admin_url('customize.php')); ?>">Görünüm → Özelleştir → WLF — İletişim ve marka</a> bölümünde numaraları ve logoları değiştirin. WhatsApp numarasını ülke koduyla girip gösterme kutusunu açın. Numara boş veya geçersizse düğme gösterilmez. Hareket azaltma tercihine uyulur. Ana sayfa görselini mevcut sayfada görsel bloğunu seçerek değiştirebilirsiniz.</p>
    <p><strong>Görünüm → Menüler:</strong> Ana menü ve alt menü konumlarına kendi bağlantılarınızı atayabilirsiniz. Menü atanmamışsa beş WLF sayfası otomatik listelenir.</p>
    <?php if ($ids) : ?><h2>WLF sayfaları</h2><ul><?php foreach ($ids as $role => $id) { if (get_post_status((int) $id)) { echo '<li><a href="' . esc_url(get_edit_post_link((int) $id)) . '">' . esc_html(get_the_title((int) $id)) . '</a> — <a href="' . esc_url(get_permalink((int) $id)) . '">Önizle</a></li>'; } } ?></ul><?php endif; ?>
    <p>Katalog, öne çıkan ürünler, arama ve teklif formu kısa kod bloklarıyla çalışır: <code>[wlf_catalogue]</code>, <code>[wlf_featured]</code>, <code>[wlf_home_search]</code>, <code>[wlf_enquiry]</code>, <code>[wlf_contact_details]</code>. Bu işlevsel blokları aynı sayfada birden fazla kullanmayın.</p>
    </div>
    <?php
}

function wlf_create_starter_pages() {
    if (!current_user_can('edit_theme_options') || !current_user_can('publish_pages')) { wp_die('Bu işlem için yetkiniz yok.', '', array('response' => 403)); }
    check_admin_referer('wlf_create_starter');
    $roles = array('home' => 'WLF Home', 'products' => 'WLF Products', 'about' => 'WLF About us', 'quote' => 'WLF Request a quote', 'contact' => 'WLF Contact');
    $ids = (array) get_option('wlf_page_ids', array());
    $created = array();
    foreach ($roles as $role => $title) {
        if (!empty($ids[$role]) && get_post_type((int) $ids[$role]) === 'page' && get_post_status((int) $ids[$role]) !== 'trash') { continue; }
        $id = wp_insert_post(array('post_type' => 'page', 'post_status' => 'publish', 'post_title' => $title, 'post_name' => 'wlf-' . $role, 'post_content' => ''), true);
        if (!is_wp_error($id)) { $ids[$role] = $id; $created[$role] = $id; }
    }
    // Resolve every permalink before creating linked button blocks; existing content is never rewritten.
    update_option('wlf_page_ids', $ids, false);
    foreach ($created as $role => $id) {
        wp_update_post(array('ID' => $id, 'post_content' => wlf_starter_content($role)));
    }
    if (!empty($_POST['wlf_set_front']) && !empty($ids['home'])) {
        update_option('show_on_front', 'page');
        update_option('page_on_front', (int) $ids['home']);
    }
    wp_safe_redirect(add_query_arg(array('page' => 'wlf-getting-started', 'wlf-created' => '1'), admin_url('themes.php')));
    exit;
}
add_action('admin_post_wlf_create_starter', 'wlf_create_starter_pages');
