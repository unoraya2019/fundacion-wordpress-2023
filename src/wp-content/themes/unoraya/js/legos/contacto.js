    const form = document.querySelector("#contactoForm");
    form.addEventListener("submit", function (event) {
    	event.preventDefault();
        postData();
    });

    async function postData() {
        $("#sendFormContact").prop('disabled', true);
        pushEventGTMBtn(null, null, 'enviar');
        const nombre = document.getElementById("nombre").value;
        const apellidos = document.getElementById("apellidos").value;
        const tipo_documento = document.getElementById("tipo_documento").value;
        const documento = document.getElementById("documento").value;
        const tel = document.getElementById("tel").value;
        const email = document.getElementById("email").value;
        const depto = document.getElementById("depto").value;
        const ciudad = document.getElementById("ciudad").value;
        const pais = document.getElementById("pais").value;
        const empresa = document.getElementById("empresa").value;
        const mensaje = document.getElementById("mensaje").value;
        if ( !nombre || !apellidos || !tipo_documento || !documento || !tel || !email || !depto || !ciudad || !pais || !mensaje) {
            Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'Faltan datos por ingresar',
            });
            return false;
        }
        if ( !validateInputs(1, nombre) || !validateInputs(2, email) ) {
            Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'Datos incorrectos',
            });
            return false;
        }
        const comoMeEntero = getControlsLikes();
        const data = {
            nombre,
            apellidos,
            tipo_documento,
            documento,
            tel,
            email,
            comoMeEntero,
            depto,
            ciudad,
            empresa,
            pais,
            mensaje
        }
        const url = 'https://unoraya.com/demo/fds/wp-json/api/salesforce';
        const response = await fetch(url, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'token': getToken()
            },
            body: JSON.stringify(data)
        }).then(res => res.json())
        .catch(error => $("#sendFormContact").prop('disabled', false))
        .then(response => showMessage(response));
    }
    
    getRemoteToken();
    
    async function getRemoteToken(){
        const url = 'https://unoraya.com/demo/fds/wp-json/api/tokentmp';
        const response = await fetch(url, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            }
        }).then(res => res.json())
        .catch(error =>  console.log(error)
        )
        .then(response => {
            localStorage.setItem('tkn', response);
            getPaises();
        });
    }
    
    async function getPaises(){
        const url = 'https://unoraya.com/demo/fds/wp-json/api/paises';
        const response = await fetch(url, {
            method: 'GET',
            headers: {
              'Content-Type': 'application/json',
              'token': getToken()
            }
        }).then(res => res.json())
        .catch(error =>  Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'Ocurrio un error al enviar el formulario',
            })
        )
        .then(response => setPaises(response));
    }
    
    const $select = document.querySelector("#pais");
    function setPaises(data) {
        const option = document.createElement('option');
        const items = [];
        for(item of data) {
            option.value = item.Id;
            option.text = item.Name;
            $select.appendChild(option.cloneNode(true));
        }
    }
    $select.addEventListener('change', (event) => {
        getDeptos(event.srcElement.value);
    });
    
    async function getDeptos(pais){
        const url = 'https://unoraya.com/demo/fds/wp-json/api/deptos';
        const response = await fetch(url, {
            method: 'GET',
            headers: {
              'Content-Type': 'application/json',
              'token': getToken(),
              'pais': pais
            }
        }).then(res => res.json())
        .catch(error =>  console.log(error)
        )
        .then(response => setDeptos(response));
    }
    const $selectDeptos = document.querySelector("#depto");
    function setDeptos(data = []) {
        $("#depto").empty().append('<option selected disabled>Departamento</option>');
        const option = document.createElement('option');
        const items = [];
        for(item of data) {
            option.value = item.Id;
            option.text = item.Name;
            $selectDeptos.appendChild(option.cloneNode(true));
        }
    }
    $selectDeptos.addEventListener('change', (event) => {
        getMunicipios(event.srcElement.value);
    });
    
    async function getMunicipios(depto){
        const url = 'https://unoraya.com/demo/fds/wp-json/api/municipios';
        const response = await fetch(url, {
            method: 'GET',
            headers: {
              'Content-Type': 'application/json',
              'token': getToken(),
              'depto': depto
            }
        }).then(res => res.json())
        .catch(error =>  console.log(error)
        )
        .then(response => setMunicipios(response));
    }
    const $selectMunicipios = document.querySelector("#ciudad");
    function setMunicipios(data) {
        $("#ciudad").empty().append('<option selected disabled>Ciudad / Municipio</option>');
        const option = document.createElement('option');
        const items = [];
        for(item of data) {
            option.value = item.Id;
            option.text = item.Name;
            $selectMunicipios.appendChild(option.cloneNode(true));
        }
    }

    function showMessage(res) {
        $("#sendFormContact").prop('disabled', false);
        const respuesta = res[0];
        if ( respuesta && respuesta.errorCode ) {
            Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'Ocurrio un error al enviar el formulario',
            });
        } else {
            Swal.fire({
              icon: 'success',
              title: 'Ok',
              text: 'Mensaje enviado con exito',
            });
        }
    }
    
    function getControlsLikes() {
        const markedCheckbox = document.getElementsByName('comoMeEntero');
        let str = '';
        for (let checkbox of markedCheckbox) {
            if (checkbox.checked) {
                str = str.concat(checkbox.value, ';');
            }
        }
        str = str.substr(0, str.length - 1);
        return str;
    }
    
    function getToken() {
        return localStorage.getItem('tkn');
    }
    
    function validateInputs(type, value) {
        if (type === 1 && value.length > 20) {
            return false;
        }
        if (type === 2 && !(/^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i.test(value))) {
            return false;
        }
        return true;
    }