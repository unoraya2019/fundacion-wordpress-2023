<?php
    $buildCollapseCss = 'css/collapse.min.css';
    $imageBannerCollpase = get_site_url().'/wp-content/uploads/2022/06/mision_vi_bg.jpg';
    $styleBannerCollapse = 'section.collapse__content{padding:100px 0;padding-bottom:200px;background:url('.$imageBannerCollpase.') 0 0 no-repeat;background-position:50%;background-size:cover}section.collapse__content .accordion{position:relative}section.collapse__content .accordion-header{margin-bottom:0;position:absolute;left:0;width:300px;z-index:999}section.collapse__content h2#headingOne{top:0}section.collapse__content h2#headingTwo{top:60px}section.collapse__content h2#headingThree{top:120px}section.collapse__content .accordion-collapse{margin:0 auto}section.collapse__content .accordion-body{width:900px;margin-left:340px;padding:30px;position:relative}section.collapse__content .accordion-body hr{position:absolute;top:0;left:0;width:400px;margin:0;height:11px;background:#ff671a}section.collapse__content .accordion-item{border:none;background-color:transparent}section.collapse__content .accordion-body,section.collapse__content .accordion-button{background:rgba(0,0,0,.8);color:#fff}section.collapse__content .accordion-button{font-size:22px;font-weight:700}section.collapse__content .accordion-button::after{background:#fff;border-radius:50%;padding:13px}section.collapse__content .accordion-button:focus::after{background:#ff671a}section.collapse__content .accordion-button:focus{color:#ff671b;background-color:rgba(0,0,0,.8);box-shadow:none}section.collapse__content .collapse__content__options{margin-top:70px;width:100%;justify-content:center}section.collapse__content .collapse__content__options a{padding:7px 10px;background-color:rgba(0,0,0,.6);border-radius:4px;margin:0 10px;transition:all .2s linear}section.collapse__content .collapse__content__options a:hover{background-color:rgba(0,0,0)}section.collapse__content .collapse__content__options h3{font-size:14px;color:#fff;margin:0}@media (max-width:600px){section.collapse__content .accordion-header{position:relative;width:100%}section.collapse__content .accordion-body{width:100%;margin-left:0}section.collapse__content .accordion-body hr{width:50%}section.collapse__content h2#headingOne,section.collapse__content h2#headingThree,section.collapse__content h2#headingTwo{top:0}section.collapse__content .collapse__content__options{flex-flow:column;text-align:center}section.collapse__content .collapse__content__options a{margin:6px 0}}';
    createRemoteCss($buildCollapseCss, $styleBannerCollapse);
?>
<link rel="stylesheet" href="<?php echo get_site_url().'/'.$buildCollapseCss; ?>" async defer>
<section class="collapse__content">
    <div class="max-content">
        <div class="accordion" id="accordionExample">
      <div class="accordion-item">
        <h2 class="accordion-header" id="headingOne" >
          <button class="accordion-button ntag12"
                  type="button"
                  onclick="pushEventGTMBtn(this)"
                  data-bs-toggle="collapse"
                  data-bs-target="#collapseOne"
                  aria-expanded="true"
                  aria-controls="collapseOne"
                  autofocus>Nuestra Misión</button>
        </h2>
        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
          <div class="accordion-body">
              <hr>
            <p>Como expresión del compromiso social del Grupo Bolívar, apoyamos y potenciamos proyectos transformadores y de alto impacto que generan capacidades en personas, 
            comunidades y organizaciones, para construir una sociedad más justa, equitativa e innovadora.</p>
          </div>
        </div>
      </div>
      <div class="accordion-item">
        <h2 class="accordion-header" id="headingTwo">
          <button class="accordion-button collapsed ntag13"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#collapseTwo"
            aria-expanded="false"
            onclick="pushEventGTMBtn(this)"
            aria-controls="collapseTwo">Nuestra Visión</button>
        </h2>
        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
          <div class="accordion-body">
              <hr>
           <p>La Fundación Bolívar Davivienda será reconocida como una fundación líder en los lugares donde está presente el grupo, por el 
           impacto de sus programas en el logro de transformaciones sociales efectivas y sostenibles.</p>
          </div>
        </div>
      </div>
      <div class="accordion-item">
        <h2 class="accordion-header" id="headingThree">
          <button class="accordion-button collapsed ntag14"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#collapseThree"
          aria-expanded="false"
          aria-controls="collapseThree"
          onclick="pushEventGTMBtn(this)">Gobierno Corporativo</button>
        </h2>
        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
          <div class="accordion-body">
              <hr>
            <p>En la Fundación Bolívar Davivienda contamos con un Sistema de Buen Gobierno en armonía con los estatutos, a través del cual se definen e identifican las funciones,
            naturaleza y razón de ser de cada uno de nuestros órganos de Gobierno Corporativo (Consejo Directivo, Consejo Ejecutivo, Director Ejecutivo y Revisor Fiscal), asignando las 
            responsabilidades y obligaciones que permiten el cumplimiento de las metas fundacionales y la adecuada atención a las necesidades de los diferentes grupos de interés.</p>
            <p>Lo anterior, se complementa con la promoción de competencias profesionales y del comportamiento, que priman como elementos fundamentales del desarrollo de la institución entre fundadores, administradores y colaboradores, donde el respeto hacia los valores éticos se convierte en un activo imprescindible que guía el actuar cotidiano de nuestra organización.</p>
            <p>Los miembros de los órganos de dirección y administración desempeñan tales cargos en atención a sus altas condiciones morales y profesionales.</p>
          </div>
        </div>
      </div>
    </div>
    <div class="flex collapse__content__options">
        <a class="ntag15" onclick="pushEventGTM(this, '<?php echo get_site_url(); ?>/wp-content/uploads/2022/04/estatutosfbd-2022-vf.pdf', '_blank', 'h5')"><h3>Conoce Nuestros Estatutos</h3></a>
        <a class="ntag16" onclick="pushEventGTM(this, '<?php echo get_site_url(); ?>/wp-content/uploads/2022/04/La_Fundacion_Codigo_Gobierno_Corporativo.pdf', '_blank', 'h5')"><h3>Código de Gobierno Corporativo</h3></a>
        <a class="ntag17" onclick="pushEventGTM(this, '<?php echo get_site_url(); ?>/wp-content/uploads/2022/04/La_Fundacion_Reglamento_Consejo_Ejecutivo.pdf', '_blank', 'h5')"><h3>Reglamento Ejecutivo</h3></a>
    </div>
    </div>
</section>