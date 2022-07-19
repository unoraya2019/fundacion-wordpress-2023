<?php get_header(); ?>
<?php $actualidadCss = esc_url( get_template_directory_uri() . '/css/actualidad.css' ); ?>
<link rel="stylesheet" href="<?php echo $actualidadCss; ?>" async defer>
<main id="content" role="main">
    <section class="max-content actualidad_content">
        <div class="breadcrumb_actualidad">
            <?php echo '<a href="/">Inicio</a>'; ?> /
             <a href="<?php echo get_site_url(); ?>/actualidad">Actualidad</a> /
            <?php echo the_title(); ?>
        </div>
        <div class="listing-here">
          <div class="one-blg">
            <h1 class="ff-sans-b line-he-1">
              <?php echo the_title(); ?>
            </h1>
            <div class="img-bx">
                <img src="<?php echo get_field('banner'); ?>"
                    alt="<?php echo the_title(); ?>"
                    class="object-fit-cover"></div>
            <div class="publish-date">
              <div class="d-flex align-items-center ff-sans-r">
                <p><?php echo get_the_date( 'l F j, Y' ); ?></p>
              </div>
            </div>
          </div>
          <div class="one-blg">
             <?php echo get_field('content'); ?>
          </div>
        </div>
    </section>
    <section class="max-content actualidad_others">
        <h3>Otros artículos interesantes</h3>
        <div class="swiper mySwiper">
            <div class="swiper-wrapper">
           <?php
              $related = get_posts( array( 'category__in' => wp_get_post_categories($post->ID),
                                            'post_type' => 'actualidad',
                                            'numberposts' => 4,
                                            'post__not_in' => array($post->ID) ) );
              if( $related ) foreach( $related as $post ) {
              setup_postdata($post); ?>
                <div class="swiper-slide">
                  <div class="slid-img-bx">
                    <a href="<?php the_permalink(); ?>">
                      <img src="<?php echo get_field('banner'); ?>"
                        alt="<?php echo get_the_title(); ?>"
                        class="object-fit-cover"></a>
                    </div>
                    <div class="tags">
                        <a href="javascript:void(0)">Categoría</a>
                    </div>
                  <div class="blg-txt-info">
                    <a href="<?php the_permalink(); ?>">
                      <h4 class="ff-sans-b"><?php echo get_the_title(); ?></h4>
                    </a>
                    <div class="dates ff-sans-i"><?php echo get_the_date( 'l F j, Y' ); ?></div>
                    <p class="ff-sans-r"><?php echo get_field('breve_descripcion'); ?></p>
                  </div>
                </div>
                  <?php }
            wp_reset_postdata(); ?>
          </div>
          <div class="swiper-pagination"></div>
        </div>
    </section>
</main>
<?php $actualidadJs = esc_url( get_template_directory_uri() . '/js/actualidad.js' ); ?>
<script src="<?php echo $actualidadJs; ?>"></script>
<?php get_footer(); ?>