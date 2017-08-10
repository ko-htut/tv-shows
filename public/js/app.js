/* AJAX FORM */
$(document).on('submit', 'form.ajax', function (e) {
    e.preventDefault();
    $(':input[value=""]').attr('disabled', true);
    var $frm = $(this);
    $.ajax({
        method: $frm.attr('method'),
        url: $frm.attr('action'),
        data: $frm.serialize()
    });
});


/* AJAX LINKS*/
$(document).on('click', 'a.ajax, div.ajax', function (e) {
    e.preventDefault();
    var $el = $(this);
    $.ajax({
        url: $el.attr('href')
    }).done(function () {

        if ($el.data('history') != false) {
            window.history.pushState("", "", $el.attr('href'));
        }

        //Updateing dates to users format

        var $dates = $('[data-date]');
        $($dates).each(function () {
            var $el = $(this);
            var $mTime = new Date($el.data('date'));
            $el.text($mTime.toLocaleDateString());
        });
    

    });
});

/* GLOBAL AJAX EVENT HANDLER */
$.ajaxSetup({
    success: function (payload) {
        if (payload) {
            if (payload.alert) {
                alert(payload.alert);
            }
            if (payload.snippets) {
                for (var i in payload.snippets) {
                    $("#" + i).replaceWith(payload.snippets[i]);
                }
            }
            if (payload.redirect) {
                if (payload.redirect == 'this') {
                    location.href = window.location.href;
                } else {
                    location.href = payload.redirect;
                }
            }
        }
    },
    dataType: "json"
});


//Filter
$(document).on('change', 'form#filter select, form#filter input', function (e) {

    e.preventDefault();
    $(':input[value=""]').attr('disabled', true);
    $('#filter').find('[name="page"]').val(1);
    var ajaxData = $('#filter').serialize();//data do filteru
    $(':input[value=""]').attr('disabled', false);
    //console.log(ajaxData);
    $("#preloader").removeClass("hide");//loading...
    $.ajax({
        url: location.protocol + '//' + location.host + location.pathname,
        data: ajaxData
    }).done(function () {
        $("#preloader").addClass("hide");//loading...
        window.history.pushState("", "", decodeURIComponent('?' + ajaxData));
    });
});



$(document).on('click', '[data-next]', function (e) {
    e.preventDefault();
    $(this).addClass("loading");
    $(':input[value=""]').attr('disabled', true);
    $('#filter').find('[name="page"]').val(parseInt($('#filter').find('[name="page"]').val()) + 1);

    var ajaxData = $('#filter').serialize();
    $(':input[value=""]').attr('disabled', false);
    $.ajax({
        url: location.protocol + '//' + location.host + location.pathname,
        data: ajaxData
    }).done(function () {
        $(this).removeClass("loading");
        window.history.pushState("", "", decodeURIComponent('?' + ajaxData));
    });
});

