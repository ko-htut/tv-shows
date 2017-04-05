(function ($) {
    $(function () {
        
        if ($("form#filter").length) {
            //$('form#filter select[name=genres]').material_select();
            $('form#filter select#genres').material_select();
            $('form#filter select#networks').material_select();
            $('form#filter select#statuses').material_select();
        }

    }); // end of document ready
})(jQuery); // end of jQuery name space