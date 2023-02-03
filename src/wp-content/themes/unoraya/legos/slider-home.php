<?php $buildSliderHomeCss = esc_url( get_template_directory_uri() . '/css/legos/slider-home.css' ); ?>
<link rel="stylesheet" href="<?php echo $buildSliderHomeCss; ?>" async defer>
<section class="slider-home">
    <div class="swiper swiperHome">
        <div class="swiper-wrapper">
            <?php if(get_sub_field('items')): ?>
              <?php while(the_repeater_field('items')):
                    $linkSH = get_sub_field('link');
                    $link_urlSH = $linkSH['url'];
                    $link_targetSH = $linkSH['target'] ? $linkSH['target'] : '_self';
                    $imageSlide = '';
                    if ( wp_is_mobile() ) {
                        $imageSlide = get_sub_field('imagen_responsive');
                    } else {
                        $imageSlide = get_sub_field('imagen');
                    }
              ?>
                <div class="swiper-slide" style="background-image: url(<?php echo $imageSlide; ?>)">
                    <?php if(get_sub_field('link')): ?>
                    <a onclick="pushEventGTM(this, '<?php echo $link_urlSH; ?>', '<?php echo $link_targetSH; ?>', 'Slider home -')" class="ntag1" data-title="<?php echo get_sub_field('titulo'); ?>">  
                        <div class="max-content slider-home__contenido">
                            <h2 data-wow-duration="2s" class="wow bounceInDown"><?php echo get_sub_field('titulo'); ?></h2>
                            <p data-wow-duration="2s" class="wow bounceInUp"><?php echo get_sub_field('descripcion'); ?></p>
                        </div>
                    </a>
                    <?php else: ?>
                    <div class="max-content slider-home__contenido">
                        <h2 data-wow-duration="2s" class="wow bounceInDown"><?php echo get_sub_field('titulo'); ?></h2>
                        <p data-wow-duration="2s" class="wow bounceInUp"><?php echo get_sub_field('descripcion'); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
              <?php endwhile; ?>
            <?php endif; ?>
        </div>
        <div class="max-content slider-home__flechas">
            <div class="content__flechas">
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>
    </div>
</section>
<?php $buildSliderHomeJs = esc_url( get_template_directory_uri() . '/js/legos/slider-home.js' ); ?>
<script src="<?php echo $buildSliderHomeJs; ?>"></script>