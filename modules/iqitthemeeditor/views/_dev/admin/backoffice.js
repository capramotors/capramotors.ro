import './lib/bootstrap-colorpicker/js/bootstrap-colorpicker.min';
import './lib/jquery.serializejson/jquery.serializejson';
import './lib/prismjs/js/prism';
function CodeFlask(){}CodeFlask.prototype.run=function(a,b){var c=document.querySelectorAll(a);if(c.length>1)throw"CodeFlask.js ERROR: run() expects only one element, "+c.length+" given. Use .runAll() instead.";this.scaffold(c[0],!1,b)},CodeFlask.prototype.runAll=function(a,b){this.update=null,this.onUpdate=null;var d,c=document.querySelectorAll(a);for(d=0;d<c.length;d++)this.scaffold(c[d],!0,b)},CodeFlask.prototype.scaffold=function(a,b,c){var d=document.createElement("TEXTAREA"),e=document.createElement("PRE"),f=document.createElement("CODE"),g=a.textContent;1==!c.enableAutocorrect&&(d.setAttribute("spellcheck","false"),d.setAttribute("name",a.id),d.setAttribute("autocapitalize","off"),d.setAttribute("autocomplete","off"),d.setAttribute("autocorrect","off")),c.language=this.handleLanguage(c.language),this.defaultLanguage=a.dataset.language||c.language||"markup",b||(this.textarea=d,this.highlightCode=f),a.classList.add("CodeFlask"),d.classList.add("CodeFlask__textarea"),e.classList.add("CodeFlask__pre"),f.classList.add("CodeFlask__code"),f.classList.add("language-"+this.defaultLanguage),/iPad|iPhone|iPod/.test(navigator.platform)&&(f.style.paddingLeft="3px"),a.innerHTML="",a.appendChild(d),a.appendChild(e),e.appendChild(f),d.value=g,this.renderOutput(f,d),Prism.highlightAll(),this.handleInput(d,f,e),this.handleScroll(d,e)},CodeFlask.prototype.renderOutput=function(a,b){a.innerHTML=b.value.replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;")+"\n"},CodeFlask.prototype.handleInput=function(a,b,c){var e,f,g,d=this;a.addEventListener("input",function(a){e=this,d.renderOutput(b,e),Prism.highlightAll()}),a.addEventListener("keydown",function(a){e=this,f=e.selectionStart,g=e.value,9===a.keyCode&&(e.value=g.substring(0,f)+"    "+g.substring(f,e.value.length),e.selectionStart=f+4,e.selectionEnd=f+4,a.preventDefault(),b.innerHTML=e.value.replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;")+"\n",Prism.highlightAll())})},CodeFlask.prototype.handleScroll=function(a,b){a.addEventListener("scroll",function(){var a=Math.floor(this.scrollTop);navigator.userAgent.toLowerCase().indexOf("firefox")<0&&(this.scrollTop=a),b.style.top="-"+a+"px"})},CodeFlask.prototype.handleLanguage=function(a){return a.match(/html|xml|xhtml|svg/)?"markup":a.match(/js/)?"javascript":a},CodeFlask.prototype.onUpdate=function(a){if("function"!=typeof a)throw"CodeFlask.js ERROR: onUpdate() expects function, "+typeof a+" given instead.";this.textarea.addEventListener("input",function(b){a(this.value)})},CodeFlask.prototype.update=function(a){var b=document.createEvent("HTMLEvents");this.textarea.value=a,this.renderOutput(this.highlightCode,this.textarea),Prism.highlightAll(),b.initEvent("input",!1,!0),this.textarea.dispatchEvent(b)};

var iqitBoxShadowGenerator;

$( document ).ready( function () {

    var $boxShadowGenerators = $('.js-box-shadow-generator');
    var $borderGenerators = $('.js-border-generator');
    var $typographyGenerators = $('.js-typography-generator');
    var $configurationForm = $('#configuration_form');
    var iqitBaseUrl = $('#iqit-base-url').val();


    $('#iqit-config-tabs').on('click', 'a[data-toggle="tab"]', function (e) {
        e.preventDefault();
        $('#iqit-config-tabs').find('a[data-toggle="tab"]').parent().removeClass('active');
    });

    $('#iqit-config-tabs').on('click', '.parent-tab', function (e) {
        e.preventDefault();
        var $link = $(this);

        if ($link.parent().find('ul').length) {
            var $firstChild = $link.parent().find('ul').find('a[data-toggle="tab"]').first();

            $('#iqit-config-tabs').find('.parent-tab').parent().removeClass('active');
            $link.parent().addClass('active');

            $link.parent().addClass('active');
            $firstChild.click();
            $firstChild.tab('show');
        }
    });

    //submit
    $configurationForm.submit(function( event ) {
        $configurationForm.trigger( 'beforeSubmit', [] );
    });

    $configurationForm.on( 'beforeSubmit', function( event ) {

        $boxShadowGenerators.each(function () {
            var $controls = $(this).find('.js-shadow-color, .js-box-shadow-switch, .js-shadow-blur, .js-shadow-spread, .js-shadow-horizontal, .js-shadow-vertical');
            $(this).find('.js-box-shadow-input').first().val( encodeURIComponent( JSON.stringify($controls.serializeToJSON() ) ));
        });

        $borderGenerators.each(function () {
            var $controls = $(this).find('.js-border-color, .js-border-type, .js-border-width');
            $(this).find('.js-border-input').first().val( encodeURIComponent( JSON.stringify($controls.serializeToJSON() ) ));
        });

        $typographyGenerators.each(function () {
            var $controls = $(this).find('.js-font-size, .js-font-spacing, .js-font-bold, .js-font-italic, .js-font-uppercase');
            $(this).find('.js-font-input').first().val( encodeURIComponent( JSON.stringify($controls.serializeToJSON() ) ));
        });
    });

    $borderGenerators.each(function () {

        var $controls = $(this).find('.js-border-type, .js-border-width');

        $controls.on('change input', function (e) {
            if ( $(this).data('name') == 'type' ) {
                if ($(this).val() != 'none') {
                    $(this).parents('.js-border-generator ').first().find('.js-border-controls-wrapper').addClass('visible-inline-option');
                } else {
                    $(this).parents('.js-border-generator ').first().find('.js-border-controls-wrapper').removeClass('visible-inline-option');
                }
            }

            var self = this;
            if ($(this).data('timeout')) {
                clearTimeout($(this).data('timeout'));
            }
            $(this).data('timeout', setTimeout(function() {
                $(self).parents('.js-border-generator').first().find('.js-border-input').change();
            }, 50));
        });
        $(this).find('.js-border-color').on('keydown', function (e) {
            var self = this;
            if ($(this).data('timeout')) {
                clearTimeout($(this).data('timeout'));
            }
            $(this).data('timeout', setTimeout(function() {
                $(self).parents('.js-border-generator').first().find('.js-border-input').change();
            }, 50));
        });
    });

    $typographyGenerators.each(function () {
        var $controls = $(this).find('.js-font-size, .js-font-spacing, .js-font-bold, .js-font-italic, .js-font-uppercase');
        var $field =  $(this).find('.js-font-input').first();


        $controls.on('change input', function() {
            $field.val( encodeURIComponent( JSON.stringify($controls.serializeToJSON() ) ));
            $field.change();
        });
    });

    // new colorpicker
    $('.colorpicker-component').colorpicker().on('changeColor', function(e) {
       $(this).find('input').keydown();
    });

    $('.js-range-slider').on('input', function(e) {
        $('#' + $(this).data('vinput')).change();
    });

    //filemanager iframe
    $('.js-iframe-upload').fancybox({
        'width'		: 900,
        'height'	: 600,
        'type'		: 'iframe',
        'autoScale' : false,
        'autoDimensions': false,
        'fitToView' : false,
        'autoSize' : false,
        onUpdate : function() {
            let $linkImage = $('.fancybox-iframe').contents().find('a.link');
            let inputName = $(this.element).data('input-name');
            $linkImage.data('field_id', inputName);
            $linkImage.attr('data-field_id', inputName);
        },
        afterShow: function(){
            let $linkImage = $('.fancybox-iframe').contents().find('a.link');
            let inputName = $(this.element).data('input-name');
            $linkImage.data('field_id', inputName);
            $linkImage.attr('data-field_id', inputName);
        },
        beforeClose: function() {
            let $input = $('#' + $(this.element).data("input-name"));
            let val = $input.val();

            $input.val(val.replace(iqitBaseUrl, ""));
            $input.change();
        }
    });

    // field condition
    $('#configuration_form').find('.condition-option').each(function () {

        let $field =  $(this);
        let condition =  $(this).data('condition');

        $.each( condition, function( input, value ) {
            let parsedValue = value.match( /(\w+)(?:\[(\w+)])?/gi ),
                conditionValue;
            let conditionOperator = value.match( /(\!=|<=|==)(?:\[(\w+)])?/gi )[0];
            let $checker = $('input[name=' + input +'], select[name=' + input +']');
            let checkerVal = $checker.val();

            if ($checker.attr('type') == 'radio'){
                checkerVal = $('input[name=' + input +']:checked').val();
            }

            if(parsedValue){
                conditionValue = parsedValue[0];
            }
            else{
                conditionValue = '';
            }

            if (conditionOperator ==  '<='){
                conditionValue = parsedValue;
            }

            if (iqitConditionCheck(checkerVal, conditionValue, conditionOperator)) {
                $field.addClass('visible-option');
            }
            else{
                $field.removeClass('visible-option');
            }
            $checker.on('change input', function() {
                if (iqitConditionCheck(this.value, conditionValue, conditionOperator)) {
                    $field.addClass('visible-option');
                }
                else{
                    $field.removeClass('visible-option');
                }
            });
        });
    });
    function iqitConditionCheck (  leftValue, rightValue, operator ) {
        switch ( operator ) {
            case '==':
                return leftValue == rightValue;
            case '!=':
                return leftValue != rightValue;
            case '<=':
                if (jQuery.inArray( leftValue , rightValue ) == -1){
                    return false;
                }
                else{
                    return true;
                }
            default:
                return leftValue === rightValue;
        }
    }


    //boxshadow
    iqitBoxShadowGenerator = (function() {

        function init() {
            $boxShadowGenerators.each(function () {

                let $generator = $(this),
                    $input = $generator.find('.js-box-shadow-input'),
                    $colorControl = $generator.find('.js-shadow-color'),
                    $controls = $generator.find(' .js-shadow-blur, .js-shadow-spread, .js-shadow-horizontal, .js-shadow-vertical'),
                    $switch = $generator.find('.js-box-shadow-switch'),
                    $controlsWrapper = $generator.find('.js-box-shadow-controls');

                setShadow( $generator );

                if ($switch.val() == 1) {
                    $controlsWrapper.addClass('visible-option');
                }
                else{
                    $controlsWrapper.removeClass('visible-option');
                }

                $colorControl.keydown(function() {
                    setShadow( $generator );
                    $input.change();
                });

                $controls.on('input', function() {
                    setShadow( $generator );
                    $input.change();
                });

                $switch.change(function() {
                    if (this.value == 1) {
                        $controlsWrapper.addClass('visible-option');
                    }
                    else{
                        $controlsWrapper.removeClass('visible-option');
                    }
                    $input.change();
                })

            });
        }

        function setShadow(  $generator ) {

            let color = $generator.find('.js-shadow-color').val(),
                blur = $generator.find('.js-shadow-blur').val(),
                spread = $generator.find('.js-shadow-spread').val(),
                horizontal = $generator.find('.js-shadow-horizontal').val(),
                vertical = $generator.find('.js-shadow-vertical').val(),
                $preview = $generator.find('.js-shadow-preview'),
                shdw = '';

            shdw += horizontal + 'px ' + vertical + 'px ' + blur + 'px ' + spread + 'px ' + color;
            $preview.css('box-shadow', shdw);
            $preview.html('box-shadow: ' + shdw);
        }

        return { init : init };

    })();
    iqitBoxShadowGenerator.init();

    //codes editor
    var flask = new CodeFlask;
    flask.run('#codes_css', { language: 'css' });
    flask.onUpdate(function(code) {
        $('#codes_css').trigger( 'cssCodeChanged', code );
    });
    flask.run('#codes_js', { language: 'javascript' });

});



