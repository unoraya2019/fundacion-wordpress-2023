<?php $buildCartaCss = esc_url( get_template_directory_uri() . '/css/legos/carta.css' ); ?>
<link rel="stylesheet" href="<?php echo $buildCartaCss; ?>" async defer>
<section class="carta_director">
    <?php
        $imageCD = get_sub_field('imagen');
        $urlCD = $imageCD['url'];
        $altCD = $imageCD['alt'];
    ?>
    <img src="<?php echo esc_url($urlCD); ?>"
        alt="<?php echo esc_attr($altCD); ?>">
    <h2><?php echo get_sub_field('titulo'); ?></h2>
    <h3><?php echo get_sub_field('subtitulo'); ?></h3>
    <div class="flex carta_director__content">
        <div class="carta_director__item">
            <?php echo get_sub_field('columna_1'); ?>
        </div>
        <div class="carta_director__item">
            <?php echo get_sub_field('columna_2'); ?>
        </div>
    </div>
</section>