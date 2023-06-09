<section class="la-fundacion-main-sec pg-la-main">
    <div class="d-flex flex-wrap">
      <div class="sm-wid txt-part-lft d-flex align-items-center justify-content-center">
        <div class="in-sm-size-bx text-white">
          <h1 class="ff-sans-b"><?php echo get_sub_field('titulo'); ?></h1>
          <div class="pra ff-nunito">
            <p><?php echo get_sub_field('descripcion'); ?></p>
          </div>
        </div>
      </div>
      <div class="sm-wid img-part-rght">
        <ul class="d-flex">
            <?php if(get_sub_field('imagenes')): ?>
              <?php while(the_repeater_field('imagenes')): ?>
                <li><img src="<?php echo get_sub_field('imagen'); ?>" alt="<?php echo get_sub_field('titulo'); ?>" class="object-fit-cover"></li>
              <?php endwhile; ?>
            <?php endif; ?>
        </ul>
      </div>
    </div>
</section>
