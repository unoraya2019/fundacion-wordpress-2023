<?php $buildFiltrosCss = esc_url( get_template_directory_uri() . '/css/legos/filtros.css' ); ?>
<link rel="stylesheet" href="<?php echo $buildFiltrosCss; ?>" async defer>
<section class="filtros_section max-content">
    <?php if(get_sub_field('titulo_seccion')): ?>
        <h2><?php echo get_sub_field('titulo_seccion'); ?></h2>
    <?php endif; ?>
    <?php if(get_sub_field('descripcion_seccion')): ?>
        <p class="text-center"><?php echo get_sub_field('descripcion_seccion'); ?></p>
    <?php endif; ?>
    <div class="flex">
        <aside class="filtros__menu">
            <h2>Filtros</h2>
            <h3><?php echo get_sub_field('titulo_filtro_1'); ?></h3>
            <?php if(get_sub_field('filtro1')):
                $checked1 = '';
                $countFilter1 = 0;
                while(the_repeater_field('filtro1')): 
                $idFilter1 = str_replace(' ', '', get_sub_field('label'));
                $countFilter1 = $countFilter1 + 1;
                if ( $countFilter1 == 1 ) { $checked1 = 'checked'; }
                else { $checked1 = ''; }
              ?>
                <div class="form-check">
                  <input class="form-check-input <?php echo $checked1; ?>"
                         type="checkbox"
                         name="filtro1"
                         value="<?php echo $idFilter1; ?>"
                         id="<?php echo $idFilter1; ?>">
                  <label class="form-check-label" for="<?php echo $idFilter1; ?>">
                    <?php echo get_sub_field('label'); ?>
                  </label>
                </div>
              <?php endwhile; ?>
            <?php endif; ?>
            <h3><?php echo get_sub_field('titulo_filtro_2'); ?></h3>
            <?php if(get_sub_field('filtro2')):
                $checked2 = '';
                $countFilter2 = 0;
                while(the_repeater_field('filtro2')): 
                $idFilter2 = str_replace(' ', '', get_sub_field('label'));
                $countFilter2 = $countFilter2 + 1;
                if ( $countFilter2 == 1 ) { $checked2 = 'checked'; }
                else { $checked2 = ''; }
              ?>
                <div class="form-check">
                  <input class="form-check-input <?php echo $checked2; ?>"
                         type="checkbox"
                         name="filtro2"
                         value="<?php echo $idFilter2; ?>"
                         id="<?php echo $idFilter2; ?>">
                  <label class="form-check-label" for="<?php echo $idFilter2; ?>">
                    <?php echo get_sub_field('label'); ?>
                  </label>
                </div>
              <?php endwhile; ?>
            <?php endif; ?>
        </aside>
        <div class="flex filtros__main">
        <?php if(get_sub_field('items')): ?>
          <?php while(the_repeater_field('items')):
            $idFilterTag1 = str_replace(' ', '', get_sub_field('filtro1'));
            $idFilterTag2 = str_replace(' ', '', get_sub_field('filtro2'));
          ?>
            <div class="player <?php echo $idFilterTag1; ?> <?php echo $idFilterTag2; ?>">
                <h3><?php echo get_sub_field('titulo'); ?></h3>
                <h4><?php echo get_sub_field('subtitulo'); ?></h4>
                <p><?php echo get_sub_field('descripcion'); ?></p>
                <?php if(get_sub_field('titulo_filtro_1')): ?>
                <p>
                    <b><?php echo get_sub_field('titulo_filtro_1'); ?></b>
                    <span><?php echo get_sub_field('filtro1'); ?></span>
                </p>
                <?php endif; ?>
                <?php if(get_sub_field('titulo_filtro_2')): ?>
                <p>
                    <b><?php echo get_sub_field('titulo_filtro_2'); ?></b>
                    <span><?php echo get_sub_field('filtro2'); ?></span>
                </p>
                <?php endif; ?>
                <?php if(get_sub_field('titulo_dato_extra')): ?>
                <p>
                    <b><?php echo get_sub_field('titulo_dato_extra'); ?></b>
                    <span><?php echo get_sub_field('dato_extra'); ?></span>
                </p>
                <?php endif; ?>
                <p>
                    <img src="<?php echo esc_url( get_template_directory_uri() . '/img/email.svg' ); ?>">
                    <a onclick="pushEventGTMBtn(this, '')"
                    href="milto:<?php echo get_sub_field('email'); ?>"><?php echo get_sub_field('email'); ?></a>
                </p>
            </div>
          <?php endwhile; ?>
        <?php endif; ?>
        </div>
    </div>
</section>
<?php $buildFilterJs = esc_url( get_template_directory_uri() . '/js/lib-filter.js' ); ?>
<script src="<?php echo $buildFilterJs; ?>"></script>
<?php $buildFiltrosJs = esc_url( get_template_directory_uri() . '/js/legos/filtros.js' ); ?>
<script src="<?php echo $buildFiltrosJs; ?>"></script>