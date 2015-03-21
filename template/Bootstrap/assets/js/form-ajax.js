
var FormHandler = {
    state : {
        useAjax: true,
        resendForm: false,
        isLoading: false
    },
    dom: {
        form: null,
        btn: false,
        callback: false
    },
    prototype: {
        beforeCall: [],
        afterCall: []
    },
    debug: {
        mode: false,
        lastResponse: null,
        enable: function(){
            this.mode = true;
            console.log('Debbuging enabled !');
            return true;
        },
        disable: function(){
            this.mode = false;
            console.log('Debbuging disabled !');
            return false;
        },
        getLastResponse: function() {
            console.log(this.lastResponse);
        },
        log: function(string) {
            if (FormHandler.debug.mode)
                console.log(string);
        }
    }
}

$('form.use-ajax').submit(function(e) {
    if (FormHandler.state.useAjax == false || FormHandler.state.isLoading == true) {
        return true;
    }
    FormHandler.state.isLoading = true;
    FormHandler.dom.form = $(this);
    FormHandler.dom.btn = FormHandler.dom.form.find('button[type="submit"]');

    var callback = FormHandler.dom.form.data('callback');
    if (typeof callback === 'string') {
        callback.split('.').forEach(function(i) {
            if (fn == false) {
                fn = window;
            }
            fn = fn[i];
        });
    } else {
        fn = function(){};
    }
    FormHandler.dom.callback = fn;

    if (typeof(FormHandler.dom.btn.data('loading-text')) != "undefined") {
        FormHandler.dom.btn.button('loading');
    }
    if (FormHandler.dom.btn.data('toggle') == "tooltip") {
        FormHandler.dom.btn.tooltip('hide');
    }

    FormHandler.prototype.beforeCall.forEach(function(fn){
        fn(FormHandler.dom.form, FormHandler.dom.btn);
    });

    FormHandler.debug.log('Starting AJAX request..');

    $.ajax({
        type: "POST",
        url: FormHandler.dom.form.attr('action') || window.location.href,
        data: FormHandler.dom.form.serialize()+'&ajax=1',
        success: function(json) {
            FormHandler.debug.log('Get a response from server');
            FormHandler.debug.lastResponse = json;
            try {
                var r = JSON && JSON.parse(json) || $.parseJSON(json);
                if (r.type == 'success') {
                    var type = 'success';
                } else {
                    var type = 'danger';
                }
                var currentAlert = $('#alert div');
                if (currentAlert.height() != null) {
                    currentAlert.css('width', currentAlert.outerWidth())
                                .css('position', 'absolute')
                                .css('transition', 'all .2s')
                                .css('margin-top', '-50px')
                                .css('opacity', '0');
                    setTimeout(function(){
                        $('#alert div:first').remove()
                    }, 200);
                }
                $('#alert').append('<div class="alert alert-'+type+' fade in" style="position: relative; top: 50px; transition: all .2s;opacity:0;">'
                                  +'<button class="close" type="button" data-dismiss="alert"><span aria-hidden="true">×</span></button>'
                                  +r.message
                                  +'</div>');
                setTimeout(function(){
                    $('#alert div:last').css('top', '0px');$('#alert div:last').css('opacity', '1');
                }, 100);
                if (typeof(r.location) != "undefined") {
                    if (FormHandler.debug.mode)
                        FormHandler.debug.log('[Cancelled] Redirecting to: '+r.location);
                    else
                        setTimeout(function(){
                            if (r.location === '$')
                                window.location.reload(true);
                            else
                                window.location.href = r.location;
                        }, 1500);
                }
                FormHandler.dom.callback(true, FormHandler.dom.form, FormHandler.dom.btn);
                FormHandler.debug.log('Finished request: Success');
            } catch(e) {
                if (FormHandler.debug.mode) {
                    FormHandler.debug.log('Finished request: Error while processing ('+e+')');
                    FormHandler.debug.log('[Cancelled] Form resending');
                } else {
                    FormHandler.state.ajax = false;
                    if (!FormHandler.state.resendForm && FormHandler.dom.form != null) {
                        FormHandler.dom.form.submit();
                        FormHandler.state.resendForm = true;
                    }
                }
                FormHandler.dom.callback(false, FormHandler.dom.form, FormHandler.dom.btn);
            }
        },
        error: function(data) {
            FormHandler.debug.lastResponse = data;
            var currentAlert = $('#alert div');
            if (currentAlert.height() != null) {
                currentAlert.css('width', currentAlert.outerWidth())
                            .css('position', 'absolute')
                            .css('transition', 'all .2s')
                            .css('margin-top', '-50px')
                            .css('opacity', '0');
                setTimeout(function(){
                    $('#alert div:first').remove()
                }, 200);
            }
            $('#alert').append('<div class="alert alert-danger fade in" style="position: relative; top: 50px; transition: all .2s;opacity:0;">'
                              +'<button class="close" type="button" data-dismiss="alert"><span aria-hidden="true">×</span></button>'
                              +'An internal error occured..'
                              +'</div>');
            FormHandler.state.ajax = false
            FormHandler.dom.callback(false, FormHandler.dom.form, FormHandler.dom.btn);
            FormHandler.debug.log('Finished request: Server returned error');
        },
        complete: function() {
            if (typeof(FormHandler.dom.btn.data('loading-text')) != "undefined") {
                FormHandler.dom.btn.button('reset');
            }
            FormHandler.state.isLoading = false;
            FormHandler.prototype.afterCall.forEach(function(fn){
                fn(FormHandler.dom.form, FormHandler.dom.btn);
            });
            FormHandler.debug.log('Request ended.');
        }
    });
    e.preventDefault();
    return false;
});