import Cookie from 'js-cookie'

var options = {
    path: ''
}

export default {

    get: function(key) {
        return Cookie.get(key, options);
    },

    set: function(key, value) {
        Cookie.set(key, value, options);
    },

    remove: function(key) {
        Cookie.remove(key, options);
    }
}