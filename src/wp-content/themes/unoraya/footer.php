<!--<section id="cookieControl" class="cookieControl" style="display: block;">
    <div class="cookieControl__Bar cookieControl__Bar--bottom-full">
        <div class="cookieControl__BarContainer">
            <div>
                <h3>Cookies &amp; privacidad</h3>
                <p>Utilizamos cookies para personalizar el contenido y los anuncios, proporcionar funciones de redes
                    sociales y analizar nuestro tráfico.</p>
            </div>
            <div class="cookieControl__BarButtons">
                <a href="http://unoraya.com/demo/fds/wp-content/uploads/2022/04/Terminos_y_Condiciones_FBD_2020.pdf" target="_blank" class="btn btn-primary">Aprenda más</a>
                <a onclick="showHideCookie('none')" class="btn btn-warning">Entendido</a>
            </div>
        </div>
    </div>
</section>-->
<footer class="position-relative"><a id="scroll" href="javascript:void(0)"><span><svg version="1.2"
                xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" overflow="visible"
                preserveAspectRatio="none" viewBox="0 0 24 24" width="39" height="39">
                <g>
                    <path id="arrow-up" xmlns:default="http://www.w3.org/2000/svg"
                        d="M20.1,12.45c0-0.36-0.14-0.7-0.39-0.95l-6.77-6.78c-0.25-0.25-0.59-0.39-0.95-0.38  c-0.35-0.01-0.7,0.13-0.94,0.39L4.28,11.5c-0.52,0.51-0.53,1.35-0.01,1.87c0,0,0.01,0.01,0.01,0.01l0.78,0.78  c0.51,0.52,1.34,0.52,1.85,0.02c0.01-0.01,0.01-0.01,0.02-0.02l3.06-3.04v7.33c-0.01,0.34,0.13,0.66,0.39,0.88  c0.26,0.23,0.6,0.35,0.94,0.34h1.33c0.34,0.01,0.68-0.11,0.94-0.34c0.26-0.22,0.41-0.55,0.4-0.89v-7.32l3.06,3.05  c0.24,0.26,0.58,0.41,0.94,0.4c0.36,0,0.7-0.14,0.95-0.4l0.78-0.78c0.25-0.25,0.39-0.59,0.39-0.94l0,0L20.1,12.45z"
                        vector-effect="non-scaling-stroke" style="fill:#77777a"></path>
                </g>
            </svg></span></a>
    <div class="container">
        <div class="end-line position-relative after-po"></div>
        <div class="d-none-n d-block-767 mobile-links-footer">
            <div class="container">
                <div class="accordion" id="accordionExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Nuestros programas
                          </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                          <div class="accordion-body">
                             <?php wp_nav_menu(array(
                              'menu' => 'footer',
                              'container' => ''
                            )); ?>
                          </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            Legales
                          </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                          <div class="accordion-body">
                            <?php wp_nav_menu(array(
                              'menu' => 'footer2',
                              'container' => ''
                            )); ?>
                          </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                           Documentación
                          </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                          <div class="accordion-body">
                            <?php wp_nav_menu(array(
                              'menu' => 'footer3',
                              'container' => ''
                            )); ?>
                          </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFour">
                          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                           Datos de contacto
                          </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionExample">
                          <div class="accordion-body">
                            <p>
                                Av. Calle 26 # 69-63. Piso 11. <br>
                                Bogotá, Colombia
                            </p>
                            <p>(+57 1) 2201610 ext 98706</p>
                            <p>(+57 1) 2201578</p>
                          </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="inner-footer margin-0-auto">
            <div class="d-flex flex-wrap justify-content-between">
                <div class="logo-social-m">
                    <a href="/" aria-current="page"
                        class="f-logo nuxt-link-exact-active nuxt-link-active">
                        <img src="<?php echo get_site_url(); ?>/wp-content/uploads/2022/03/footer_logo.png"
                            alt="Fundacion Bolivar Sonaqube" class="img-fluid">
                    </a>
                    <div class="social-m">
                        <mini-title class="ff-sans-b">Síguenos</mini-title>
                        <div class="set-of-icon"><a href="https://www.facebook.com/fundacionbolivardavivienda/"
                                target="_blank"><svg version="1.2" xmlns="http://www.w3.org/2000/svg"
                                    xmlns:xlink="http://www.w3.org/1999/xlink" overflow="visible"
                                    preserveAspectRatio="none" viewBox="0 0 24 24" width="32" height="32">
                                    <g>
                                        <path id="facebook" xmlns:default="http://www.w3.org/2000/svg"
                                            d="M16.5,3.46c-0.79-0.09-1.58-0.13-2.37-0.12c-1.07-0.05-2.11,0.33-2.89,1.06c-0.76,0.81-1.15,1.89-1.08,3v2.28  H7.5v3.08h2.65v7.9h3.19v-7.9H16l0.41-3.08h-3.07v-2c-0.03-0.4,0.08-0.79,0.31-1.12c0.33-0.29,0.77-0.43,1.21-0.37h1.64V3.46  L16.5,3.46z"
                                            vector-effect="non-scaling-stroke" style="fill:#878787"></path>
                                    </g>
                                </svg></a> <a href="https://www.instagram.com/fundacionbd/" target="blank"><svg
                                    version="1.2" xmlns="http://www.w3.org/2000/svg"
                                    xmlns:xlink="http://www.w3.org/1999/xlink" overflow="visible"
                                    preserveAspectRatio="none" viewBox="0 0 24 24" width="32" height="32">
                                    <g>
                                        <path id="instagram" xmlns:default="http://www.w3.org/2000/svg"
                                            d="M13.88,13.88c-1.04,1.04-2.72,1.04-3.76,0.01c0,0-0.01-0.01-0.01-0.01c-1.04-1.04-1.04-2.72-0.01-3.76  c0,0,0.01-0.01,0.01-0.01c1.04-1.04,2.72-1.04,3.76-0.01c0,0,0.01,0.01,0.01,0.01C14.83,11.13,14.74,12.78,13.88,13.88z M14.88,9.09  c-0.76-0.77-1.8-1.2-2.88-1.19c-2.26-0.01-4.09,1.82-4.1,4.08c0,0.01,0,0.01,0,0.02c-0.01,2.26,1.82,4.09,4.08,4.1  c0.01,0,0.01,0,0.02,0c2.26,0.01,4.09-1.82,4.1-4.08c0-0.01,0-0.01,0-0.02c0.01-1.09-0.42-2.14-1.2-2.9L14.88,9.09z M16.88,7.09  c-0.38-0.4-1.02-0.41-1.41-0.03s-0.41,1.02-0.03,1.41s1.02,0.41,1.41,0.03c0.2-0.19,0.31-0.46,0.31-0.73  c0.04-0.26-0.04-0.52-0.21-0.72L16.88,7.09z M12.8,5.44h1.1h1c0.36,0.01,0.72,0.04,1.07,0.1c0.25,0.04,0.5,0.1,0.74,0.19  c0.69,0.28,1.24,0.83,1.52,1.52c0.09,0.24,0.15,0.49,0.19,0.74c0.06,0.35,0.09,0.71,0.1,1.07c0,0.42,0,0.75,0,1s0,0.61,0,1.1  c0,0.48,0,0.75,0,0.8c0,0.08,0,0.31,0,0.8s0,0.85,0,1.1s0,0.58,0,1c-0.01,0.36-0.04,0.72-0.1,1.07c-0.04,0.25-0.1,0.5-0.19,0.74  c-0.28,0.69-0.83,1.24-1.52,1.52c-0.24,0.09-0.49,0.15-0.74,0.19c-0.35,0.06-0.71,0.09-1.07,0.1h-1h-3.79h-1  C8.74,18.49,8.37,18.47,8,18.43c-0.25-0.04-0.5-0.1-0.74-0.19c-0.69-0.28-1.24-0.83-1.52-1.52C5.66,16.49,5.61,16.24,5.57,16  c-0.06-0.35-0.09-0.71-0.1-1.07c0-0.42,0-0.75,0-1c0-0.25,0-0.61,0-1.1c0-0.48,0-0.75,0-0.8c0-0.08,0-0.31,0-0.8s0-0.85,0-1.1  s0-0.58,0-1C5.48,8.75,5.51,8.37,5.57,8c0.04-0.25,0.1-0.5,0.19-0.74c0.28-0.69,0.83-1.23,1.52-1.5C7.51,5.68,7.75,5.61,8,5.57  c0.35-0.06,0.71-0.09,1.07-0.1h1h2.73V5.44z M19.94,8.7c0.01-1.24-0.45-2.44-1.29-3.35c-0.91-0.84-2.11-1.3-3.35-1.29  C14.69,4.02,13.59,4,12,4S9.31,4.02,8.7,4.05c-1.24-0.01-2.44,0.46-3.35,1.3C4.51,6.26,4.05,7.46,4.06,8.7C4.02,9.31,4,10.41,4,12  s0.02,2.69,0.05,3.3c-0.01,1.24,0.45,2.44,1.29,3.35c0.91,0.84,2.12,1.3,3.36,1.29C9.31,19.98,10.41,20,12,20s2.69-0.02,3.3-0.05  c1.24,0.01,2.44-0.45,3.35-1.29c0.84-0.91,1.3-2.11,1.29-3.35C19.98,14.69,20,13.59,20,12S19.98,9.31,19.94,8.7z"
                                            vector-effect="non-scaling-stroke" style="fill:#878787"></path>
                                    </g>
                                </svg></a> <a
                                href="https://www.linkedin.com/company/fundaci%C3%B3n-bol%C3%ADvar-davivienda/"
                                target="_blank"><svg version="1.2" xmlns="http://www.w3.org/2000/svg"
                                    xmlns:xlink="http://www.w3.org/1999/xlink" overflow="visible"
                                    preserveAspectRatio="none" viewBox="0 0 24 24" width="32" height="32">
                                    <g>
                                        <path id="linkedin" xmlns:default="http://www.w3.org/2000/svg"
                                            d="M4.19,9.32v10.32h3.44V9.32H4.19z M7.85,6.14C7.86,5.66,7.67,5.2,7.32,4.86c-0.37-0.34-0.87-0.52-1.38-0.5  c-0.51-0.02-1.01,0.16-1.4,0.5C4.18,5.19,3.98,5.65,3.99,6.14C3.98,6.62,4.17,7.08,4.51,7.41c0.37,0.35,0.86,0.53,1.37,0.51l0,0  C6.4,7.94,6.91,7.76,7.29,7.41c0.35-0.33,0.55-0.79,0.53-1.27l0,0H7.85z M19.99,13.73c0.09-1.25-0.3-2.48-1.08-3.46  c-0.74-0.79-1.78-1.22-2.86-1.18c-0.4,0-0.8,0.05-1.19,0.16c-0.32,0.1-0.61,0.25-0.87,0.45c-0.21,0.16-0.41,0.34-0.59,0.54  c-0.16,0.18-0.3,0.38-0.43,0.58l0,0v-1.5H9.52v0.5c0,0.33,0,1.36,0,3.08s0,3.97,0,6.74h3.47v-5.76c-0.03-0.3,0-0.59,0.07-0.88  c0.14-0.35,0.37-0.65,0.66-0.89c0.31-0.25,0.7-0.37,1.1-0.36c0.52-0.04,1.02,0.2,1.32,0.62c0.31,0.52,0.45,1.11,0.42,1.71v5.52h3.43  V13.73L19.99,13.73z"
                                            vector-effect="non-scaling-stroke" style="fill:#878787"></path>
                                    </g>
                                </svg></a> <a href="https://twitter.com/FundacionBD?lang=es" target="_blank"><svg
                                    version="1.2" xmlns="http://www.w3.org/2000/svg"
                                    xmlns:xlink="http://www.w3.org/1999/xlink" overflow="visible"
                                    preserveAspectRatio="none" viewBox="0 0 24 24" width="32" height="32">
                                    <g>
                                        <path id="twitter" xmlns:default="http://www.w3.org/2000/svg"
                                            d="M20.2,6.92c-0.62,0.27-1.27,0.45-1.94,0.52c0.71-0.41,1.24-1.07,1.48-1.85c-0.66,0.39-1.38,0.67-2.13,0.81  c-1.27-1.36-3.41-1.43-4.76-0.15c-0.03,0.02-0.05,0.05-0.08,0.07c-0.65,0.62-1.01,1.48-1,2.38c0,0.26,0.03,0.52,0.08,0.77  C10.51,9.41,9.19,9.05,8,8.43C6.82,7.84,5.78,7.01,4.94,6C4.35,7.02,4.33,8.27,4.89,9.3C5.16,9.77,5.54,10.16,6,10.45  C5.46,10.43,4.93,10.27,4.46,10l0,0c-0.01,0.78,0.26,1.54,0.77,2.14c0.49,0.6,1.17,1.01,1.93,1.16c-0.29,0.07-0.58,0.11-0.88,0.11  c-0.21,0-0.43-0.02-0.64-0.05c0.44,1.37,1.7,2.3,3.14,2.33c-1.17,0.97-2.65,1.5-4.17,1.51c-0.27,0.02-0.54,0.02-0.81,0  c1.55,0.99,3.36,1.5,5.2,1.46c1.14,0.01,2.28-0.19,3.36-0.57c0.96-0.34,1.86-0.85,2.64-1.51c0.74-0.63,1.39-1.37,1.92-2.19  c0.52-0.8,0.93-1.67,1.2-2.59c0.26-0.88,0.4-1.79,0.4-2.7c0-0.19,0-0.34,0-0.44C19.18,8.18,19.75,7.59,20.2,6.92L20.2,6.92z"
                                            vector-effect="non-scaling-stroke" style="fill:#878787"></path>
                                    </g>
                                </svg></a> <a href="https://www.youtube.com/channel/UCEcX3O1scgTJ9qpLwrpEb-A"
                                target="_blank"><svg version="1.2" xmlns="http://www.w3.org/2000/svg"
                                    xmlns:xlink="http://www.w3.org/1999/xlink" overflow="visible"
                                    preserveAspectRatio="none" viewBox="0 0 24 24" width="32" height="32">
                                    <g>
                                        <path id="youtube-play" xmlns:default="http://www.w3.org/2000/svg"
                                            d="M10.07,14.47V9.23l5,2.63l-5,2.6l0,0V14.47z M12,5.47c-2.61,0-4.78,0.06-6.53,0.19H5.29H5.05H4.81  c-0.1,0.02-0.2,0.04-0.3,0.08L4.22,5.88C4.11,5.94,4,6,3.9,6.08C3.79,6.16,3.69,6.26,3.6,6.36L3.45,6.65  C3.31,6.85,3.2,7.06,3.12,7.29c-0.13,0.32-0.22,0.66-0.26,1c-0.13,0.99-0.2,2-0.19,3v1.83c0,0.29,0.02,0.68,0.06,1.18  c0.04,0.5,0.08,0.97,0.13,1.42c0.05,0.36,0.14,0.71,0.28,1.05c0.08,0.21,0.18,0.42,0.3,0.61l0.16,0.25  c0.17,0.18,0.37,0.32,0.59,0.43c0.17,0.09,0.35,0.16,0.53,0.22c0.19,0.04,0.38,0.08,0.57,0.1h0.37h0.51l2.09,0.08  c1.06,0,2.31,0.02,3.74,0.07c2.61,0,4.78-0.07,6.53-0.2h0.17h0.24h0.25c0.1-0.02,0.2-0.04,0.3-0.08l0.29-0.13  c0.11-0.05,0.22-0.12,0.32-0.2c0.11-0.08,0.21-0.18,0.3-0.28l0.15-0.18c0.14-0.2,0.25-0.41,0.33-0.64c0.13-0.32,0.22-0.66,0.26-1  c0.13-0.99,0.2-2,0.19-3v-1.89c0-0.29-0.02-0.68-0.06-1.18s-0.08-0.98-0.13-1.43C21.09,7.96,21,7.61,20.86,7.27  c-0.08-0.21-0.18-0.42-0.3-0.61L20.4,6.47c-0.09-0.1-0.19-0.2-0.3-0.28c-0.1-0.08-0.21-0.14-0.32-0.2l-0.29-0.14  c-0.1-0.04-0.2-0.06-0.3-0.08h-0.24h-0.24h-0.18h-0.76c-0.51-0.13-1.31-0.2-2.39-0.23S13.17,5.5,12,5.49l0,0V5.47z"
                                            vector-effect="non-scaling-stroke" style="fill:#878787"></path>
                                    </g>
                                </svg></a></div>
                    </div>
                </div>
                <div class="footer-menu d-none-767">
                    <mini-title>Nuestros programas</mini-title>
                    <?php wp_nav_menu(array(
                      'menu' => 'footer',
                      'container' => ''
                    )); ?>
                </div>
                <div class="footer-menu two d-none-767">
                    <mini-title>Legales</mini-title>
                    <div class="if-two-ul-is-there">
                        <?php wp_nav_menu(array(
                          'menu' => 'footer2',
                          'container' => ''
                        )); ?>
                    </div>
                    <br>
                    <mini-title>Documentación</mini-title>
                    <div class="if-two-ul-is-there">
                        <?php wp_nav_menu(array(
                          'menu' => 'footer3',
                          'container' => ''
                        )); ?>
                    </div>
                </div>
                <div class="footer-menu d-none-767">
                    <div class="if-two-ul-is-there">
                        <mini-title>Datos de contacto</mini-title>
                        <ul class="ff-nunito">
                            <li>
                                <p>
                                    Av. Calle 26 # 69-63. Piso 11. <br>
                                    Bogotá, Colombia
                                </p>
                            </li>
                        </ul>
                    </div>
                    <div class="if-two-ul-is-there">
                        <mini-title>Recepción</mini-title>
                        <ul class="ff-nunito">
                            <li>
                                <p>(+57 1) 2201610 ext 98981</p>
                            </li>
                        </ul>
                    </div>
                    <div class="if-two-ul-is-there">
                        <mini-title>Mesa de Ayuda</mini-title>
                        <ul class="ff-nunito">
                            <li>
                                <p>(+57 1) 2201610 ext 98706</p>
                            </li>
                        </ul>
                    </div>
                    <div class="if-two-ul-is-there">
                        <mini-title>Fax</mini-title>
                        <ul class="ff-nunito">
                            <li>
                                <p>(+57 1) 2201578</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="copywrite text-center">
                <p>Copyright - Fundación Bolívar Davivienda 2021</p>
            </div>
        </div>
    </div>
    <a class="top-btn" href="javascript:void(0)"><span><svg version="1.2" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" overflow="visible" preserveAspectRatio="none" viewBox="0 0 24 24" width="39" height="39"><g><path id="arrow-up" xmlns:default="http://www.w3.org/2000/svg" d="M20.1,12.45c0-0.36-0.14-0.7-0.39-0.95l-6.77-6.78c-0.25-0.25-0.59-0.39-0.95-0.38  c-0.35-0.01-0.7,0.13-0.94,0.39L4.28,11.5c-0.52,0.51-0.53,1.35-0.01,1.87c0,0,0.01,0.01,0.01,0.01l0.78,0.78  c0.51,0.52,1.34,0.52,1.85,0.02c0.01-0.01,0.01-0.01,0.02-0.02l3.06-3.04v7.33c-0.01,0.34,0.13,0.66,0.39,0.88  c0.26,0.23,0.6,0.35,0.94,0.34h1.33c0.34,0.01,0.68-0.11,0.94-0.34c0.26-0.22,0.41-0.55,0.4-0.89v-7.32l3.06,3.05  c0.24,0.26,0.58,0.41,0.94,0.4c0.36,0,0.7-0.14,0.95-0.4l0.78-0.78c0.25-0.25,0.39-0.59,0.39-0.94l0,0L20.1,12.45z" vector-effect="non-scaling-stroke" style="fill:#77777a"></path></g></svg></span></a>
</footer>
<?php $mainJs = esc_url( get_template_directory_uri() . '/js/main.js' ); ?>
<script src="<?php echo $mainJs; ?>"></script>
<?php $mainCss = esc_url( get_template_directory_uri() . '/css/main.css' ); ?>
<link rel="stylesheet" href="<?php echo $mainCss; ?>" async/>
<?php
    $titlePageTmp = eliminar_acentos(get_the_title( $post->post_parent ));
    $titleCurrentPageTmp = eliminar_acentos(get_the_title());
    $utilsJs = 'utils.js.php?titlePage='.$titlePageTmp.'&titleCurrentPage='.$titleCurrentPageTmp;
    $pathFileUtilsJs = esc_url( get_template_directory_uri() . '/js/'.$utilsJs );
?>
<script src="<?php echo $pathFileUtilsJs; ?>"></script>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-K3234SQ"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
</body>
</html>