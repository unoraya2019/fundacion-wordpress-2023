<?php
    if ( !isset($loadSliderPerfil) ){
        $loadSliderPerfil = true;
        $tmpSliderPerfilPerView = 4;
        if ( $classSlider ) {
            $tmpSliderPerfilPerView = 2;
        }
        $buildSilderPerfilJs = 'js/slider-perfiles.min.js';
        $scriptSliderPerfilesJs = 'const myCustomSlider = document.querySelectorAll(".swiper-perfiles"); for( i=0; i< myCustomSlider.length; i++ ) { myCustomSlider[i].classList.add("swiper-perfiles-" + i); var slider = new Swiper(".swiper-perfiles-" + i, { slidesPerView: 1, spaceBetween: 10, navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev", }, breakpoints: { 1000: { slidesPerView:'.$tmpSliderPerfilPerView.', spaceBetween: 30, }, } }); }';
        createRemoteCss($buildSilderPerfilJs, $scriptSliderPerfilesJs);
        $buildSliderPerfilesCss = esc_url( get_template_directory_uri() . '/css/legos/slider_perfiles.css' );
        $buildSliderPerfilJs = esc_url( get_template_directory_uri() . '/js/legos/slider_perfiles.js' );
?>
<link rel="stylesheet" href="<?php echo $buildSliderPerfilesCss; ?>" async defer>
<script src="<?php echo $buildSliderPerfilJs; ?>"></script>
<?php  } ?>
<section class="slider_perfiles">
    <h2><?php echo get_sub_field('titulo'); ?></h2>
    <div class="max-content swiper swiper-perfiles">
        <div class="swiper-wrapper">
            <?php if(get_sub_field('items')): ?>
              <?php while(the_repeater_field('items')): ?>
                <?php
                    $classSlider = null;
                    if(get_sub_field('contenido')):
                        $classSlider = 'slider_perfiles__contenido_p';
                    endif;
                ?>
                <div class="flex swiper-slide <?php echo $classSlider; ?>">
                    <img alt="<?php echo get_sub_field('titulo'); ?>"
                         src="<?php echo get_sub_field('imagen'); ?>"
                         class="object-fit-cover">
                    <div class="slider_perfiles__text">
                        <h2><?php echo get_sub_field('titulo'); ?></h2>
                        <p><?php echo get_sub_field('descripcion'); ?></p>
                        <p class="slider_perfiles__text__parrafo"><?php echo get_sub_field('contenido'); ?></p>
                    </div>
                </div>
              <?php endwhile; ?>
            <?php endif; ?>
        </div>
        <div class="slider-home__flechas">
            <div class="content__flechas">
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>
    </div>
</section>
