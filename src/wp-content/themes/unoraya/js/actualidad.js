var swiper = new Swiper(".mySwiper", {
    slidesPerView: 1,
    spaceBetween: 30,
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
    breakpoints: {
        1000: {
            slidesPerView: 3,
            spaceBetween: 30,
        },
    }
});
function filterTextInput() {
  var input, radios, radio_filter, text_filter, td0, i, divList;
  input = document.getElementById("myInput");
  text_filter = input.value.toUpperCase();
  divList = $(".actualidad-pg");
  console.log(divList);
  for (i = 0; i < divList.length; i++) {
    td0 = divList[i].getAttribute('data-content');
    if (td0) {
      if (td0.toUpperCase().indexOf(text_filter) > -1) {
        divList[i].style.display = "";
      } else {
        divList[i].style.display = "none";
      }
    } 
  }
}