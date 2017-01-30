import storage from './cookie_storage'

var key = 'token';

var token = {

    set: function(token) {
        if(token) {
            storage.set(key, token);
        } else {
            storage.remove(key);
        }
    },

    get: function() {
        return storage.get(key);
    },

    check: function() {
        return !!this.get();
    }

}


var h = window.location.hash.substr(1);
if(h.indexOf('token=') !== -1) {
    var t = h.split('=');
    token.set(t[1]);
    window.location.hash = '';
}


export default token