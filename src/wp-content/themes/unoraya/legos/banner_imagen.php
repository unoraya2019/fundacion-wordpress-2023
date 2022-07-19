<?php $buildBannerImagenCss = esc_url( get_template_directory_uri() . '/css/legos/banner_imagen.css' ); ?>
<link rel="stylesheet" href="<?php echo $buildBannerImagenCss; ?>" async defer>
<section class="banner_imagen" style="background-image: url(<?php echo get_sub_field('imagen'); ?>)"></section>