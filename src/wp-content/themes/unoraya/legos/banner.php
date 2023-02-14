<?php
    $buildBannerCss = 'css/banner.min.css';
    $styleBanner = '.banner{background:url('.get_site_url().'/wp-content/uploads/2022/04/desarrollamos_shape-w.svg) 0 0 no-repeat;background-position:50%;background-size:cover}.desarrollamos-section:after,.desarrollamos-section:before{content:"";background-image:url('.get_site_url().'/wp-content/uploads/2022/04/puntos_fbd.svg);position:absolute;display:inline-block;background-repeat:no-repeat;background-size:cover}.desarrollamos-section:after{width:280px;height:170px;top:0;left:0}.desarrollamos-section:before{width:280px;height:170px;bottom:0;right:-105px}@media (max-width:600px){.desarrollamos-section:after,.desarrollamos-section:before{width:130px;height:90px}}';
    createRemoteCss($buildBannerCss, $styleBanner);
?>
<link rel="stylesheet" href="<?php echo get_site_url().'/'.$buildBannerCss; ?>" async defer>
<section class="banner desarrollamos-section position-relative before-po">
    <div class="inner-section margin-0-auto position-relative z-index-5">
        <div class="text-center text-white"><small class="ff-nunito"><?php echo get_sub_field('subtitulo'); ?></small>
            <h2 class="ff-sans-b"><?php echo get_sub_field('titulo'); ?></h2>
            <p class="ff-nunito margin-0-auto">
               <?php echo get_sub_field('descripcion'); ?>
            </p>
            <br>
            <?php
                $link = get_sub_field('link');
                $link_url = $link['url'];
                $link_title = $link['title'];
                $link_target = $link['target'] ? $link['target'] : '_self';
            ?>
            <?php if(get_sub_field('link')): ?>
            <a class="conozca-mas-home-1 btn-tell-more send_dataGTM ntag4"
                data-title="<?php echo get_sub_field('titulo'); ?>"
                data-url="<?php echo $link_url; ?>"
                data-target="<?php echo $link_target; ?>"><?php echo esc_html( $link_title ); ?></a>
            <?php endif; ?>
        </div>
    </div>
</section>