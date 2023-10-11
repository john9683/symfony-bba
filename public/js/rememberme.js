import jQuery from 'jquery';

(function($) {
    "use strict";

    if ( $.cookie('RememberMeChecked') === 'true' ) {
        $("#customCheckRememberMe").prop("checked", true);
    }

    $("#btnLogin").on("click", function () {
        $.cookie('RememberMeChecked', $("#customCheckRememberMe").prop("checked"), { expires: 1 });
    });

})(jQuery);