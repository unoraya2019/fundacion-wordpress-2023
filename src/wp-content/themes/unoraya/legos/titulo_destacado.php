<?php
    if ( !isset($loadTituloDestacado) ){
        $loadTituloDestacado = true;
        $buildTituloDestacadoCss = 'css/titulo_destacado.min.css';
        $styleTituloDestacado = '.titulo_destacado{background:url('.get_sub_field("fondo").') 0 0 no-repeat}.howwedoheight{z-index:999;position:relative}@media (max-width:900px){.titulo_destacado{margin-bottom:70px}.howwedoheight{height:440px}}';
        createRemoteCss($buildTituloDestacadoCss, $styleTituloDestacado);
?>
<link rel="stylesheet" href="<?php echo get_site_url().'/'.$buildTituloDestacadoCss; ?>" async defer>
<?php  } ?>
<section class="titulo_destacado banner_descriptivo howwedo custive-img">
  <div class="max-content">
    <div class="d-flex justify-content-center howwedoheight">
      <div class="align-self-center howwedotext">
        <h2><?php echo get_sub_field('titulo'); ?></h2>
        <?php echo get_sub_field('contenido'); ?>
      </div>
    </div>
  </div>
</section>