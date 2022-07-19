<?php $buildLibreCss = esc_url( get_template_directory_uri() . '/css/legos/libre.css' ); ?>
<link rel="stylesheet" href="<?php echo $buildLibreCss; ?>" async defer>
<section class="max-content libre">
    <?php echo get_sub_field('contenido'); ?>
</section>