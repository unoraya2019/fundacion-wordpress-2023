<?php
get_header();
$eventoCss = esc_url(get_template_directory_uri() . '/css/evento.css');

$fecha_string =  get_field('fecha');
$link_del_evento = get_field('link_del_evento');
$fecha_array = explode("/", $fecha_string);


$meses = array(
  "Ene" => "1",
  "Feb" => "2",
  "Mar" => "3",
  "Abr" => "4",
  "May" => "5",
  "Jun" => "6",
  "Jul" => "7",
  "Ago" => "8",
  "Sep" => "9",
  "Oct" => "10",
  "Nov" => "11",
  "Dic" => "12",
);

$fecha_array[1] = $meses[$fecha_array[1]];

$fecha = date_create(join("-", $fecha_array) . "");
$format = date_format($fecha, "d-m-Y 00:00:00");
$today = new DateTime('today');
?>
<link rel="stylesheet" href="<?php echo $eventoCss; ?>" async defer>
<main id="content" role="main">
  <section class="
  main-slider-at-top
  actualidad-pg
  evento-details
  after-po
  position-relative
">
    <div class="
    back-to-home
    position-absolute
    d-flex
    align-items-center
    position-relative-767
  "><em class="ic-back"></em>
      <a href="<?php echo get_site_url(); ?>/eventos" class="ff-sans-b text-white">Volver</a>
    </div>
    <div class="img-prt alto-mb position-relative">
      <img src="<?php echo get_field('banner'); ?>" alt="" class="object-fit-cover">
      <div class="container position-absolute set-to-bottom d-none-767">
        <h1 class="text-white ff-sans-b">
          <?php echo get_the_title(); ?>
        </h1>
      </div>
    </div>
  </section>
  <section class="wht-abt-ent">
    <div class="container">
      <div class="in-container">
        <div class="d-flex flex-wrap justify-content-between">
          <div class="ifo-abt-evnt">
            <h2 class="ff-sans-b">¿De qué trata el evento?</h2>
            <div class="para-p ff-nunito">
              <?php echo get_field('¿de_que_trata_el_evento'); ?>
            </div>
          </div>
          <div class="evnt-dta ff-nunito">
            <div class="d-flex">
              <div class="lbl-evnt evnt-finalized">
                <?php if ($fecha > $today) : ?>
                  Evento activo
                <?php
                else : ?>
                  Evento inactivo
                <?php endif; ?>
              </div>
            </div>
            <div class="iner-evt-dta">
              <h3>Datos del evento</h3>
              <ul>
                <li class="d-flex align-items-center">
                  <div class="lbl"><strong>Fecha:</strong></div>
                  <div class="i-txt"><?php echo get_field('fecha'); ?></div>
                </li>
                <li class="d-flex align-items-center">
                  <div class="lbl"><strong>Hora:</strong></div>
                  <div class="i-txt"><?php echo get_field('hora'); ?></div>
                </li>
                <li class="d-flex align-items-center">
                  <div class="lbl"><strong>Dirección:</strong></div>
                  <div class="i-txt"><?php echo get_field('lugar'); ?></div>
                </li>
                <li class="d-flex align-items-center">
                  <div class="i-txt"></div>
                </li>
                <li class="d-flex align-items-center">
                  <div class="i-txt"></div>
                </li>
              </ul>
              <div class="btn-gp">

                <?php
                if ($fecha < $today) : ?>
                  <span>Evento finalizado</span>
                <?php
                elseif ($link_del_evento) : ?>
                  <span onclick="pushEventGTM(this, '<?php echo get_field('link_del_evento'); ?>', '_self', 'h4', '<?php echo get_the_title(); ?>')" target="_blank"                        class="btn-attend text-center">Inscríbase aquí</span>

                <?php
                else : ?>
                  <span>Entrada Libre</span>
                <?php endif; ?>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <section class="max-content eventos_others">
    <h3 class="ff-sans-b">Otros eventos interesantes</h3>
    <div class="swiper mySwiper">
      <div class="swiper-wrapper">
        <?php
        $related = get_posts(array(
          'category__in' => wp_get_post_categories($post->ID),
          'numberposts' => 4,
          'post_type' => 'eventos',
          'post__not_in' => array($post->ID)
        ));
        if ($related) foreach ($related as $post) {
          setup_postdata($post); ?>
          <div class="swiper-slide">
            <div class="slid-img-bx">
              <a href="<?php the_permalink(); ?>">
                <img src="<?php echo get_field('banner'); ?>" alt="<?php echo get_the_title(); ?>" class="object-fit-cover"></a>
            </div>
            <div class="blg-txt-info">
              <a href="<?php the_permalink(); ?>">
                <h4 class="ff-sans-b"><?php echo get_the_title(); ?></h4>
              </a>
              <div class="dates ff-sans-i"><?php echo get_the_date('l F j, Y'); ?></div>
              <?php echo get_field('descripcion_del_evento'); ?>
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
<?php $buildActualidadJs = esc_url(get_template_directory_uri() . '/js/actualidad.js'); ?>
<script src="<?php echo $buildActualidadJs; ?>"></script>