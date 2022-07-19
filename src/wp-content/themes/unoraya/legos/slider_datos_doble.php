<?php if(get_sub_field('items')): ?>
<?php $buildSliderDatosDobleCss = esc_url( get_template_directory_uri() . '/css/legos/slider_datos_doble.css' ); ?>
<link rel="stylesheet" href="<?php echo $buildSliderDatosDobleCss; ?>" async defer>
<section class="max-content slider_datos">
    <?php if(get_sub_field('titulo')): ?>
    <h2><?php echo get_sub_field('titulo'); ?></h2>
    <hr>
    <?php endif; ?>
    <div class="max-content slider_datos__btns">
        <div class="flex slider_datos__content">
            <?php
                $contSlider = 0;
                while(the_repeater_field('items')):
                    $nombreBtn = get_sub_field('nombre_boton');
            ?>
            <a onclick="pushEventGTMBtn(this)"
                id="slide_dato__btn<?php echo $contSlider; ?>"
                class="btn slide-<?php echo $contSlider; ?>"><?php echo $nombreBtn; ?></a>
            <?php 
            $contSlider = $contSlider + 1;
            endwhile; ?>
        </div>
    </div>
    <div class="swiper_datos">
        <div class="swiper-wrapper">
            <?php while(the_repeater_field('items')): ?>
            <div class="swiper-slide">
                <?php if(get_sub_field('contenido_items')): ?>
                    <?php
                        while(the_repeater_field('contenido_items')):
                            $imageSDD = get_sub_field('imagen');
                            $urlSDD = $imageSDD['url'];
                            $altSDD = $imageSDD['alt'];
                    ?>
                    <div class="flex slider_datos__item">
                        <?php if(get_sub_field('imagen')): ?>
                            <div class="slider_datos__item__img">
                                <span><?php echo esc_attr($altSDD); ?></span>
                                <img src="<?php echo esc_url($urlSDD); ?>"
                                     alt="<?php echo esc_attr($altSDD); ?>">
                            </div>
                        <?php endif; ?>
                        <div>
                        <p><?php echo get_sub_field('descripcion'); ?></p>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>
<?php
    $silderDatosDobleJs = 'slider_datos_doble.js.php?countItem='.$contSlider;
    $pathFileSliderDatosDobleJS = esc_url( get_template_directory_uri() . '/js/legos/'.$silderDatosDobleJs );
?>
<script src="<?php echo $pathFileSliderDatosDobleJS; ?>"></script>
<?php endif; ?>