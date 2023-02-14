// A $( document ).ready() block.
$(document).ready(function () {
    console.log("ready!");

    //slider de Banner Principal
    $(".ntag1").on("click", function () {
        let ntag = {
            'event': 'event_click',
            'EventoCategoria': '1. Home',
            'EventoTipo': 'Click',
            'EventoEtiqueta': '1.0 Banner Principal - '+$(this).data("title"),
            'EventoLanding': 'FBD Principal'
        };

        dataLayer.push(ntag)
        console.log("newtag", ntag)
    });

    //botón de abrir menú en mobile
    $(".ntag2").on("click", function () {
        let ntag = {
            'event': 'event_click',
            'EventoCategoria': '1. Home',
            'EventoTipo': 'Click',
            'EventoEtiqueta': '1.1 Menu - BotonMenu',
            'EventoLanding': 'FBD Principal'
        };
        dataLayer.push(ntag)
        console.log("newtag", ntag)
    });

    //Botónes de menú || va sin números por que se generan dinámicamente
    $(".menubtns li a").on("click", function () {
        let title = $(this).text()
        if($(this).hasClass("dropdown-item")){
            
            title = "Nuestros Programas"+" - "+$(this).text()
        }

        let ntag = {
            'event': 'event_click',
            'EventoCategoria': window.location.pathname.length == 1 ? "home":window.location.pathname,
            'EventoTipo': 'Click',
            'EventoEtiqueta': title,
            'EventoLanding': 'FBD Principal'
        };
        dataLayer.push(ntag)
        console.log("newtag", ntag)
    });

    //Conozca más del home 
    $(".ntag4").on("click", function () {
        let ntag = {
            'event': 'event_click',
            'EventoCategoria': window.location.pathname.length == 1 ? "home":window.location.pathname,
            'EventoTipo': 'Click',
            'EventoEtiqueta': 'Sobre nosotros - Conozca mas - '+$(this).data("title"),
            'EventoLanding': 'FBD Principal'
        };
        dataLayer.push(ntag)
        console.log("newtag", ntag)
    });

    //sección nuestros programas home
    $(".ntag5").on("click", function () {
        let ntag = {
            'event': 'event_click',
            'EventoCategoria': window.location.pathname.length == 1 ? "home":window.location.pathname,
            'EventoTipo': 'Click',
            'EventoEtiqueta': 'Seccion - Leer más - '+$(this).data("title"),
            'EventoLanding': 'FBD Principal'
        };
        dataLayer.push(ntag)
        console.log("newtag", ntag)
    });


    //banner de noticias actualidad
    $(".ntag6").on("click", function () {
        let ntag = {
            'event': 'event_click',
            'EventoCategoria': '1. Home',
            'EventoTipo': 'Click',
            'EventoEtiqueta': '1.5 - Actualidad '+$(this).data("title"),
            'EventoLanding': 'FBD Principal'
        };
        dataLayer.push(ntag)
        console.log("newtag", ntag)
    });

    //carrusel de eventos de Home
    $(".ntag7").on("click", function () {
        let ntag = {
            'event': 'event_click',
            'EventoCategoria': '1. Home',
            'EventoTipo': 'Click',
            'EventoEtiqueta': '1.6 Ver evento - '+$(this).data("title"),
            'EventoLanding': 'FBD Principal'
        };
        dataLayer.push(ntag)
        console.log("newtag", ntag)
    });

    //ver noticias sección actualidad abierta
    $(".ntag8").on("click", function () {
        let ntag = {
            'event': 'event_click',
            'EventoCategoria': '1. Home',
            'EventoTipo': 'Click',
            'EventoEtiqueta': '1.7 Ver noticia - '+$("#actualidadTitle").text(),
            'EventoLanding': 'FBD Principal'
        };
        dataLayer.push(ntag)
        console.log("newtag", ntag)
    });

    //footer menú 1
    $(".ntag9").on("click", function () {
        let ntag = {
            'event': 'event_click',
            'EventoCategoria': window.location.pathname.length == 1 ? "home":window.location.pathname,
            'EventoTipo': 'Footer',
            'EventoEtiqueta': 'NP - '+$(this).text(),
            'EventoLanding': 'FBD Principal'
        };
        dataLayer.push(ntag)
        console.log("newtag", ntag)
    });

    //footer menú 2
    $(".ntag10").on("click", function () {
        let ntag = {
            'event': 'event_click',
            'EventoCategoria': window.location.pathname.length == 1 ? "home":window.location.pathname,
            'EventoTipo': 'Footer',
            'EventoEtiqueta': 'Legales - '+$(this).text(),
            'EventoLanding': 'FBD Principal'
        };
        dataLayer.push(ntag)
        console.log("newtag", ntag)
    });

    //footer menú 3
    $(".ntag11").on("click", function () {
        let ntag = {
            'event': 'event_click',
            'EventoCategoria': window.location.pathname.length == 1 ? "home":window.location.pathname,
            'EventoTipo': 'Footer',
            'EventoEtiqueta': 'Documentacion - '+$(this).text(),
            'EventoLanding': 'FBD Principal'
        };
        dataLayer.push(ntag)
        console.log("newtag", ntag)
    });

    //La fundación botón Carrusel 1
    $(".ntag12").on("click", function () {
        let ntag = {
            'event': 'event_click',
            'EventoCategoria': '2. La fundacion',
            'EventoTipo': 'Click',
            'EventoEtiqueta': '2.1 Nuestra mision',
            'EventoLanding': 'FBD Principal'
        };
        dataLayer.push(ntag)
        console.log("newtag", ntag)
    });

    //La fundación botón Carrusel 2
    $(".ntag13").on("click", function () {
        let ntag = {
            'event': 'event_click',
            'EventoCategoria': '2. La fundacion',
            'EventoTipo': 'Click',
            'EventoEtiqueta': '2.2 Nuestra vision',
            'EventoLanding': 'FBD Principal'
        };
        dataLayer.push(ntag)
        console.log("newtag", ntag)
    });

    //La fundación botón Carrusel 3
    $(".ntag14").on("click", function () {
        let ntag = {
            'event': 'event_click',
            'EventoCategoria': '2. La fundacion',
            'EventoTipo': 'Click',
            'EventoEtiqueta': '2.3 Gobierno corporativo',
            'EventoLanding': 'FBD Principal'
        };
        dataLayer.push(ntag)
        console.log("newtag", ntag)
    });

    //carrusel doc 1
    $(".ntag15").on("click", function () {
        let ntag = {
            'event': 'event_click',
            'EventoCategoria': '2. La fundacion',
            'EventoTipo': 'Click',
            'EventoEtiqueta': '2.4 Conoce nuestros estatutos',
            'EventoLanding': 'FBD Principal'
        };
        dataLayer.push(ntag)
        console.log("newtag", ntag)
    });

    //carrusel doc 2
    $(".ntag16").on("click", function () {
        let ntag = {
            'event': 'event_click',
            'EventoCategoria': '2. La fundacion',
            'EventoTipo': 'Click',
            'EventoEtiqueta': '2.5 Cod gobierno corporativo',
            'EventoLanding': 'FBD Principal'
        };
        dataLayer.push(ntag)
        console.log("newtag", ntag)
    });

    //carrusel doc 3
    $(".ntag17").on("click", function () {
        let ntag = {
            'event': 'event_click',
            'EventoCategoria': '2. La fundacion',
            'EventoTipo': 'Click',
            'EventoEtiqueta': '2.6 Reglamento ejecutivo',
            'EventoLanding': 'FBD Principal'
        };
        dataLayer.push(ntag)
        console.log("newtag", ntag)
    });

    //generic tabs section
    $(".ntag18").on("click", function () {
        let ntag = {
            'event': 'event_click',
            'EventoCategoria': window.location.pathname.length == 1 ? "home":window.location.pathname,
            'EventoTipo': 'Click',
            'EventoEtiqueta': $(this).text(),
            'EventoLanding': 'FBD Principal'
        };
        dataLayer.push(ntag)
        console.log("newtag", ntag)
    });

    //se ejecuta genérico ntag4
    $(".ntag19").on("click", function () {
        let ntag = {
            'event': 'event_click',
            'EventoCategoria': '3. Aflora',
            'EventoTipo': 'Click',
            'EventoEtiqueta': '3.2 Sobre nosotros - Conozca mas',
            'EventoLanding': 'FBD Principal'
        };
        dataLayer.push(ntag)
        console.log("newtag", ntag)
    });

    //características Aflora - desarrollamos talento
    $(".ntag20").on("click", function () {
        let ntag = {
            'event': 'event_click',
            'EventoCategoria': '3.1. Aflora - Desarrollamos Talento',
            'EventoTipo': 'Click',
            'EventoEtiqueta': '3.1.1 Caracteristicas - {@Caracteristica}',
            'EventoLanding': 'FBD Principal'
        };
        dataLayer.push(ntag)
        console.log("newtag", ntag)
    });

    //botón naranja genérico
    $(".genBtn").on("click", function () {
        let ntag = {
            'event': 'event_click',
            'EventoCategoria': window.location.pathname.length == 1 ? "home":window.location.pathname,
            'EventoTipo': 'Click',
            'EventoEtiqueta': "Redireciona a " + $(this).data("target"),
            'EventoLanding': 'FBD Principal'
        };
        dataLayer.push(ntag)
        console.log("newtag", ntag)
    });

    //Generico titulo_con_acciones__content botones
    $(".ntag22").on("click", function () {
        let ntag = {
            'event': 'event_click',
            'EventoCategoria': window.location.pathname.length == 1 ? "home":window.location.pathname,
            'EventoTipo': 'Click',
            'EventoEtiqueta':  $(this).text(),
            'EventoLanding': 'FBD Principal'
        };
        dataLayer.push(ntag)
        console.log("newtag", ntag)
    });
    
    //Cultivarte zona ludica ver mas items
    $(".ntag30").on("click", function () {
        let ntag = {
            'event': 'event_click',
            'EventoCategoria': '5.2. Cultivarte - Zona Ludica',
            'EventoTipo': 'Click',
            'EventoEtiqueta': '5.2.1 '+$(this).data("title")+' - Ver mas',
            'EventoLanding': 'FBD Principal'
        };
        dataLayer.push(ntag)
        console.log("newtag", ntag)
    });


    //banner azul genérico
    $(".bannerblue").on("click", function () {
        let ntag = {
            'event': 'event_click',
            'EventoCategoria': window.location.pathname.length == 1 ? "home":window.location.pathname,
            'EventoTipo': 'Click',
            'EventoEtiqueta': $(this).data("title"),
            'EventoLanding': 'FBD Principal'
        };
        dataLayer.push(ntag)
        console.log("newtag", ntag)
    });

    //audiciones FJC conozca mas
    $(".ntag39").on("click", function () {
        let ntag = {
            'event': 'event_click',
            'EventoCategoria': '7.2. Filarmonica joven de colombia - Audiciones',
            'EventoTipo': 'Intencion',
            'EventoEtiqueta': '7.2.1 Inscribase aqui',
            'EventoLanding': 'FBD Principal'
        };
        dataLayer.push(ntag)
        console.log("newtag", ntag)
    });

    //
    
    $(".anchortag").on("click", function () {
        let ntag = {
            'event': 'event_click',
            'EventoCategoria': window.location.pathname.length == 1 ? "home":window.location.pathname,
            'EventoTipo': 'Click',
            'EventoEtiqueta': $(this).text(),
            'EventoLanding': 'FBD Principal'
        };
        dataLayer.push(ntag)
        console.log("newtag", ntag)
    });

    //seguir enlace FJC
    $(".ntag44").on("click", function () {
        let ntag = {
            'event': 'event_click',
            'EventoCategoria': '7.2.2. Filarmonica joven de colombia - Audiciones - Audicion',
            'EventoTipo': 'Click',
            'EventoEtiqueta': '7.2.2.1 Videos Youtube - Seguir enlace',
            'EventoLanding': 'FBD Principal'
        };
        dataLayer.push(ntag)
        console.log("newtag", ntag)
    });

    //FAQ repertorios
    $(".accordion-button").on("click", function () {
        let ntag = {
            'event': 'event_click',
            'EventoCategoria': window.location.pathname.length == 1 ? "home":window.location.pathname,
            'EventoTipo': 'Click',
            'EventoEtiqueta': '7.2.4.1 Pregunta - '+$(this).text(),
            'EventoLanding': 'FBD Principal'
        };
        dataLayer.push(ntag)
        console.log("newtag", ntag)
    });

    //enlace solicitar recursos 
    $(".libre h3 a").on("click", function () {
        let ntag = {
            'event': 'event_click',
            'EventoCategoria': '8.1. Inversion social - Desarrollamos talento',
            'EventoTipo': 'Click',
            'EventoEtiqueta': '8.1 Solicitar recursos ingrese aqui',
            'EventoLanding': 'FBD Principal'
        };
        dataLayer.push(ntag)
        console.log("newtag", ntag)
    });

    //click en enlace dentro de una descripción 
    $(".description p a").on("click", function () {
        let ntag = {
            'event': 'event_click',
            'EventoCategoria': window.location.pathname.length == 1 ? "home":window.location.pathname,
            'EventoTipo': 'Click',
            'EventoEtiqueta': 'Link: '+ $(this).text(),
            'EventoLanding': 'FBD Principal'
        };
        dataLayer.push(ntag)
        console.log("newtag", ntag)
    });

    //perfiles rdai
    $(".ntag53").on("click", function () {
        let ntag = {
            'event': 'event_click',
            'EventoCategoria': window.location.pathname.length == 1 ? "home":window.location.pathname,
            'EventoTipo': 'Click',
            'EventoEtiqueta': $(this).data('title'),
            'EventoLanding': 'FBD Principal'
        };
        dataLayer.push(ntag)
        console.log("newtag", ntag)
    });

    //ver noticias
    $(".ntag59").on("click", function () {
        let ntag = {
            'event': 'event_click',
            'EventoCategoria': '12. Actualidad',
            'EventoTipo': 'Click',
            'EventoEtiqueta': '12.1 Ver noticia - '+$(this).data("title"),
            'EventoLanding': 'FBD Principal'
        };
        dataLayer.push(ntag)
        console.log("newtag", ntag)
    });

    //ver eventos
    $(".ntag60").on("click", function () {
        let ntag = {
            'event': 'event_click',
            'EventoCategoria': '13. Eventos',
            'EventoTipo': 'Click',
            'EventoEtiqueta': '13.1 Ver evento - '+ $(this).data("title"),
            'EventoLanding': 'FBD Principal'
        };
        dataLayer.push(ntag)
        console.log("newtag", ntag)
    });
    $(".ntag61").on("click", function () {
        let ntag = {
            'event': 'event_click',
            'EventoCategoria': '14. Informe de sostenibilidad',
            'EventoTipo': 'Click',
            'EventoEtiqueta': '14.1 Informe - {@NombreInforme}',
            'EventoLanding': 'FBD Principal'
        };
        dataLayer.push(ntag)
        console.log("newtag", ntag)
    });
    $(".ntag62").on("click", function () {
        let ntag = {
            'event': 'event_click',
            'EventoCategoria': '15. About us',
            'EventoTipo': 'Click',
            'EventoEtiqueta': '15.1 Learn more - {@Programa}',
            'EventoLanding': 'FBD Principal'
        };
        dataLayer.push(ntag)
        console.log("newtag", ntag)
    });
    $(".ntag63").on("click", function () {
        let ntag = {
            'event': 'event_click',
            'EventoCategoria': '15. About us',
            'EventoTipo': 'Click',
            'EventoEtiqueta': '15.2 Downloads - {@NombreArchivo}',
            'EventoLanding': 'FBD Principal'
        };
        dataLayer.push(ntag)
        console.log("newtag", ntag)
    });

});