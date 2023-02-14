<?php $buildLinkCss = esc_url( get_template_directory_uri() . '/css/legos/links-acceso.css' ); ?>
<link rel="stylesheet" href="<?php echo $buildLinkCss; ?>" async defer>
<section class="links_acceso bg-white only-links-to">
    <div class="max-content">
        <?php if(get_sub_field('titulo')): ?>
        <h3><?php echo get_sub_field('titulo'); ?></h3>
        <hr>
        <?php endif; ?>
        <div class="d-flex flex-wrap for-mg justify-content-center justify-content-start-767">
            <?php if(get_sub_field('links')): ?>
              <?php while(the_repeater_field('links')): ?>
                <?php
                    $linkLA = get_sub_field('link');
                    $link_urlLA = $linkLA['url'];
                    $link_titleLA = $linkLA['title'];
                    $link_targetLA = $linkLA['target'] ? $linkLA['target'] : '_self';
                ?>
                <a class="linkse d-flex justify-content-center align-items-center text-center ntag18"
                    onclick="pushEventGTM(this, '<?php echo esc_url( $link_urlLA ); ?>', '<?php echo esc_attr( $link_targetLA ); ?>', 'NO')"><h2><?php echo esc_html( $link_titleLA ); ?></h2></a>
                <?php echo get_sub_field('descripcionSc2'); ?>
              <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
</section>