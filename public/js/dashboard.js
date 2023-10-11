import jQuery from 'jquery';

(function($) {
  "use strict";

  $("#menu-toggle").on('click', function(e) {
    e.preventDefault();
    $("#wrapper").toggleClass("toggled");

    if ($("#wrapper").hasClass("toggled")) {
      $("#menu-toggle").text("Показать меню")
    } else {
      $("#menu-toggle").text("Скрыть меню")
    }
  });
})(jQuery);
