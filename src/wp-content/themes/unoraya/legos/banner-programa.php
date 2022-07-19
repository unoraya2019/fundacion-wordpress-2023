<?php
    $imageBPr = get_sub_field('banner');
    $urlBPr = $imageBPr['url'];
    $altBPr = $imageBPr['alt'];
    
    $imageBPrRes = get_sub_field('banner_responsive');
    $urlBPrRes = $imageBPrRes['url'];
    $altBPrRes = $imageBPrRes['alt'];
    
    $imageBPrLogo = get_sub_field('logo');
    $urlBPrLogo = $imageBPrLogo['url'];
    $altBPrLogo = $imageBPrLogo['alt'];
?>
<?php $buildBannerProgramaCss = esc_url( get_template_directory_uri() . '/css/legos/banner-programa.css' ); ?>
<link rel="stylesheet" href="<?php echo $buildBannerProgramaCss; ?>" async defer>
<section class="banner-programa aflora-bnr-se position-relative after-po">
    <div class="img-prt position-absolute">
        <img src="<?php echo esc_url($urlBPr); ?>"
             alt="<?php echo esc_attr($altBPr); ?>"
             class="object-fit-cover">
    </div>
    <div class="img-prt d-none-n d-block-767 position-absolute">
      <img src="<?php echo esc_url($urlBPrRes); ?>"
           alt="<?php echo esc_attr($altBPrRes); ?>"
           class="object-fit-cover">
    </div>
    <div class="d-flex align-items-center some-txt-logo position-relative">
    <div>
        <div data-wow-duration="2s" class="log-prt wow bounceInDown">
            <img src="<?php echo esc_url($urlBPrLogo); ?>"
               alt="<?php echo esc_attr($altBPrLogo); ?>"
               class="img-fluid">
        </div>
      <div data-wow-duration="2s" class="wow bounceInUp">
          <h1><?php echo get_sub_field('titulo'); ?></h1>
        <p><?php echo get_sub_field('descripcion'); ?></p>
      </div>
    </div>
    </div>
</section>