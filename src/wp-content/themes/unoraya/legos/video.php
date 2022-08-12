<?php $buildVideoCss = esc_url( get_template_directory_uri() . '/css/legos/video.css' ); ?>
<link rel="stylesheet" href="<?php echo $buildVideoCss; ?>" async defer>
<section class="video">
    <?php if(get_sub_field('titulo')): ?>
    <h2 class="text-center"><?php echo get_sub_field('titulo'); ?></h2>
    <br>
    <?php endif; ?>
    <iframe width="100%"
            height="500"
            src="<?php echo get_sub_field('video'); ?>"
            title="YouTube video player"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen></iframe>
</section>