<?php
    $imageBD = get_sub_field('imagen');
    $urlBD = $imageBD['url'];
    $altBD = $imageBD['alt'];
    $linkBD = get_sub_field('link');
    $link_urlBD = $linkBD['url'];
    $link_titleBD = $linkBD['title'];
    $link_targetBD = $linkBD['target'] ? $linkBD['target'] : '_self';
?>
<?php $buildBannerDestacadoCss = esc_url( get_template_directory_uri() . '/css/legos/banner_destacado.css' ); ?>
<link rel="stylesheet" href="<?php echo $buildBannerDestacadoCss; ?>" async defer>
<section class="banner_destacado how-we-connect bg-white">
    <div class="max-content">
        <div class="d-flex justify-content-between align-items-center flex-wrap-991">
            <div data-wow-duration="1s" class="left-solid-color d-flex align-items-center wow slideInLeft">
                <div data-wow-delay="1s" class="inner-tx-w text-center wow bounceInUp">
                    <h3>
                        <?php echo get_sub_field('titulo'); ?>
                    </h3>
                    <a class="btn-tell-more ff-sans-b"
                       onclick="pushEventGTM(this, '<?php echo $link_urlBD; ?>', '<?php echo $link_targetBD; ?>')"><?php echo esc_html( $link_titleBD ); ?></a>
                </div>
            </div>
            <div data-wow-duration="1s" class="right-img position-relative wow slideInRight">
                <img src="<?php echo esc_url($urlBD); ?>"
                     alt="<?php echo esc_attr($altBD); ?>"
                     class="object-fit-cover"/>
            </div>
        </div>
    </div>
</section>