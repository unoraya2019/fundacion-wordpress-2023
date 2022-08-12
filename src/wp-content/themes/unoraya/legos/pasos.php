<?php $buildPasosCss = esc_url( get_template_directory_uri() . '/css/legos/pasos.css' ); ?>
<link rel="stylesheet" href="<?php echo $buildPasosCss; ?>" async defer>
<section class="pasos_content que-about-ytube">
    <?php if(get_sub_field('encabezado')): ?>
    <div class="max-content pasos_content__encabezado">
        <?php echo get_sub_field('encabezado'); ?>
        <?php if(get_sub_field('link')): ?>
            <a class="btn-seguir-en ff-lato"
                onclick="pushEventGTM(this, '<?php echo get_sub_field('link'); ?>', '_blank')"> Seguir enlace</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <div class="container">
        <div class="zig-zag-se margin-0-auto">
            <?php if(get_sub_field('items')): ?>
              <?php while(the_repeater_field('items')): ?>
                <?php if(get_sub_field('orientacion') == 1): ?>
                <div class="inr-zig-zag impar">
                    <div class="d-flex">
                        <?php if(get_sub_field('numero')): ?>
                        <div class="num-se d-flex align-items-center justify-content-center ff-sans-b">
                           <?php echo get_sub_field('numero'); ?>
                        </div>
                        <?php endif; ?>
                        <div class="txt-prt d-flex align-items-center position-relative after-po">
                            <div class="position-relative z-index-5">
                                <?php echo get_sub_field('contenido'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php else : ?>
                <div class="inr-zig-zag par">
                    <div class="d-flex one-one">
                        <?php if(get_sub_field('numero')): ?>
                        <div class="num-se d-flex align-items-center justify-content-center">
                            <?php echo get_sub_field('numero'); ?>
                        </div>
                        <?php endif; ?>
                        <div class="txt-prt position-relative after-po">
                            <div class="position-relative z-index-5">
                                <?php echo get_sub_field('contenido'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
              <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
</section>