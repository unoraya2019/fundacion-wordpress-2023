<?php
    //Definicion del tipo de archivo
    header('Content-type','application/javascript');
    //Recuperamos la variable de WordPress a enviar
    $tmpItems = isset($_GET['countItem'])?$_GET['countItem']:1;
?>
  const btnActiveFirst = document.getElementById("slide_dato__btn0");
  btnActiveFirst.classList.add("btn-active");
  const swiperDatos = new Swiper('.swiper_datos', {
          autoHeight: true,
          simulateTouch: false,
    });
    <?php
        $contSliderJs = 0;
        $x = 1;
        while($x <= $tmpItems):
    ?>
    document
    .querySelector('.slide-<?php echo $contSliderJs; ?>')
    .addEventListener('click', function (e) {
      e.preventDefault();
      var elements = document.getElementsByClassName('btn-active');
        for(element of elements){
          element.classList.remove('btn-active');
        }
      var targetElement = e.target || e.srcElement;
      targetElement.classList.add("btn-active");
      
      swiperDatos.slideTo(<?php echo $contSliderJs; ?>, 0);
    });
  <?php 
  $x++;
  $contSliderJs = $contSliderJs + 1;
  endwhile; ?>