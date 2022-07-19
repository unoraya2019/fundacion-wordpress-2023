<?php $buildTextoConFondoCss = esc_url( get_template_directory_uri() . '/css/legos/texto_con_fondo.css' ); ?>
<link rel="stylesheet" href="<?php echo $buildTextoConFondoCss; ?>" async defer>
<section class="educational-training texto_con_fondo position-relative after-po">
    <img
        src="<?php echo get_sub_field('fondo'); ?>"
        alt="<?php echo get_sub_field('titulo'); ?>" class="object-fit-cover for-hght">
    <div class="dots-three-rt position-absolute">
        <div class="d-flex"><span></span> <span></span> <span></span> <span></span> <span></span> <span></span>
            <span></span> <span></span></div>
        <div class="d-flex"><span></span> <span></span> <span></span> <span></span> <span></span> <span></span>
            <span></span> <span></span></div>
        <div class="d-flex"><span></span> <span></span> <span></span> <span></span> <span></span> <span></span>
            <span></span> <span></span></div>
        <div class="d-flex"><span></span> <span></span> <span></span> <span></span> <span></span> <span></span>
            <span></span> <span></span></div>
        <div class="d-flex"><span></span> <span></span> <span></span> <span></span> <span></span> <span></span>
            <span></span> <span></span></div>
        <div class="d-flex"><span></span> <span></span> <span></span> <span></span> <span></span> <span></span>
            <span></span> <span></span></div>
        <div class="d-flex"><span></span> <span></span> <span></span> <span></span> <span></span> <span></span>
            <span></span> <span></span></div>
        <div class="d-flex"><span></span> <span></span> <span></span> <span></span> <span></span> <span></span>
            <span></span> <span></span></div>
        <div class="d-flex"><span></span> <span></span> <span></span> <span></span> <span></span> <span></span>
            <span></span> <span></span></div>
        <div class="d-flex"><span></span> <span></span> <span></span> <span></span> <span></span> <span></span>
            <span></span> <span></span></div>
    </div>
    <div class="container position-absolute">
        <br>
        <h3 data-wow-duration="2s" class="ff-sans-b wow bounceInDown">
            <?php echo get_sub_field('titulo'); ?>
        </h3>
        <div data-wow-duration="2s" class="small-bx wow bounceInUp">
            <div class="pra-p ff-nunito">
                <?php echo get_sub_field('contenido'); ?>
            </div>
        </div>
    </div>
</section>