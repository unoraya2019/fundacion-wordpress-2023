<?php $buildLinkVideosCss = esc_url( get_template_directory_uri() . '/css/legos/lista_videos.css' ); ?>
<link rel="stylesheet" href="<?php echo $buildLinkVideosCss; ?>" async defer>
<section class="max-content videos">
    <h2 class="ff-sans-b mb-5 text-center"><?php echo get_sub_field('titulo'); ?></h2>
    <h4><?php echo get_sub_field('subtitulo'); ?></h4>
    <div class="flex videos__content">
        <?php if(get_sub_field('items')): ?>
          <?php while(the_repeater_field('items')): ?>
            <iframe height="500px" width="100%"
                src="https://www.youtube.com/embed/<?php echo get_sub_field('link_video'); ?>"
                title="YouTube video player"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen></iframe>
          <?php endwhile; ?>
        <?php endif; ?>
    </div>
</section>