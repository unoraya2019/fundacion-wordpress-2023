<?php
    $imageTI = get_sub_field('imagen');
    $urlTI = $imageTI['url'];
    $altTI = $imageTI['alt'];
if ( !isset($loadTextoConImagen) ){
    $loadTextoConImagen = true;
    $buildTextoConImagenCss = esc_url( get_template_directory_uri() . '/css/legos/texto_con_imagen.css' );
?>
<link rel="stylesheet" href="<?php echo $buildTextoConImagenCss; ?>" async defer>
<?php } ?>
<section class="bg-white what-we-do position-relative ig-tx-zing-zag texto_con_imagen">
    <div class="max-content">
        <div class="d-flex align-items-start flex-wrap-991">
            <div data-wow-duration="1s" class="two-div-in-one-left position-relative order-1-991 wow bounceInLeft">
                <div class="txt-bx-wht position-relative order-2-991">
                    <?php echo get_sub_field('texto'); ?>
                    <?php
                        if(get_sub_field('redireccionar')):
                            $linkRd = get_sub_field('redireccionar');
                            $link_urlRd = $linkRd['url'];
                            $link_titleRd = $linkRd['title'];
                            $link_targetRd = $linkRd['target'] ? $linkRd['target'] : '_self';
                            $titlePage = str_replace(' ', '', get_the_title());
                            $countContentSection = str_word_count(get_sub_field('texto'), 0);
                            $idBtnSection = $titlePage + $countContentSection;
                    ?>
                    <button class="btn btn-primary"
                        type="button"
                        onclick="pushEventGTM(this, '<?php echo $link_urlRd; ?>', '<?php echo $link_targetRd; ?>')"
                        id="btn_<?php echo $idBtnSection; ?>"><?php echo esc_html( $link_titleRd ); ?></button>
                    <?php endif; ?>
                </div>
                <?php if(get_sub_field('texto_destacado')): ?>
                <div class="extra-div position-relative order-2-991">
                    <h3 class="ff-sans-b">
                        <?php echo get_sub_field('texto_destacado'); ?>
                    </h3>
                </div>
                <?php endif; ?>
            </div>
            <div data-wow-duration="2.5s" class="img-rrt-pr order-2-991 wow bounceInRight">
                <img  src="<?php echo esc_url($urlTI); ?>"
                    alt="<?php echo esc_attr($altTI); ?>"
                    class="object-fit-cover">
            </div>
        </div>
    </div>
</section>