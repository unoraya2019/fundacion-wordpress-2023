<?php
    $linkBazul = get_sub_field('url');
    $link_urlBazul = $linkBazul['url'];
    $link_titleBazul = $linkBazul['title'];
    $link_targetBazul = $linkBazul['target'] ? $linkBazul['target'] : '_self';
?>
<section class="banner_azul ig-tx-zing-zag autodiag-pg desarrollamos-section">
    <div class="container">
        <div class="d-flex justify-content-center">
            <div class="align-self-center calls">
                <h2><?php echo get_sub_field('titulo'); ?></h2>
                <p><?php echo get_sub_field('descripcion'); ?></p>
                <br>
                <a class="btn-tell-more ff-sans-b"
                    onclick="pushEventGTM(this, '<?php echo esc_url( $link_urlBazul ); ?>',
                    '<?php echo esc_attr( $link_targetBazul ); ?>')"><?php echo esc_html( $link_titleBazul ); ?></a>
            </div>
        </div>
    </div>
</section>