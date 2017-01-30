import 'whatwg-fetch'
import token from 'libs/token'
import client from 'libs/client'


function request(method, url) {
    var headers = new Headers({
        'X-Requested-With': 'XMLHttpRequest',
        'Authorization': 'Bearer ' + token.get()
    })
    var req = {
        credentials: 'same-origin',
        method,
        headers
    }
    return fetch(url, req)
        .then((response) => {
            return response.json().then((data) => {
                if(response.status !== 200) {
                    if(data.error == 'access_denied') {
                        token.set(null);
                    }
                    return Promise.reject(data);
                }
                return Promise.resolve(data);
            })
        })
}


export default {

    getAuthDetails: function() {
        if(token.check()) {
            return request('GET', '/oauth_server/authorization' + window.location.search)
        } else {
            return new Promise((resolve, reject) => {
                reject({
                    error: {
                        error: 'auth_required',
                        error_description: ''
                    }
                })
            })
        }
    },

    authorize: function() {
        return request('POST', '/oauth_server/authorization/authorize' + window.location.search)
            .then(res=>client.authorize(res))
    },

    deny: function(client_details) {
        return request('POST', '/oauth_server/authorization/deny' + window.location.search)
            .then(res=>client.deny(res))
    }

}