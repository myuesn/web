<?php
if (!defined('ABSPATH')) { exit; }
get_header();
while (have_posts()) : the_post();
    $product = function_exists('wlf_product_data') ? wlf_product_data(get_post()) : array();
    if (!$product) : ?>
        <article class="container section"><h1><?php the_title(); ?></h1><?php the_content(); ?></article>
    <?php else :
        $translated = function ($value) {
            if (!is_array($value)) { $value = array('en' => (string) $value); }
            $fallback = isset($value['en']) ? $value['en'] : '';
            return array(isset($value['en']) ? $value['en'] : $fallback, !empty($value['es']) ? $value['es'] : $fallback, !empty($value['ru']) ? $value['ru'] : $fallback);
        };
        $name = $translated($product['name']);
        $description = $translated($product['description']);
        ?>
        <section class="page-intro"><div class="container"><div class="breadcrumb"><a href="<?php echo esc_url(wlf_page_url('products')); ?>"><?php echo wlf_text('Products', 'Productos', 'Продукция'); ?></a><span>/</span><span><?php echo esc_html($product['code']); ?></span></div><p class="eyebrow"><?php echo esc_html($product['code']); ?></p><h1 data-en="<?php echo esc_attr($name[0]); ?>" data-es="<?php echo esc_attr($name[1]); ?>" data-ru="<?php echo esc_attr($name[2]); ?>"><?php echo esc_html($name[0]); ?></h1></div></section>
        <section class="section"><div class="container wlf-single-product"><div class="detail-image"><img src="<?php echo esc_url($product['image']); ?>" alt="<?php echo esc_attr($name[0]); ?>" data-alt-en="<?php echo esc_attr($name[0]); ?>" data-alt-es="<?php echo esc_attr($name[1]); ?>" data-alt-ru="<?php echo esc_attr($name[2]); ?>" width="700" height="600"></div><div class="detail-info"><p class="part-code">WLF: <?php echo esc_html($product['code']); ?><?php if ($product['oem'] !== '') : ?> · OEM: <?php echo esc_html($product['oem']); ?><?php endif; ?></p>
        <?php echo wlf_text($description[0], $description[1], $description[2], 'p', 'detail-description'); ?>
        <?php if (!empty($product['specs'])) : ?><table class="spec-table"><caption class="sr-only"><?php echo wlf_text('Technical details', 'Datos técnicos', 'Технические данные'); ?></caption><tbody><?php foreach ($product['specs'] as $spec) { $label = $translated($spec['label']); $value = $translated($spec['value']); echo '<tr><th scope="row">' . wlf_text($label[0], $label[1], $label[2]) . '</th><td>' . wlf_text($value[0], $value[1], $value[2]) . '</td></tr>'; } ?></tbody></table><?php endif; ?>
        <?php if (!empty($product['models'])) : ?><?php echo wlf_text('Machine references', 'Referencias de máquinas', 'Модели техники', 'h2'); ?><ul class="model-list"><?php foreach ($product['models'] as $model) { $text = $translated($model); echo '<li>' . wlf_text($text[0], $text[1], $text[2]) . '</li>'; } ?></ul><?php endif; ?>
        <?php echo wlf_text('Confirm the exact reference, machine model and availability with your enquiry. No manufacturer affiliation is implied.', 'Confirme la referencia exacta, el modelo y la disponibilidad en su solicitud. No se implica vinculación con el fabricante.', 'Уточните точный артикул, модель и наличие в запросе. Связь с производителем не подразумевается.', 'p', 'demo-notice'); ?>
        <div class="hero-actions"><button type="button" class="button" data-add="<?php echo esc_attr((string) get_the_ID()); ?>"><?php echo wlf_text('Add to enquiry', 'Añadir a la solicitud', 'Добавить в запрос'); ?><span aria-hidden="true">＋</span></button><a class="text-link" href="<?php echo esc_url(wlf_page_url('quote')); ?>"><?php echo wlf_text('View enquiry', 'Ver solicitud', 'Открыть запрос'); ?> →</a></div></div></div></section>
    <?php endif;
endwhile;
get_footer();
