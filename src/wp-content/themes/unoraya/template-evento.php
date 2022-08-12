<?php
/**
 * Template Name: Evento
*/
	get_header();
	$eventoCss = esc_url( get_template_directory_uri() . '/css/evento.css' );
?>
<link rel="stylesheet" href="<?php echo $eventoCss; ?>" async defer>
<main>
    <section class="main-slider-at-top actualidad-pg">
      <div class="img-prt position-relative">
          <?php
            $urlImageBanner = get_site_url()."/wp-content/uploads/2022/05/encabezado-2.jpg";
            if ( wp_is_mobile() ) {
                $urlImageBanner = get_site_url()."/wp-content/uploads/2022/05/encabezados_eventos.jpg";
            }
          ?>
          <img src="<?php echo $urlImageBanner; ?>" alt="Actualidad Fundación Bolívar Davivienda"
                class="object-fit-cover">
        <div class="container position-absolute set-to-bottom">
          <div class="banner-txt text-white">
            <div data-wow-duration="2s" class="orange-bg wow bounceInDown">
              <h1 class="ff-sans-b">Eventos</h1>
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
<section class="only-boxes-section">
  <div class="max-content">
    <div class="d-flex flex-wrap for-mar-setmet">
<?php
    $args = array('post_type' => 'eventos', 'posts_per_page' => 20,'order' => 'DESC' );
    $loop = new WP_Query( $args );
?><!-- cierra -->
<?php if ( $loop->have_posts() ): ?>
        <!-- Repeater    -->
        <?php while ( $loop->have_posts() ): $loop->the_post(); ?>
        <div class="only-one-bx actualidad-pg" data-content="<?php echo get_the_title(); ?>">
            <h2 class="ff-sans-b text-uppercase"><?php echo get_the_title(); ?></h2>
            <div class="im-bxe">
                <img src="<?php echo get_field('banner'); ?>"
                     alt="<?php echo get_the_title(); ?>" 
                     class="object-fit-cover"></div>
            <div class="dt-tx-info position-relative after-po ff-nunito">
              <div class="d-flex">
                <div class="dat-bx position-relative after-po">
                  <p><?php echo get_field('fecha'); ?></p>
                  <p><?php echo get_field('hora'); ?></p>
                </div>
                <div class="bx-nam">
                  <div class="d-flex justify-content-between">
                    <div class="lft">
                      <h4><b>Lugar del evento:</b> <?php echo get_field('lugar'); ?></h4>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <br>
            <a onclick="pushEventGTM(this, '<?php the_permalink(); ?>', '_self')"
                class="btn-ver-evento ff-nunito position-relative">
                Ver Evento</a>
        </div>
        <?php endwhile; ?>
<?php endif; ?>
    </div>
  </div>
</section>
</main>
<?php
	get_footer();
?>
<?php $buildActualidadJs = esc_url( get_template_directory_uri() . '/js/actualidad.js' ); ?>
<script src="<?php echo $buildActualidadJs; ?>"></script>