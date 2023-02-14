<?php
if ( !isset($loadTituloConAcciones) ){
    $loadTituloConAcciones = true;
    $linkTcA1 = get_sub_field('boton_primario');
    $link_urlTcA1 = $linkTcA1['url'];
    $link_titleTcA1 = $linkTcA1['title'];
    $link_targetTcA1 = $linkTcA1['target'] ? $linkTcA1['target'] : '_self';
    
    $linkTcA2 = get_sub_field('boton_secundario');
    $link_urlTcA2 = $linkTcA2['url'];
    $link_titleTcA2 = $linkTcA2['title'];
    $link_targetTcA2 = $linkTcA2['target'] ? $linkTcA2['target'] : '_self';
    $buildTituloConAccionesCss = esc_url( get_template_directory_uri() . '/css/legos/titulo_con_acciones.css' );
?>
<link rel="stylesheet" href="<?php echo $buildTituloConAccionesCss; ?>" async defer>
<?php } ?>
<section class="max-content titulo_con_acciones">
    <div class="titulo_con_acciones__content margin-0-auto text-center">
        <h4 class="ff-sans-b"><?php echo get_sub_field('titulo'); ?></h4>
        <div class="pra-p ff-nunito">
            <?php echo get_sub_field('contenido'); ?>
        </div>
        <div class="two-btn d-flex justify-content-between ff-sans-b flex-wrap">
        <?php if(get_sub_field('boton_primario') && get_sub_field('boton_secundario')): ?>
            <a class="btn-sm btn-line-btn text-center ntag22"
                onclick="pushEventGTM(this, '<?php echo $link_urlTcA2; ?>', '<?php echo $link_targetTcA2; ?>')"><?php echo esc_html( $link_titleTcA2 ); ?></a>
            <a class="btn-sm btn-solid-btn text-center ntag22"
                onclick="pushEventGTM(this, '<?php echo $link_urlTcA1; ?>', '<?php echo $link_targetTcA1; ?>')"><?php echo esc_html( $link_titleTcA1 ); ?></a>
        <?php elseif(get_sub_field('boton_primario') && !get_sub_field('boton_secundario')): ?>
            <a class="btn-sm btn-solid-btn text-center margin-0-auto ntag22"
                onclick="pushEventGTM(this, '<?php echo $link_urlTcA1; ?>', '<?php echo $link_targetTcA1; ?>')"><?php echo esc_html( $link_titleTcA1 ); ?></a>
        <?php endif; ?>
        </div>
    </div>
</section>