<?php
    $imageNt = get_sub_field('banner');
?>
<?php $buildNoticiasCss = esc_url( get_template_directory_uri() . '/css/legos/noticias.css' ); ?>
<link rel="stylesheet" href="<?php echo $buildNoticiasCss; ?>" async defer>
<section class="article">
  <div class="max-content">
    <div class="row">
      <div class="col-md-8 offset-md-2">
        <h1><?php echo get_sub_field('titulo'); ?></h1>
        <img src="<?php echo $imageNt; ?>"
             alt="<?php echo get_sub_field('titulo'); ?>"
             class="img-fluid">
        <div class="description">
          <?php echo get_sub_field('contenido'); ?>
        </div>
      </div>
    </div>
  </div>
</section>
