<?php
    if ( !isset($loadSliderDatos) ){
        $loadSliderDatos = true;
        $buildSliderDatosCss = esc_url( get_template_directory_uri() . '/css/legos/slider_datos.css' );
?>
<link rel="stylesheet" href="<?php echo $buildSliderDatosCss; ?>" async defer>
<?php  } ?>
<?php if(get_sub_field('items')): ?>
<section class="max-content slider_datos">
    <?php if(get_sub_field('titulo')): ?>
    <h2><?php echo get_sub_field('titulo'); ?></h2>
    <?php
        $tmpSliderDatos = str_replace(array(" ", ",", ";", "?", "¿", "á","é","í","ó","ú", ";"), '', get_sub_field('titulo'));
    ?>
    <hr>
    <?php endif; ?>
    <p class="slider_datos__btns" id="content_btns_<?php echo $tmpSliderDatos; ?>">
        <?php
            $contSlider = 0;
            while(the_repeater_field('items')):
        ?>
        <a onclick="pushEventGTMBtn(this)"
            id="slide_dato__btn<?php echo $contSlider . $tmpSliderDatos; ?>"
            class="btn slide-<?php echo $contSlider . $tmpSliderDatos; ?>"><?php echo get_sub_field('nombre_boton'); ?></a>
        <?php 
        $contSlider = $contSlider + 1;
        endwhile; ?>
    </p>
    <div class="swiper_datos-<?php echo $tmpSliderDatos; ?>">
        <div class="swiper-wrapper">
            <?php while(the_repeater_field('items')): ?>
            <div class="swiper-slide">
                <div class="flex slider_datos__item">
                    <?php if(get_sub_field('imagen')): ?>
                    <img src="<?php echo get_sub_field('imagen'); ?>">
                    <?php endif; ?>
                    <p><?php echo get_sub_field('descripcion'); ?></p>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>
<?php
    if ( !$tmpSliderDatos ) {
        $tmpSliderDatos = '';
    }
    $silderDatosJs = 'slider_datos.js.php?titlePage='.$tmpSliderDatos.'&countItem='.$contSlider;
    $pathFileSliderDatosJS = esc_url( get_template_directory_uri() . '/js/legos/'.$silderDatosJs );
?>
<script src="<?php echo $pathFileSliderDatosJS; ?>"></script>
<?php endif; ?>