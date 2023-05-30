<?php
    $imageBB = '';
    if ( wp_is_mobile() ) {
        $imageBB = get_sub_field('banner_responsive');
    } else {
        $imageBB = get_sub_field('banner');
    }
?>
<?php $buildBannerConBreancrumbCss = esc_url( get_template_directory_uri() . '/css/legos/banner_con_breadcrumb.css' ); ?>
<link rel="stylesheet" href="<?php echo $buildBannerConBreancrumbCss; ?>" async defer>
<section class="banner_con_breadcrumb main-slider-at-top aflora-pge position-relative after-po">
    <?php
    $classTituloBreadcrumb = "";
    if(!get_sub_field('banner')):
        $classTituloBreadcrumb = "banner_con_breadcrumb__links--small";
    endif; ?>
    <div class="banner_con_breadcrumb__links <?php echo $classTituloBreadcrumb; ?>">
    <?php echo '<a href='.get_site_url().'>Inicio</a> '; ?>
    /
     <a href="<?php echo get_permalink( $post->post_parent ); ?>" >
        <?php echo get_the_title( $post->post_parent ); ?>
     </a>
     /
    <span><?php echo the_title(); ?></span>
    <?php if(get_sub_field('banner')): ?>
    <p>
        <svg version="1.2" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" overflow="visible" preserveAspectRatio="none" viewBox="0 0 24 24" width="54" height="49"><g><path xmlns:default="http://www.w3.org/2000/svg" d="M15.41 16.09l-4.58-4.59 4.58-4.59L14 5.5l-6 6 6 6z" style="fill: rgb(247, 247, 247);" vector-effect="non-scaling-stroke"/></g></svg>
        <a href="<?php echo get_permalink( $post->post_parent ); ?>" >Volver</a>
    </p>
    <?php else : ?>
    <p>
        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30">
          <g id="Group_1" data-name="Group 1" transform="translate(-67 -281)">
            <g id="Rectangle_1" data-name="Rectangle 1" transform="translate(67 281)" fill="#fff" stroke="#707070" stroke-width="1" opacity="0">
              <rect width="30" height="30" stroke="none"/>
              <rect x="0.5" y="0.5" width="29" height="29" fill="none"/>
            </g>
            <g id="back_acon" transform="translate(77 288)">
              <path id="Path_1" data-name="Path 1" d="M18,19.792,11.819,13.6,18,7.4,16.1,5.5,8,13.6l8.1,8.1Z" transform="translate(-8 -5.5)" fill="#474747"/>
            </g>
          </g>
        </svg>
        <a class="breadcrump_gris" href="<?php echo get_permalink( $post->post_parent ); ?>" >Volver</a>
    </p>
    <h1 data-wow-duration="2s" class="ff-sans-b wow bounceInDown"><?php echo get_sub_field('titulo'); ?></h1>
     <?php endif; ?>
    </div>
    <?php if(get_sub_field('banner')): ?>
      <div class="img-prt position-relative">
          <img src="<?php echo $imageBB; ?>"
               alt="banner programa"
               class="object-fit-cover"></div>
      <div class="pg-tit-inner position-absolute text-center">
        <h1 data-wow-duration="2s" class="ff-sans-b wow bounceInDown"><?php echo get_sub_field('titulo'); ?></h1>
      </div>
  <?php endif; ?>
</section>
