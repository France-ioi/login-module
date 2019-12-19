module.exports = function(config) {
    var regex_test = eval(config.new);
    var regex_filter = eval(config.filter);

    return {
        test: function(str) {
            return regex_test.test(str) && str.length >= config.length.min && str.length <= config.length.max;
        },

        sanitise: function(str) {
            return str.toLowerCase().replace(regex_filter, '');
        }
    }
}
