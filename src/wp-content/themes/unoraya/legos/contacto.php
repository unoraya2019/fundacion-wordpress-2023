<?php $buildContactoCss = esc_url( get_template_directory_uri() . '/css/legos/contacto.css' ); ?>
<link rel="stylesheet" href="<?php echo $buildContactoCss; ?>" async defer>
<section class="contactenos">
    <div class="flex">
        <div class="contactenos__form">
            <h1>Contáctenos</h1>
            <p>Si desea conocer información adicional sobre la fundación Bolívar Davivienda y nuestros diferentes
                programas, diligencie el siguiente formulario:</p>
            <form id="contactoForm" onsubmit="return false">
                <div class="flex contactenos__item">
                    <input type="text" class="form-control" id="nombre" name="nombre" maxlength="100" placeholder="Nombres" required>
                    <input type="text" class="form-control" id="apellidos" name="apellidos" maxlength="100" placeholder="Apellidos" required>
                </div>
                <div class="flex contactenos__item">
                    <select class="form-select" required id="tipo_documento" name="tipo_documento">
                        <option selected disabled value="">Tipo de documento de identidad</option>
                        <option value="Cédula de Ciudadanía">Cédula de Ciudadanía</option>
                        <option value="Cédula de Extranjería">Cédula de Extranjería</option>
                        <option value="Cédula de identidad">Cédula de identidad</option>
                        <option value="CIP">CIP</option>
                        <option value="Drivers">Drivers</option>
                        <option value="Permit">Permit</option>
                        <option value="DUI">DUI</option>
                        <option value="Pasaporte">Pasaporte</option>
                        <option value="Tarjeta de Identidad">Tarjeta de Identidad</option>
                    </select>
                    <input type="text" class="form-control" id="documento" name="documento" maxlength="20" placeholder="No. documento" required>
                </div>
                <div class="flex contactenos__item">
                    <input type="tel" class="form-control" id="tel" name="tel" maxlength="12" 
                            placeholder="Teléfono Móvil" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" required>
                    <input type="email" class="form-control" id="email" name="email" maxlength="40" placeholder="Correo electrónico" required>
                </div>
                <div class="flex contactenos__item">
                    <select class="form-select" required id="pais" name="pais">
                        <option selected disabled value="">País</option>
                    </select>
                    <select class="form-select" required id="depto" name="depto">
                        <option selected disabled value="">Departamento</option>
                    </select>
                </div>
                <div class="flex contactenos__item">
                    <select class="form-select" required id="ciudad" name="ciudad">
                        <option selected disabled value="">Ciudad / Municipio</option>
                    </select>
                    <input type="text" class="form-control" id="empresa" maxlength="200" name="empresa" placeholder="Organización / Empresa">
                </div>
                <p class="contactenos__item_p">¿Cómo se enteró de la FBD?</p>
                <div class="flex contactenos__item">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" value="Radio" name="comoMeEntero">
                      <label class="form-check-label" for="radio">
                        Radio
                      </label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" value="Página web" name="comoMeEntero">
                      <label class="form-check-label" for="web">
                        Página web
                      </label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" value="Prensa" name="comoMeEntero">
                      <label class="form-check-label" for="prensa">
                        Prensa
                      </label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" value="Redes Sociales" name="comoMeEntero">
                      <label class="form-check-label" for="redes">
                        Redes Sociales
                      </label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" value="Referidos" name="comoMeEntero">
                      <label class="form-check-label" for="referidos">
                        Referidos
                      </label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" value="Televisión" name="comoMeEntero">
                      <label class="form-check-label" for="tv">
                        Televisión
                      </label>
                    </div>
                </div>
                <div class="flex contactenos__item">
                    <textarea name="mensaje" id="mensaje" class="form-control" maxlength="4500" placeholder="¿Cómo podemos ayudarte?" required></textarea>
                </div>
                <div class="flex contactenos__item">
                    <div class="contactanos__radios">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" required>
                          <label class="form-check-label" for="flexCheckDefault">
                           Autorizo el tratamiento de mi información personal de acuerdo con las
                            Políticas de Tratamiento de Datos de LA FUNDACIÓN publicadas en
                            www.fundacionbolivardavivienda.org
                          </label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked" required>
                          <label class="form-check-label" for="flexCheckChecked">
                           Acepto los Términos y Condiciones
                          </label>
                        </div>
                    </div>
                    <input id="sendFormContact" type="submit" value="ENVIAR">
                </div>
            </form>
        </div>
        <div class="contactenos__img" style="background-image: url(<?php echo get_site_url(); ?>/wp-content/uploads/2022/07/contacto_desktop.jpeg)">
        </div>
    </div>
</section>
<?php $buildContactoJs = esc_url( get_template_directory_uri() . '/js/legos/contacto.js' ); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.17/dist/sweetalert2.all.min.js"
        integrity="sha256-RhRrbx+dLJ7yhikmlbEyQjEaFMSutv6AzLv3m6mQ6PQ=" crossorigin="anonymous"></script>
<script src="<?php echo $buildContactoJs; ?>"></script>