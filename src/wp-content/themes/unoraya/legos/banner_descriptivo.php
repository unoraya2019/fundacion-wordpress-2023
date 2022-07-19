<?php $buildBannerDescriptivoCss = esc_url( get_template_directory_uri() . '/css/legos/banner_descriptivo.css' ); ?>
<link rel="stylesheet" href="<?php echo $buildBannerDescriptivoCss; ?>" async defer>
<section class="banner_descriptivo invester-section-1">
  <div class="container margin-0-auto">
    <div class="row">
      <div class="col-md-12 text-center leader-title">
        <h1><?php echo get_sub_field('titulo'); ?></h1>
        <h2><?php echo get_sub_field('subtitulo'); ?></h2>
      </div>
      <div class="col-md-12">
        <div class="sec1-boxes">
            <img  src="<?php echo get_sub_field('banner'); ?>"
                alt="<?php echo get_the_title(); ?>"
                class="img-fluid">
          <div class="sec1-description">
            <?php echo get_sub_field('contenido'); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>