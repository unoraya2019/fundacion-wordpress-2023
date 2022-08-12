<section class="max-content tabla-con-filtro">
    <br><br>
    <p><?php echo get_sub_field('descripcion'); ?></p>
    <h2><?php echo get_sub_field('titulo'); ?></h2>
    <?php if(get_sub_field('titulos')): ?>
      <?php while(the_repeater_field('titulos')): ?>
        <input type="text"
            class="table-filter form-control"
            data-table="order-table"
            placeholder="Ingrese <?php echo get_sub_field('titulo_1'); ?>, <?php echo get_sub_field('titulo_2'); ?>, <?php echo get_sub_field('titulo_3'); ?>...">
      <?php endwhile; ?>
    <?php endif; ?>
    <br>
    <table class="order-table table">
        <thead>
            <tr>
                <?php if(get_sub_field('titulos')): ?>
                  <?php while(the_repeater_field('titulos')): ?>
                    <th><?php echo get_sub_field('titulo_1'); ?></th>
                    <th><?php echo get_sub_field('titulo_2'); ?></th>
                    <th><?php echo get_sub_field('titulo_3'); ?></th>
                    <th><?php echo get_sub_field('titulo_4'); ?></th>
                  <?php endwhile; ?>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if(get_sub_field('items')): ?>
              <?php while(the_repeater_field('items')): ?>
                <tr>
                    <td><?php echo get_sub_field('nombre_1'); ?></td>
                    <td><?php echo get_sub_field('nombre_2'); ?></td>
                    <td><?php echo get_sub_field('nombre_3'); ?></td>
                    <td><?php echo get_sub_field('nombre_4'); ?></td>
                </tr>
              <?php endwhile; ?>
            <?php endif; ?>
            
        </tbody>
    </table>
</section>
<?php $buildTablaFiltrosJs = esc_url( get_template_directory_uri() . '/js/legos/tabla-con-filtro.js' ); ?>
<script src="<?php echo $buildTablaFiltrosJs; ?>"></script>