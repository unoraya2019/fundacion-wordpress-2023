<?php
    $imageBPr = get_sub_field('banner');
    $imageBPrRes = get_sub_field('banner_responsive');    
    $imageBPrLogo = get_sub_field('logo');
?>
<?php $buildBannerProgramaCss = esc_url( get_template_directory_uri() . '/css/legos/banner-programa.css' ); ?>
<link rel="stylesheet" href="<?php echo $buildBannerProgramaCss; ?>" async defer>
<section class="banner-programa aflora-bnr-se position-relative after-po">
    <div class="img-prt position-absolute">
        <img src="<?php echo esc_url($imageBPr); ?>"
             alt="banner programa"
             class="object-fit-cover">
    </div>
    <div class="img-prt d-none-n d-block-767 position-absolute">
      <img src="<?php echo esc_url($imageBPrRes); ?>"
           alt="banner programa versión celular"
           class="object-fit-cover">
    </div>
    <div class="d-flex align-items-center some-txt-logo position-relative">
    <div>
        <div data-wow-duration="2s" class="log-prt wow bounceInDown">
            <img src="<?php echo esc_url($imageBPrLogo); ?>"
               alt="logo de programa"
               class="img-fluid">
        </div>
      <div data-wow-duration="2s" class="wow bounceInUp">
          <h1><?php echo get_sub_field('titulo'); ?></h1>
        <p><?php echo get_sub_field('descripcion'); ?></p>
      </div>
    </div>
    </div>
</section>
