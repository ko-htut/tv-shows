

(function ($) {

    $(function () {

        //Menu
        $(".button-collapse").sideNav();
        // Initialize collapsible (uncomment the line below if you use the dropdown variation)
        $('.collapsible').collapsible();

        $('textarea#comment').characterCounter();
        $('textarea#about').characterCounter();

        $('select#gender').material_select();

        $('.birthdaypicker').pickadate({
            selectMonths: true, // Creates a dropdown to control month
            selectYears: 90, // Creates a dropdown of 15 years to control year,
            today: 'Today',
            clear: 'Clear',
            close: 'Ok',
            closeOnSelect: true, // Close upon selecting a date,
            format: 'yyyy-mm-dd', //submited format
            max: true, //Only past dates
        });

        //Paralax
        $('.parallax').parallax();
        
         $('.navigate').tooltip({delay: 50});
         
        if ($("form#filter").length) {
            //$('form#filter select[name=genres]').material_select();
            $('form#filter select#genres').material_select();
            $('form#filter select#networks').material_select();
            $('form#filter select#statuses').material_select();
            $('form#filter select#order').material_select();
        }

        //Galerie
        $('#gallery').lightGallery({
            thumbnail: true,
            animateThumb: false,
            showThumbByDefault: false
        });
        $("#gallery a").hide().filter(":first-child").show();

        $('#gallery').lightGallery({
            thumbnail: true,
            animateThumb: false,
            showThumbByDefault: false
        });
        $("#gallery a").hide().filter(":first-child").show();

        //Data
        var $dates = $('[data-date]');
        $($dates).each(function () {
            var $el = $(this);
            var $mTime = new Date($el.data('date'));
            $el.text($mTime.toLocaleDateString());
        });


        //Autocomplete
        var options = {
            url: function (q) {

                var $lang = $('html').attr('lang');
                return "/search/" + "?" + "q=" + q + "&lang=" + $lang;

            },
            getValue: function (element) {
                return element.name;
            },
            ajaxSettings: {
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: "json",
                method: "POST",
                //cache: false,
                data: {
                    dataType: "json",
                    //cache: false
                }
            },
            preparePostData: function (data) {
                data.phrase = $("#search-input").val();
                return data;
            },
            template: {
                type: "iconRight",
                fields: {
                    iconSrc: "icon"
                }
            },
            list: {
                showAnimation: {
                    type: "slide"
                },
                hideAnimation: {
                    type: "slide"
                },
                onClickEvent: function () {
                    var $url = $("#search-input").getSelectedItemData().url;
                    if ($url !== null) {
                        window.location.href = $url;
                    }
                }
            },
            requestDelay: 400

        };

        $("#search-input").easyAutocomplete(options);






        /*
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
         */



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