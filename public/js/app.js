/* AJAX FORM */
$(document).on('submit', 'form.ajax', function (e) {
    e.preventDefault();
    var $frm = $(this);
    console.log($frm);
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
        window.history.pushState("", "", $el.attr('href'));
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
    //$('#filter-form').find('[name="page"]').val(1);strankovani 
    var ajaxData = $('#filter').serialize();//data do filteru
    //console.log(ajaxData);
    //$(".elm_inner_content").addClass("mod--loading");//loading...
    $.ajax({
        url: location.protocol + '//' + location.host + location.pathname,
        data: ajaxData
    }).done(function () {
        //$(".elm_inner_content").removeClass("mod--loading");//loading...
        window.history.pushState("", "", decodeURIComponent('?' + ajaxData));
    });
});
