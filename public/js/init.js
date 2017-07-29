

(function ($) {

    $(function () {

        //Menu
        $(".button-collapse").sideNav();
        // Initialize collapsible (uncomment the line below if you use the dropdown variation)
        $('.collapsible').collapsible();

        if ($("form#filter").length) {
            //$('form#filter select[name=genres]').material_select();
            $('form#filter select#genres').material_select();
            $('form#filter select#networks').material_select();
            $('form#filter select#statuses').material_select();
            $('form#filter select#order').material_select();
        }

        initAutoComplete({inputId: 'search', ajaxUrl: window.location.href});

        function initAutoComplete(options)
        {
            var defaults = {
                inputId: null,
                ajaxUrl: false,
                data: {}
            };

            options = $.extend(defaults, options);
            var $input = $("#" + options.inputId);

            if (options.ajaxUrl !== false)
            {
                var $autocomplete = $('<ul id="myId" class="autocomplete-content dropdown-content"></ul>'),
                        $inputDiv = $input.closest('.input-field'),
                        //timeout,
                        runningRequest = false,
                        request;

                if ($inputDiv.length) {
                    $inputDiv.append($autocomplete); // Set ul in body
                } else {
                    $input.after($autocomplete);
                }

                var highlight = function (string, $el) {
                    var img = $el.find('img');
                    var matchStart = $el.text().toLowerCase().indexOf("" + string.toLowerCase() + ""),
                            matchEnd = matchStart + string.length - 1,
                            beforeMatch = $el.text().slice(0, matchStart),
                            matchText = $el.text().slice(matchStart, matchEnd + 1),
                            afterMatch = $el.text().slice(matchEnd + 1);
                    $el.html("<span>" + beforeMatch + "<span class='highlight'>" + matchText + "</span>" + afterMatch + "</span>");
                    if (img.length) {
                        $el.prepend(img);
                    }
                };

                $autocomplete.on('click', 'li', function () {
                    var $val = $(this).text().trim();
                    $input.val($val);
                    $autocomplete.empty();
                    //trigger the change
                    $("#search").val($val).trigger("change");
                });

                $input.on('keyup', function (e) {

                    //if(timeout){ clearTimeout(timeout);}
                    if (runningRequest)
                        request.abort();

                    if (e.which === 13) {
                        $autocomplete.find('li').first().click();
                        return;
                    }

                    var val = $input.val().toLowerCase();
                    $autocomplete.empty();

                    //timeout = setTimeout(function() {

                    runningRequest = true;

                    request = $.ajax({
                        type: 'GET', // your request type
                        url: options.ajaxUrl,
                        data: {
                            query: val,
                        },
                        success: function (data) {
                            if (!$.isEmptyObject(data)) {
                                // Check if the input isn't empty
                                if (val !== '') {
                                    for (var key in data) {
                                        if (data.hasOwnProperty(key) &&
                                                key.toLowerCase().indexOf(val) !== -1 &&
                                                key.toLowerCase() !== val) {
                                            var autocompleteOption = $('<li></li>');
                                            if (!!data[key]) {
                                                autocompleteOption.append('<img src="' + data[key] + '" class="right circle"><span>' + key + '</span>');
                                            } else {
                                                autocompleteOption.append('<span>' + key + '</span>');
                                            }
                                            $autocomplete.append(autocompleteOption);

                                            highlight(val, autocompleteOption);
                                        }
                                    }
                                }
                            }
                        },
                        complete: function () {
                            runningRequest = false;
                        }
                    });
                    //},250);
                });
            }
            else
            {
                $input.autocomplete({
                    data: options.data
                });
            }
        }

        /*
         var rangeSlider = document.getElementById('runtimeSlider');
         var $slider = $('#runtimeSlider');
         noUiSlider.create(rangeSlider, {
         start: [parseInt($("#sMin").val()), parseInt($("#sMax").val())],
         connect: true,
         step: $slider.data('step'),
         range: {
         'min': parseInt($("#sMin").val()),
         'max': parseInt($("#sMax").val()),
         },
         });
         var sMin = document.getElementById('sMin');
         var sMax = document.getElementById('sMax');
         
         rangeSlider.noUiSlider.on('slide', function (values, handle) {
         
         var value = values[handle];
         if (handle) {
         sMax.value = parseInt(value);
         $("#sMax").val(sMax.value).trigger("change");
         } else {
         sMin.value = parseInt(value);
         $("#sMin").val(sMin.value).trigger("change");
         }
         
         });
         */

    }); // end of document ready
})(jQuery); // end of jQuery name space