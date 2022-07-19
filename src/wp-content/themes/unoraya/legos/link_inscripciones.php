<?php
    $linkIns = get_sub_field('enlace');
    $link_urlIns = $linkIns['url'];
    $link_titleIns = $linkIns['title'];
    $link_targetIns = $linkIns['target'] ? $linkIns['target'] : '_self';
?>
<section class="box-manu-main">
    <div class="black-bx">
        <div class="text-center">
            <h2 class="ff-sans-b"><?php echo get_sub_field('titulo'); ?></h2>
            <?php if(get_sub_field('enlace')): ?>
            <a class="ff-lato"
                onclick="pushEventGTM(this, '<?php echo $link_urlIns; ?>', '<?php echo $link_targetIns; ?>')"><?php echo esc_html( $link_titleIns ); ?></a>
            <?php endif; ?>
        </div>
    </div>
</section>