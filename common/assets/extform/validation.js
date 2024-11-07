yii.validationext = (function ($) {
    var pub = {
        isEmpty: function (value) {
            return value === null || value === undefined || value == [] || value === '';
        },

        isEmptyNumeric: function (value) {
            if(value === null || value === undefined || value == [] || value === '') return true;

            var tmp = parseFloat(value);
            if(isNaN(tmp)) tmp = 0;
            if(tmp == 0) return true;

            return false;
        },

        addMessage: function (messages, message, value) {
            messages.push(message.replace(/\{value\}/g, value));
        },

        requiredNumeric: function (value, messages, options) {
            var valid = false;
            if (options.requiredValue === undefined) {
                var isString = typeof value == 'string' || value instanceof String;
                if (options.strict && value !== undefined || !options.strict && !pub.isEmptyNumeric(isString ? $.trim(value) : value)) {
                    valid = true;
                }
            } else if (!options.strict && value == options.requiredValue || options.strict && value === options.requiredValue) {
                valid = true;
            }

            if (!valid) {
                pub.addMessage(messages, options.message, value);
            }
        },
    };
    return pub;
})(jQuery);
