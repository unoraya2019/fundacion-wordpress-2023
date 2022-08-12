<?php
    $buildTestimoniosCss = 'css/testimonios.min.css';
    $styleTestimonios = '.testimo-slider .quote-sign:after,.testimo-slider .quote-sign:before{background:url('.get_site_url().'/wp-content/uploads/2022/04/ic_quote.svg) 0 0 no-repeat}';
    createRemoteCss($buildTestimoniosCss, $styleTestimonios);
?>
<link rel="stylesheet" href="<?php echo get_site_url().'/'.$buildTestimoniosCss; ?>" async defer>
<section class="testimonios testimonios-sec">
  <div class="container-fluid">
    <div class="sec-title text-center">
      <h2 class="ff-nunito">Testimonios</h2>
      <p class="ff-sans-b">De nuestros integrantes</p>
    </div>
    <div class="testimo-slider">
      <div class="for-marg-1">
        <div class="swiper mySwiper">
          <div class="swiper-wrapper">
            <?php if(get_sub_field('testimoniosC')): ?>
              <?php while(the_repeater_field('testimoniosC')): ?>
                <div class="swiper-slide">
                    <div class="position-relative testimo-bx bg-white">
                      <div class="text-center til ff-sans-b">
                        <h3><?php echo get_sub_field('titulo'); ?></h3>
                      </div>
                      <div class="txt margin-0-auto position-relative after-po before-po quote-sign ff-sans-i">
                        <p><?php echo get_sub_field('mensaje'); ?></p>
                      </div>
                    </div>
                </div>
              <?php endwhile; ?>
            <?php endif; ?>
          </div>
          <div class="swiper-pagination"></div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php $buildTestimoniosJs = esc_url( get_template_directory_uri() . '/js/legos/testimonios.js' ); ?>
<script src="<?php echo $buildTestimoniosJs; ?>"></script>