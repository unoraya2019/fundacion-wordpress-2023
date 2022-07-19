<?php $buildCifrasCss = esc_url( get_template_directory_uri() . '/css/legos/cifras.css' ); ?>
<link rel="stylesheet" href="<?php echo $buildCifrasCss; ?>" async defer>
<section class="cifras max-content">
    <div class="flex cifras__content">
        <?php if(get_sub_field('items')): ?>
          <?php while(the_repeater_field('items')):?>
            <div class="flex cifras__item">
                <img src="<?php echo get_sub_field('icono'); ?>"
                     alt="<?php echo get_sub_field('titulo'); ?>">
                <div class="cifras__texto">
                    <h2><?php echo get_sub_field('titulo'); ?></h2>
                    <h4><?php echo get_sub_field('descripcion'); ?></h4>
                </div>
            </div>
          <?php endwhile; ?>
        <?php endif; ?>
    </div>
</section>