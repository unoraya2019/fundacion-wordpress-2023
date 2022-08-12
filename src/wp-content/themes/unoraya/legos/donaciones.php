<?php
    $imageDn = get_sub_field('imagen');
    $urlDn = $imageDn['url'];
    $altDn = $imageDn['alt'];
    $linkDn = get_sub_field('link');
    $link_urlDn = $linkDn['url'];
    $link_titleDn = $linkDn['title'];
    $link_targetDn = $linkDn['target'] ? $linkDn['target'] : '_self';
?>
<?php $buildDonacionesCss = esc_url( get_template_directory_uri() . '/css/legos/donaciones.css' ); ?>
<link rel="stylesheet" href="<?php echo $buildDonacionesCss; ?>" async defer>
<section class="max-content donaciones">
    <img src="<?php echo esc_url($urlDn); ?>"
                 alt="<?php echo esc_attr($altDn); ?>"
                 class="object-fit-cover"/>
    <h5>Recaudado</h5>
    <div class="progress">
      <div class="progress-bar" role="progressbar"
            style="width: <?php echo get_sub_field('porcentaje'); ?>%"
            aria-valuenow="<?php echo get_sub_field('porcentaje'); ?>"
            aria-valuemin="0"
            aria-valuemax="100"></div>
    </div>
    <div class="flex">
        <span><?php echo get_sub_field('recaudado'); ?></span>
        <label>de</label>
        <span><?php echo get_sub_field('total'); ?></span>
    </div>
    <br>
    <?php echo get_sub_field('descripcion'); ?>
    <a class="btn btn_evento"
       onclick="pushEventGTM(this, '<?php echo $link_urlDn; ?>', '<?php echo $link_targetDn; ?>', 'h5')">
        <?php echo esc_html( $link_titleDn ); ?>
    </a>
</section>