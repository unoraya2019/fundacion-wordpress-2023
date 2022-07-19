<?php $buildSliderEventosCss = esc_url( get_template_directory_uri() . '/css/legos/slider_eventos.css' ); ?>
<link rel="stylesheet" href="<?php echo $buildSliderEventosCss; ?>" async defer>
<section class="max-content slider_eventos">
    <div class="swiper swiperEventos">
        <div class="swiper-wrapper">
            <?php
                $args = array( 'post_type' => 'eventos',
                                'posts_per_page' => 4,
                                'order' => 'DSC' );
                $loop = new WP_Query( $args );
            ?>
              <?php if ( $loop->have_posts() ): ?>
                <?php while ( $loop->have_posts() ):
                  setup_postdata( have_posts() );
                  $loop->the_post();
                if ( wp_is_mobile() ) {
                    $imageBannerSlider = get_field('banner_responsive');
                } else {
                    $imageBannerSlider = get_field('banner');
                }
            ?>
                <div class="swiper-slide" style="background-image: url(<?php echo $imageBannerSlider; ?>)">
                    <div class="slider-eventos__contenido">
                    <h3>Próximos Eventos</h3>
                    <a onclick="pushEventGTM(this, '<?php the_permalink(); ?>', '_self', 'h4')">
                        <div class="slider-eventos__texto">
                            <h4><?php echo get_the_title(); ?></h4>
                            <?php echo get_field('descripcion_del_evento'); ?>
                            <p>Lugar: <?php echo get_field('lugar'); ?></p>
                            <p>Fecha: <?php echo get_field('fecha'); ?></p>
                            <p>Hora: <?php echo get_field('hora'); ?></p>
                        </div>
                    </a>
                    <a class="flex btn btn_evento" onclick="pushEventGTM(this, '<?php the_permalink(); ?>', '_self', 'h4')">
                        <span>Ver evento</span>
                        <img src="<?php echo esc_url( get_template_directory_uri() . '/img/flecha_blanca.svg' ); ?>">
                    </a>
                    </div>
                </div>
                <?php endwhile; wp_reset_postdata();?>
            <?php endif; ?>
        </div>
        <div class="slider-eventos__flechas">
            <div class="content__flechas">
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>
    </div>
</section>
<?php $buildSliderEventosJs = esc_url( get_template_directory_uri() . '/js/legos/slider_eventos.js' ); ?>
<script src="<?php echo $buildSliderEventosJs; ?>"></script>