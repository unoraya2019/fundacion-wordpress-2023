<?php if( have_rows('contenido') ):
    while ( have_rows('contenido') ) : the_row();
        if( get_row_layout() == 'slider-home' ):
            require 'legos/slider-home.php';
        elseif( get_row_layout() == 'slider_eventos' ):
            require 'legos/slider_eventos.php';
        elseif( get_row_layout() == 'slider_texto' ):
            require 'legos/slider_texto.php';
        elseif( get_row_layout() == 'slider_datos' ):
            require 'legos/slider_datos.php';
        elseif( get_row_layout() == 'slider_datos_doble' ):
            require 'legos/slider_datos_doble.php';
        elseif( get_row_layout() == 'slider_perfiles' ):
            require 'legos/slider_perfiles.php';

        elseif( get_row_layout() == 'banner' ):
            require 'legos/banner.php';
        elseif( get_row_layout() == 'banner_con_breadcrumb' ):
            require 'legos/banner_con_breadcrumb.php';
        elseif( get_row_layout() == 'banner_programa' ):
            require 'legos/banner-programa.php';
        elseif( get_row_layout() == 'banner_destacado' ):
            require 'legos/banner_destacado.php';
        elseif( get_row_layout() == 'banner_destacado' ):
            require 'legos/banner_destacado.php';
        elseif( get_row_layout() == 'banner_descriptivo' ):
            require 'legos/banner_descriptivo.php';
        elseif( get_row_layout() == 'banner_azul' ):
            require 'legos/banner_azul.php';
        elseif( get_row_layout() == 'banner_imagen' ):
            require 'legos/banner_imagen.php';
            
        elseif( get_row_layout() == 'carta' ):
            require 'legos/carta.php';
            
        elseif( get_row_layout() == 'tabla-con-filtro' ):
            require 'legos/tabla-con-filtro.php';
            
        elseif( get_row_layout() == 'grid' ):
            require 'legos/grid.php';
            
        elseif( get_row_layout() == 'pasos' ):
            require 'legos/pasos.php';
            
        elseif( get_row_layout() == 'texto_con_fondo' ):
            require 'legos/texto_con_fondo.php';
            
        elseif( get_row_layout() == 'actualidad' ):
            require 'legos/actualidad.php';
        elseif( get_row_layout() == 'galeria_de_imagenes' ):
            require 'legos/galeria_de_imagenes.php';
            
        elseif( get_row_layout() == 'collapse_datos' ):
            require 'legos/collapse_datos.php';
        elseif( get_row_layout() == 'collapse' ):
            require 'legos/collapse.php';
        
        elseif( get_row_layout() == 'links_acceso' ):
            require 'legos/links_acceso.php';
        elseif( get_row_layout() == 'texto_con_imagen' ):
            require 'legos/texto_con_imagen.php';
        elseif( get_row_layout() == 'imagen_con_texto' ):
            require 'legos/imagen_con_texto.php';
        elseif( get_row_layout() == 'titulo_destacado' ):
            require 'legos/titulo_destacado.php';
        elseif( get_row_layout() == 'noticia' ):
            require 'legos/noticia.php';
        
        elseif( get_row_layout() == 'video' ):
            require 'legos/video.php';
        elseif( get_row_layout() == 'lista_videos' ):
            require 'legos/lista_videos.php';

            
        elseif( get_row_layout() == 'perfiles' ):
            require 'legos/perfiles.php';
            
        elseif( get_row_layout() == 'filtros' ):
            require 'legos/filtros.php';
            
        elseif( get_row_layout() == 'libre' ):
            require 'legos/libre.php';
            
        elseif( get_row_layout() == 'link_inscripciones' ):
            require 'legos/link_inscripciones.php';
            
        elseif( get_row_layout() == 'testimonios' ):
            require 'legos/testimonios.php';
        elseif( get_row_layout() == 'logos' ):
            require 'legos/logos.php';
        
        elseif( get_row_layout() == 'logos' ):
            require 'legos/logos.php';
        elseif( get_row_layout() == 'testimonios' ):
            require 'legos/testimonios.php';
       
        elseif( get_row_layout() == 'items' ):
            require 'legos/items.php';
            
        elseif( get_row_layout() == 'donaciones' ):
            require 'legos/donaciones.php';
            
        elseif( get_row_layout() == 'titulo_con_acciones' ):
            require 'legos/titulo_con_acciones.php';
            
        elseif( get_row_layout() == 'cifras' ):
            require 'legos/cifras.php';
            
        elseif( get_row_layout() == 'contacto' ):
            require 'legos/contacto.php';
            
        endif;
    endwhile;
else: ?>
   <h2>sin contenido</h2>
<?php endif; ?>