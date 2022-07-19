<?php
    $imageIT = get_sub_field('imagen');
    $urlIT = $imageIT['url'];
    $altIT = $imageIT['alt'];
if ( !isset($loadImagenConTexto) ){
    $loadImagenConTexto = true;
    $buildImagenConTextoCss = esc_url( get_template_directory_uri() . '/css/legos/imagen_con_texto.css' );
?>
<link rel="stylesheet" href="<?php echo $buildImagenConTextoCss; ?>" async defer>
<?php } ?>
<section class="imagen_con_texto ig-tx-zing-zag bg-white">
  <div class="max-content">
    <div class="d-flex multiple-reapt align-items-start part-two flex-wrap-991">
      <div data-wow-duration="2.5s"
         class="img-points order-2-991  wow bounceInRight">
          <img src="<?php echo esc_url($urlIT); ?>"
                alt="<?php echo esc_attr($altIT); ?>"
                class="object-fit-cover">
      </div>
      <div data-wow-duration="1s"
            class="two-div-in-one position-relative order-1-991  wow bounceInLeft">
        <?php if(get_sub_field('texto')): ?>
        <div class="txt-points position-relative">
          <div class="pra-p">
            <?php echo get_sub_field('texto'); ?>
            <?php
                if(get_sub_field('redireccionar')):
                    $linkRdT = get_sub_field('redireccionar');
                    $link_urlRdT = $linkRdT['url'];
                    $link_titleRdT = $linkRdT['title'];
                    $link_targetRdT = $linkRdT['target'] ? $linkRdT['target'] : '_self';
                    $titlePage = str_replace(' ', '', get_the_title());
                    $countContentSection = str_word_count(get_sub_field('texto'), 0);
                    $idBtnSection = $titlePage + $countContentSection;
            ?>
            <button class="btn btn-primary"
                type="button"
                onclick="pushEventGTM(this, '<?php echo $link_urlRdT; ?>', '<?php echo $link_targetRdT; ?>')"
                id="btn_<?php echo $idBtnSection; ?>"><?php echo esc_html( $link_titleRdT ); ?></button>
            <?php endif; ?>
          </div>
        </div>
        <?php endif; ?>
        <?php if(get_sub_field('texto_destacado')): ?>
        <div class="extra-div">
          <p><b><?php echo get_sub_field('texto_destacado'); ?></b></p>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>