<?php
    $buildItemsCss = 'css/items.min.css';
    $itemsImageUrl = esc_url(get_template_directory_uri().'/img/exclamation-icon.svg');
    $styleItems = '.items__content .diff-style{background-color:#43464a;padding:14px 18px;width:48%;margin:10px 1%;border-radius:10px}.items__content .one-que.diff-style p{align-items:center;height:100%}.items__content .one-que.diff-style p:before{background:url('.$itemsImageUrl.') 0 0 no-repeat;height:87px;width:87px;top:-11px;left:-16px}.items__content .one-que.diff-style p span{margin-left:70px;color:#fff}@media (max-width:900px){section.items__content{margin-bottom:20px}.items__content .diff-style{width:100%;margin:20px 15px}}';
    createRemoteCss($buildItemsCss, $styleItems);
?>
<link rel="stylesheet" href="<?php echo get_site_url().'/'.$buildItemsCss; ?>" async defer>
<section class="items__content">
    <div class="some-points-autod-pg margin-0-auto">
        <?php if(get_sub_field('titulo')): ?>
        <div class="text-center">
            <h4 class="ff-sans-b"><?php echo get_sub_field('titulo'); ?></h4>
        </div>
        <?php endif; ?>
        <div class="d-flex flex-wrap for-margin-1">
            <?php if(get_sub_field('listado')): ?>
              <?php while(the_repeater_field('listado')): ?>
                <?php if(!get_sub_field('destacar')): ?>
                <div class="one-bx-grey">
                    <div class="info-tx position-relative after-po before-po ff-sans-r">
                        <p><?php echo get_sub_field('texto'); ?></p>
                    </div>
                </div>
                <?php else : ?>
                <div class="one-que diff-style">
                    <p class="flex position-relative before-po">
                    <span>Recuerde, el video no debe tener cortes ni ser editado.</span>
                  </p>
                </div>
                <?php endif; ?>
              <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
</section>