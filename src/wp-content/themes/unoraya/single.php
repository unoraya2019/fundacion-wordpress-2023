<?php get_header(); ?>
<style>
    .actualidad_others .position-relative {
        max-width: 260px;
        margin: 0px 20px;
    }
    .actualidad_others h4 {
        font-size: 14px;
        font-weight: bold;
        margin-top: 5px;
    }
    .actualidad_others p {
        font-size: 14px;
    }
    .actualidad_others .dates.ff-sans-i {
        font-style: italic;
        font-size: 14px;
        margin-bottom: 10px;
    }
    .actualidad_content h3 {
        font-size: 38px;
        margin-bottom: 30px;
    }
    .actualidad_others h3 {
        font-size: 38px;
        margin: 0 0 30px;
    }
    .tags a {
        color: #ff671b;
        font-size: 14px;
        margin-top: 10px;
        display: block;
    }
</style>
<main id="content" role="main">
    <section class="max-content actualidad_content">
        <div class="breadcrumb_actualidad">
            <?php echo '<a href="/">Inicio</a> / '; ?>
             <a href="<?php echo get_site_url(); ?>/actualidad">Actualidad / </a>
            <?php echo the_title(); ?>
        </div>
        <div class="listing-here">
          <div class="one-blg">
            <h2 class="ff-sans-b line-he-1">
              <?php echo the_title(); ?>
            </h2>
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
              $related = get_posts( array( 'category__in' => wp_get_post_categories($post->ID), 'numberposts' => 20, 'post__not_in' => array($post->ID) ) );
              if( $related ) foreach( $related as $post ) {
              setup_postdata($post); ?>
                <div class="position-relative">
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
<?php get_footer(); ?>
<script>
  var swiper = new Swiper(".mySwiper", {
    slidesPerView: 3,
    spaceBetween: 30,
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
  });
</script>