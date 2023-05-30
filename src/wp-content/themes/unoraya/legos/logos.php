<?php $buildLogosCss = esc_url( get_template_directory_uri() . '/css/legos/logos.css' ); ?>
<link rel="stylesheet" href="<?php echo $buildLogosCss; ?>" async defer>
<section class="wid-support-of bg-white logos">
  <div class="container">
    <h4 class="ff-sans-b"><?php echo get_sub_field('titulo'); ?></h4>
    <div class="d-flex flex-wrap for-marg-1">
        <?php if(get_sub_field('logosC')): ?>
          <?php while(the_repeater_field('logosC')): ?>
            <?php
                $imageLG = get_sub_field('imagen');
            ?>
            <div class="lgoes-bx d-flex align-items-center position-relative">
              <img src="<?php echo $imageLG; ?>"
                   alt="logo">
            </div>
          <?php endwhile; ?>
        <?php endif; ?>
    </div>
  </div>
</section>
