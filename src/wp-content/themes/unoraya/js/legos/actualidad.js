function changeActualy(fecha, categorias, titulo, imagen, id, link) {
    const element0 = document.getElementById("actualidad0");
    element0.classList.remove("actualidad--active");
    const element1 = document.getElementById("actualidad1");
    element1.classList.remove("actualidad--active");
    const element2 = document.getElementById("actualidad2");
    element2.classList.remove("actualidad--active");
    document.getElementById(id).classList.add("actualidad--active");
    document.getElementById("actualidadImage").src = imagen;
    document.getElementById("actualidadTitle").innerHTML = titulo;
    document.getElementById("actualidadDate").innerHTML = fecha;
    document.getElementById("actualidadLink").innerHTML = categorias;
    document.getElementById("actualidadUrl").setAttribute('data-link', link);
}
function sendActionGTM(btnID) {
    const link = btnID.getAttribute("data-link");
    pushEventGTM(btnID, link, '_self');
}
document.getElementById("actualidad0").click();