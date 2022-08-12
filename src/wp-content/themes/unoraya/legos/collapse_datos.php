<?php
    $buildCollapseDatosCss = 'css/collapse_datos.min.css';
    $imageBannerCollpaseDatos = esc_url(get_template_directory_uri()).'/img/ic_dw_fq.svg';
    $styleBannerCollapse = '.collapse_datos{margin-top:50px;margin-bottom:50px}.collapse_datos button.accordion-button{display:block;padding:12px 65px 12px 15px;border:20px;color:#fff;font-size:18px;background-color:#43464a;border-radius:10px!important;outline:0;margin-top:30px}.collapse_datos .accordion-item{border:none}.collapse_datos .accordion-body{margin-bottom:40px;box-shadow:0 0 10px #d5d5d5;padding:30px;margin-top:-10px;border-radius:0 0 10px}.collapse_datos button{position:relative}.collapse_datos .accordion-button::after{position:absolute;right:30px;top:12px;background-image:url('.$imageBannerCollpaseDatos.')}';
    createRemoteCss($buildCollapseDatosCss, $styleBannerCollapse);
?>
<link rel="stylesheet" href="<?php echo get_site_url().'/'.$buildCollapseDatosCss; ?>" async defer>
<section class="max-content collapse_datos">
    <div class="accordion" id="accordionExample">
    <?php if(get_sub_field('items')): ?>
      <?php
        $countAccordeon = 0;
        while(the_repeater_field('items')):
            $countAccordeon = $countAccordeon + 1;
        ?>
        <div class="accordion-item">
            <h2 class="accordion-header" id="heading<?php echo $countAccordeon; ?>">
              <button class="accordion-button"
                      type="button"
                       onclick="pushEventGTMBtn(this, '')"
                      data-bs-toggle="collapse"
                      data-bs-target="#collapse<?php echo $countAccordeon; ?>"
                      aria-expanded="true"
                      aria-controls="collapse<?php echo $countAccordeon; ?>">
                <?php echo get_sub_field('titulo'); ?>
              </button>
            </h2>
            <div id="collapse<?php echo $countAccordeon; ?>"
                class="accordion-collapse collapse"
                aria-labelledby="heading<?php echo $countAccordeon; ?>"
                data-bs-parent="#accordionExample">
              <div class="accordion-body">
                  <?php echo get_sub_field('descripcion'); ?>
              </div>
            </div>
        </div>
      <?php endwhile; ?>
    <?php endif; ?>
    </div>
</section>