<?php
/**
 * Template Name: Actualidad
*/
	get_header();
?>
<main>
    <section class="main-slider-at-top actualidad-pg">
      <div class="img-prt position-relative">
		  <?php
            $urlImageBanner = get_site_url()."/wp-content/uploads/2022/06/encabezado_actualidad.jpg";
            if ( wp_is_mobile() ) {
                $urlImageBanner = get_site_url()."/wp-content/uploads/2022/07/actualidad_encabezado_op2.jpeg";
            }
          ?>
          <img src="<?php echo $urlImageBanner; ?>"
                alt="Actualidad Fundación Bolívar Davivienda"
                class="object-fit-cover">
        <div class="container position-absolute set-to-bottom">
          <div class="banner-txt text-white">
            <div data-wow-duration="2s" class="orange-bg wow bounceInDown">
              <h1 class="ff-sans-b">Actualidad Fundación Bolívar Davivienda</h1>
            </div>
            <div data-wow-duration="2s" class="black-bg wow bounceInUp">
              <p>
                Aquí vas a poder aprender acerca del desarrollo de nuestros
                programas y sus novedades
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>
    <section class="what-looking-for">
      <div class="container">
        <div class="innper-bx margin-0-auto">
          <div class="d-flex align-items-center justify-content-between flex-wrap-767">
            <p class="ff-nunito text-white">¿Qué estas buscando?</p>
            <input id="myInput"
                   onkeyup="filterTextInput()"
                   type="text"
                   placeholder="Actualidad"
                   class="form-control">
          </div>
        </div>
      </div>
    </section>
<section class="what-looking-for">
<?php
    $args = array('post_type' => 'actualidad', 'posts_per_page' => 20,'order' => 'DESC' );
    $loop = new WP_Query( $args );
?><!-- cierra -->
<?php if ( $loop->have_posts() ): ?>
        <!-- Repeater    -->
        <?php while ( $loop->have_posts() ): $loop->the_post(); ?>
        <div class="main-slider-at-top actualidad-pg "
            data-content="<?php echo get_the_title(); ?>">
          <div class="img-prt position-relative">
            <img src="<?php echo get_field('banner'); ?>"
                 alt="<?php echo get_the_title(); ?>" 
                 class="object-fit-cover">
            <div class="container position-absolute set-to-bottom "  > 
              <div class="banner-txt text-white">
                <div class="line-he-1">
                    <span class="date-p ff-nunito text-white"><?php echo get_the_date( 'l F j, Y' ); ?></span>
                </div>
                <div class="orange-bg">
                  <h2 class="ff-sans-b"><?php echo get_the_title(); ?></h2>
                </div>
                <div class="black-bg">
                  <p>
                    <?php echo get_field('breve_descripcion'); ?>
                  </p>
                </div>
              </div>
            </div> <a href="<?php the_permalink(); ?>" class="position-absolute full-box-link ntag59" data-title="<?php echo get_the_title(); ?>"></a>
          </div>
        </div>
        <?php endwhile; ?>
<?php endif; ?>
</section>
</main>
<?php
	get_footer();
?>
<?php $buildActualidadJs = esc_url( get_template_directory_uri() . '/js/actualidad.js' ); ?>
<script src="<?php echo $buildActualidadJs; ?>"></script>