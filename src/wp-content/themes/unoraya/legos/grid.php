<?php
    $buildGridCss = 'css/grid.min.css';
    $styleGrid = '.grid_section .text-center h3{font-weight:500;font-family:Nunito,sans-serif}.grid_section .text-center h3 b{display:block}.sec-name:before{content:"";background-image:url('.get_site_url().'/wp-content/uploads/2022/04/puntos_fbd.svg);position:absolute;display:inline-block;background-repeat:no-repeat;background-size:cover;bottom:-17px;right:-140px;width:210px;height:90px}@media (max-width:991px){.program-box .title-tx h3{font-size:24px;max-width:300px}.sec-name:before{background-image:none}}';
    createRemoteCss($buildGridCss, $styleGrid);
?>
<link rel="stylesheet" href="<?php echo get_site_url().'/'.$buildGridCss; ?>" async defer>
<section class="our-program-section grid_section">
    <div class="d-flex flex-wrap">
        <?php if(get_sub_field('titulo')): ?>
        <div class="sec-name fr-width-height d-flex align-items-center position-relative justify-content-center">
            <div class="text-white text-center">
                <h3><?php 
                        $titleSectionPrograma = get_sub_field('titulo');
                        echo $titleSectionPrograma;
                    ?>
                </h3>
            </div>
        </div>
        <?php endif; ?>
        <?php if(get_sub_field('items')): ?>
          <?php while(the_repeater_field('items')): ?>
            <?php
                $imageG = "";
                if ( wp_is_mobile() ) {
                    $imageG = get_sub_field('imagen_responsive');
                } else {
                    $imageG = get_sub_field('imagen');
                }
                $linkG = get_sub_field('link');
                $link_url = $linkG['url'];
                $link_title = $linkG['title'];
                $link_target = $linkG['target'] ? $linkG['target'] : '_self';
            ?>
            <div class="program-box position-relative fr-width-height">
              <div class="for-img position-absolute">
                <img src="<?php echo esc_url($imageG); ?>" alt="<?php echo esc_attr($link_title); ?>" class="object-fit-cover"/>
              </div>
              <div class="for-img d-none-n d-block-767 position-absolute">
                <img src="<?php echo esc_url($imageG); ?>" alt="<?php echo esc_attr($link_title); ?>" class="object-fit-cover"/>
              </div>
              <div class="title-tx position-absolute text-white ff-sans-b">
                <h3><?php echo get_sub_field('titulo'); ?></h3>
              </div>
              <div class="d-none-n plus-sign d-block-767 position-absolute">
                  <a style="font-size: 0px;" onclick="pushEventGTM(this,
                                         '<?php echo $link_url; ?>',
                                         '<?php echo $link_target; ?>',
                                         'h3',
                                         '<?php echo $titleSectionPrograma.' ' .get_sub_field('titulo'); ?>')"
                  class="after-po before-po position-relative ntag5" data-title="<?php echo get_sub_field('titulo'); ?>"><?php echo esc_html( $link_title ); ?></a>
              </div>
              <div class="on-hover-show position-absolute d-flex align-items-center"></div>
              <div class="on-hover-2div position-absolute d-flex align-items-center">
                <p><?php echo get_sub_field('descripcion'); ?></p>
                <a class="btn-read-mr position-absolute ntag5" data-title="<?php echo get_sub_field('titulo'); ?>"
                    onclick="pushEventGTM(this,
                                         '<?php echo $link_url; ?>',
                                         '<?php echo $link_target; ?>',
                                         'h3',
                                         '<?php echo $titleSectionPrograma.' ' .get_sub_field('titulo'); ?>')">
                    <?php echo esc_html( $link_title ); ?>
                </a>
              </div>
            </div>
          <?php endwhile; ?>
        <?php endif; ?>
    </div>
</section>
