<?php $buildPerfilesCss = esc_url( get_template_directory_uri() . '/css/legos/perfiles.css' ); ?>
<link rel="stylesheet" href="<?php echo $buildPerfilesCss; ?>" async defer>
<section class="perfiles">
    <div class="max-content">
        <?php if(get_sub_field('subtitulo')): ?>
            <h3><?php echo get_sub_field('subtitulo'); ?></h3>
        <?php endif; ?>
        <h2><?php echo get_sub_field('titulo'); ?></h2>
        <?php if(get_sub_field('descripcion')): ?>
            <p><?php echo get_sub_field('descripcion'); ?></p>
        <?php endif; ?>
        <div class="flex perfiles__content">
            <?php if(get_sub_field('items')): ?>
              <?php while(the_repeater_field('items')):
                $imagePF = get_sub_field('imagen');
                $urlPF = $imagePF['url'];
                $altPF = $imagePF['alt'];
                $linkPF = get_sub_field('url');
                $link_urlPF = $linkPF['url'];
                $link_titlePF = $linkPF['title'];
                $link_targetPF = $linkPF['target'] ? $linkPF['target'] : '_self';
              ?>
                <?php if(get_sub_field('contenido')): ?>
                <div class="perfiles__item">
                    
                    <h3><?php echo get_sub_field('titulo'); ?></h3>
                    <img class="img-fluid"
                         src="<?php echo $imagePF; ?>"
                         alt="<?php echo get_sub_field('titulo'); ?>">
                    <p><?php echo get_sub_field('contenido'); ?></p>
                    <?php if(get_sub_field('url')): ?>
                    <a class="btn btn-primary ntag53" target="_blank" data-title="<?php echo get_sub_field('titulo'); ?> - <?php echo esc_html( $link_titlePF ); ?>"
                        onclick="pushEventGTM(this, '<?php echo esc_url( $link_urlPF ); ?>', '<?php echo esc_attr( $link_targetPF ); ?>')"><?php echo esc_html( $link_titlePF ); ?></a>
                    <?php endif; ?>
                </div>
                <?php else:  ?>
                <div class="perfiles__item perfiles__item__small">
                    <h3><?php echo get_sub_field('titulo'); ?></h3>
                    <img src="<?php echo $imagePF; ?>"
                         alt="<?php echo get_sub_field('titulo'); ?>">
                    <?php if(get_sub_field('url')): ?>
                    <a class="btn btn-primary tag ntag30" data-title="<?php echo get_sub_field('titulo'); ?>"
                        onclick="pushEventGTM(this, '<?php echo esc_url( $link_urlPF ); ?>', '<?php echo esc_attr( $link_targetPF ); ?>')"><?php echo esc_html( $link_titlePF ); ?></a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
              <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
