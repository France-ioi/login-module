import 'whatwg-fetch'
import token from 'libs/token'

function getRequestBody(data) {
    var body = new FormData();
    for(var k in data) {
        if(data.hasOwnProperty(k)) {
            body.append(k, data[k])
        }
    }
    return body;
}

function post(url, data) {
    var body = getRequestBody(data)
    var headers = new Headers({
        'X-Requested-With': 'XMLHttpRequest'
    })
    var req = {
        credentials: 'same-origin',
        method: 'POST',
        headers,
        body
    }
    return fetch(url, req)
        .then((response) => {
            if((response.status >= 200 && response.status < 300) || response.status == 422) {
                return response.ok ? response.json() : response.json().then(err => Promise.reject(err))
            } else {
                return Promise.reject();
            }
        })
}


export default {

    login: function(data) {
        token.set(null);
        return post('/login', data)
            .then((res) => {
                token.set(res.access_token);
            })
    },

    resetPassword: function(data) {
        return post('/password/email', data)
    },

    newPassword: function(data) {
        return post('/password/reset', data)
    },

    registration: function(data) {
        return post('/registration', data)
            .then((res) => {
                if(res.success) {
                    token.set(res.token.access_token);
                }
            })
    }

}