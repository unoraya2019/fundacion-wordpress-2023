<?php
    //Definicion del tipo de archivo
    header('Content-type','application/javascript');
    //Recuperamos la variable de WordPress a enviar
    $tmpTitlePage = isset($_GET['titlePage'])?$_GET['titlePage']:null;
    $tmpTitlePageCurrent = isset($_GET['titleCurrentPage'])?$_GET['titleCurrentPage']:null;
?>
new WOW().init();
$(document).ready(function () {
    window.addEventListener('scroll', function () {
        if (window.scrollY > 50) {
            $("#menu_fixed").addClass('fixed-header');
        } else {
            $("#menu_fixed").removeClass('fixed-header');
        }
    });
});
let topBtn = document.querySelector(".top-btn");
topBtn.onclick = () => window.scrollTo({ top: 0, behavior: "smooth" });
window.onscroll = () => window.scrollY > 500 ? topBtn.style.opacity = 1 : topBtn.style.opacity = 0;

// Envio de datalayers
function pushEventGTM(element, url, target, findTag = 'h2', customTitlePost = false) {
    const currentParentPage = '<?php echo $tmpTitlePage; ?>';
    const currentPage = '<?php echo $tmpTitlePageCurrent; ?>';
    const padre = $(element).parent();
    let titulo = '';
    const finEtiqueta = ' ' + $(element).text();
    if ( findTag !== 'h2' && !customTitlePost ) {
        titulo = $(padre).find(findTag).eq(0).text();
    }
    else if ( findTag !== 'h2' && customTitlePost ) {
        titulo = removeTags(customTitlePost);
    }
    else {
        titulo = $(padre).find("h2").eq(0).text() ? $(padre).find("h2").eq(0).text() : $(padre).find("h3").eq(0).text();
    }
    let eventActionTmp = 'Click';
    if ( target === '_blank' ) {
        eventActionTmp = 'Link';
    }
    titulo = titulo + finEtiqueta;
    titulo = titulo.replace(/(\r\n|\n|\r)/gm, " ");
    titulo = titulo.replace(/  /g, "");
    titulo = normalizarStringDataLayer(titulo);
    const tmpCategory = validateDuplicate(currentParentPage, currentPage);
    const dataGTM = {
        'eventCategory': tmpCategory,
        'eventAction': eventActionTmp,
        'eventLabel': titulo,
        'eventValue': '',
        'event': 'eventClick'
    }
    console.log(dataGTM);
    dataLayer.push(dataGTM);
    window.open(url, target);
}

// Envio de datalayers para btns
function pushEventGTMBtn(element, findTag, custom = '') {
    const currentParentPage = '<?php echo $tmpTitlePage; ?>';
    const currentPage = '<?php echo $tmpTitlePageCurrent; ?>';
    let titulo = '';
    if ( custom == '' ) {
        titulo = $(element).text();
    } else {
        titulo = custom;
    }
    titulo = titulo.replace(/(\r\n|\n|\r)/gm, " ");
    titulo = titulo.replace(/  /g, "");
    titulo = normalizarStringDataLayer(titulo);
    const tmpCategory = validateDuplicate(currentParentPage, currentPage);
    const dataGTM = {
        'eventCategory': tmpCategory,
        'eventAction': 'Click',
        'eventLabel': titulo,
        'eventValue': '',
        'event': 'eventClick'
    }
    console.log(dataGTM);
    dataLayer.push(dataGTM);
}
// Validar título duplicado
function validateDuplicate(title1, title2) {
    if (title1 == title2) {
        return title1;
    }
    return title1 + ' ' + title2;
}
// Eliminar tags html
function removeTags(str) {
    if ((str===null) || (str===''))
        return false;
    else
        str = str.toString();
    return str.replace( /(<([^>]+)>)/ig, '');
}

function normalizarStringDataLayer(cadena) {
    let tmpCadena = cadena.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
    tmpCadena = tmpCadena.replace(/[&\/\\#,+()$~%.'":*¿!¡?<>{}]/g, '');
    tmpCadena = tmpCadena.trim();
    return tmpCadena.toLocaleLowerCase()   
}

// Mostrar el primer item de los filtros checkeado
$('.checked').click();


// Listener para los botones que deben enviar un datalayer a GTM
const btnsGTM = document.querySelectorAll('.send_dataGTM');
btnsGTM.forEach(box => {
  box.addEventListener('click', function handleClick(e) {
    const urlBtn = $(e.srcElement).attr("data-url");
    const targetBtn = $(e.srcElement).attr("data-target");
    const findTag = $(e.srcElement).attr("data-findTag");
    const customTitlePost = $(e.srcElement).attr("data-titlepost");
    pushEventGTM($(e.srcElement), urlBtn, targetBtn, findTag, customTitlePost);
  });
});