<?php
    //Definicion del tipo de archivo
    header('Content-type','application/javascript');
    //Recuperamos la variable de WordPress a enviar
    $tmpSliderDatos = isset($_GET['titlePage'])?$_GET['titlePage']:null;
    $tmpItems = isset($_GET['countItem'])?$_GET['countItem']:1;
?>
const btnActiveFirst<?php echo $tmpSliderDatos; ?> = document.getElementById("slide_dato__btn0<?php echo $tmpSliderDatos; ?>");
btnActiveFirst<?php echo $tmpSliderDatos; ?>.classList.add("btn-active");
const swiperDatos<?php echo $tmpSliderDatos; ?> = new Swiper('.swiper_datos-<?php echo $tmpSliderDatos; ?>', {
      autoHeight: true,
      simulateTouch: false,
});
<?php
    $contSliderJs = 0;
    $x = 1;
    while($x <= $tmpItems):
?>
document
.querySelector('.slide-<?php echo $contSliderJs . $tmpSliderDatos; ?>')
.addEventListener('click', function (e) {
e.preventDefault();
var elements = document.getElementById('content_btns_<?php echo $tmpSliderDatos; ?>').getElementsByClassName('btn-active');
    for(element of elements){
      element.classList.remove('btn-active');
    }
var targetElement = document.getElementById("slide_dato__btn<?php echo $contSliderJs . $tmpSliderDatos; ?>");
targetElement.classList.add("btn-active");
swiperDatos<?php echo $tmpSliderDatos; ?>.slideTo(<?php echo $contSliderJs; ?>, 0);
});
<?php 
$x++;
$contSliderJs = $contSliderJs + 1;
endwhile; ?>