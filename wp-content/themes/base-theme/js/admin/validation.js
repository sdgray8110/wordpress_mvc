var global = global || {};

jQuery.extend(global, (function() {
    var self = {
        valMessaging: '<div class="validation"><ul class="validationErrors"></span></div>',
        validate: function(data,formEl) {
            var invalid = [];

            for (var key in data) {
                var type = self.validationType(key);

                console.log(key);

                if (!self.validationRules[type](data[key]) && self.validationMessages[key]) {
                    invalid.push(key);
                }
            }

            formEl.find('.validationErrors').remove();

            if (invalid.length) {
                self.validationMessaging(invalid,formEl);
            } else {
                formEl.find('.validation').remove();
            }

            return (!invalid.length)
        },

        validationType: function(name) {
            var type = 'required';

            if (name.match('email')) {
                type = 'email';
            }

            return type;
        },

        validationMessaging: function(errors,formEl) {
            var messaging = jQuery(self.valMessaging),
                errorContainer = messaging.find('.validationErrors'),
                form = formEl.parents('form').length ? formEl.parents('form') : formEl,
                legend = formEl.find('legend');

            if (!legend.length) {
                formEl.prepend('<legend style="display:none"></legend>');
                legend = formEl.find('legend');
            }

            for (i=0;i<errors.length;i++) {
                var message = '<li><span>' + self.validationMessages[errors[i]] + '</span></li>';
                errorContainer.append(message);
            }

            formEl.find('.validation').remove();
            legend.after(messaging);

            self.revalidateForm(form,formEl);

            form.find('input,textarea').change(function() {
                var formData = form.serializeObject();
                self.validate(formData,formEl);
            });
        },

        revalidateForm: function(form,formEl) {
            if (!self.validationHandlersApplied) {
                self.validationHandlersApplied = true;

                form.find('input,textarea').on('keyup', function() {
                    var formData = form.serializeObject();
                    self.validate(formData,formEl);
                });

                form.find('select').on('change', function() {
                    var formData = form.serializeObject();
                    self.validate(formData,formEl);
                });
            }
        },

        validationRules: {
            email: function(value) {
                var regex = /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
                return regex.test(value);
            },

            required: function(value) {
                return jQuery.trim(value) != '';
            }
        },

        validationMessages: {
            'contact_name': 'Please enter your name',
            'contact_email': 'Please enter a valid email address.',
            'contact_subject': 'Please enter a subject',
            'contact_message': 'Please enter your message',
            'join_email': 'Please enter a valid email address.',
            'agree-to-terms': 'You must agree to the terms to continue'
        }
    };

    return self;
})(window));