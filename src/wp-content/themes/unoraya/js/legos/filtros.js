
var allCheckboxes = document.querySelectorAll('input[type=checkbox]');
var allPlayers = Array.from(document.querySelectorAll('.player'));
var checked = {};

getChecked('filtro1');
getChecked('filtro2');

Array.prototype.forEach.call(allCheckboxes, function (el) {
  el.addEventListener('change', toggleCheckbox);
});

function toggleCheckbox(e) {
  getChecked(e.target.name);
  setVisibility();
}

function getChecked(name) {
  checked[name] = Array.from(document.querySelectorAll('input[name=' + name + ']:checked')).map(function (el) {
    return el.value;
  });
}

function setVisibility() {
  allPlayers.map(function (el) {
    var filtro1 = checked.filtro1.length ? _.intersection(Array.from(el.classList), checked.filtro1).length : true;
    var filtro2 = checked.filtro2.length ? _.intersection(Array.from(el.classList), checked.filtro2).length : true;

    if (filtro1 && filtro2) {
      el.style.display = 'block';
    } else {
      el.style.display = 'none';
    }
  });
}