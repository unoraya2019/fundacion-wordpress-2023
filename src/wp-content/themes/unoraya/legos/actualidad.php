<?php $buildActualidadCss = esc_url( get_template_directory_uri() . '/css/legos/actualidad.css' ); ?>
<link rel="stylesheet" href="<?php echo $buildActualidadCss; ?>" async defer>
<section class="max-content actualidad">
    <h2>Actualidad Fundación Bolivar Davivienda</h2>
    <div class="flex actualidad__wrapper">
        <div class="flex actualidad__items">
        <?php
            $args = array( 'post_type' => 'actualidad',
                            'posts_per_page' => 3,
                            'order' => 'DESC' );
            $loop = new WP_Query( $args );
        ?><!-- cierra -->
        <?php if ( $loop->have_posts() ):
            $i = 0;
            while ( $loop->have_posts() ): 
              setup_postdata( have_posts() );
              $loop->the_post();
                $category = get_the_category(get_the_ID());
                $categoryName = $category[0]->name;
        ?>
            <div class="flex actualidad__item"
                 id="actualidad<?php echo $i; ?>"
                 onclick="changeActualy('<?php echo get_the_date( 'l F j, Y' ); ?>',
                                        '#<?php echo $categoryName; ?>',
                                        '<?php echo get_the_title(); ?>',
                                        '<?php echo get_field('banner'); ?>',
                                        'actualidad<?php echo $i; ?>',
                                        '<?php the_permalink(); ?>')">
                <img src="<?php echo get_field('banner'); ?>"
                    width="200px">
                <div class="actualidad__item__text">
                    <h3><?php echo get_the_title(); ?></h3>
                    <p><?php echo get_field('breve_descripcion'); ?></p>
                    <a class="flex btn btn_evento" href="<?php the_permalink(); ?>">
                        <span>Ver noticia</span>
                        <img src="<?php echo esc_url( get_template_directory_uri() . '/img/flecha_blanca.svg' ); ?>">
                    </a>
                </div>
            </div>
            <?php
                $i = $i + 1;
            endwhile; wp_reset_postdata();?>
        <?php endif; ?>
        </div>
        <div class="actualidad__content">
            <img id="actualidadImage"
                 src=""
                 class="object-fit-cover">
            <div class="flex">
                <a id="actualidadLink" href="javascript:void(0)"></a>
            </div>
            <div id="actualidadDate" class="date"></div>
            <h3 id="actualidadTitle"></h3>
            <a class="flex btn btn_evento"
                id="actualidadUrl"
                data-link=""
                onclick="sendActionGTM(this)">
                <span>Ver noticia</span>
                <img src="<?php echo esc_url( get_template_directory_uri() . '/img/flecha_blanca.svg' ); ?>">
            </a>
        </div>
    </div>
</section>
<?php $buildActualidadJs = esc_url( get_template_directory_uri() . '/js/legos/actualidad.js' ); ?>
<script src="<?php echo $buildActualidadJs; ?>"></script>