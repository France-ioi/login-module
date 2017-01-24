(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
'use strict';

require('libs/bootstrap');

require('libs/token');

var _react = require('react');

var _react2 = _interopRequireDefault(_react);

var _reactDom = require('react-dom');

var _reactDom2 = _interopRequireDefault(_reactDom);

var _reactRouter = require('react-router');

var _history = require('libs/history');

var _layout = require('modules/layout');

var _layout2 = _interopRequireDefault(_layout);

var _auth = require('modules/auth');

var _auth2 = _interopRequireDefault(_auth);

var _authorization = require('modules/authorization');

var _authorization2 = _interopRequireDefault(_authorization);

var _error_pages = require('modules/error_pages');

var _error_pages2 = _interopRequireDefault(_error_pages);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

_reactDom2.default.render(_react2.default.createElement(
    _reactRouter.Router,
    { history: (0, _history.getHistory)() },
    _react2.default.createElement(
        _reactRouter.Route,
        { path: '/', component: _layout2.default.components.Layout },
        _react2.default.createElement(
            _reactRouter.Route,
            { component: _auth2.default.components.Authenticated },
            _authorization2.default.routes
        ),
        _error_pages2.default.routes
    )
), document.getElementById('app'));

},{"libs/bootstrap":5,"libs/history":8,"libs/token":9,"modules/auth":20,"modules/authorization":23,"modules/error_pages":27,"modules/layout":30,"react":"react","react-dom":"react-dom","react-router":"react-router"}],2:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.Panel = exports.Loader = undefined;

var _loader = require('./loader');

var _loader2 = _interopRequireDefault(_loader);

var _panel = require('./panel');

var _panel2 = _interopRequireDefault(_panel);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.Loader = _loader2.default;
exports.Panel = _panel2.default;

},{"./loader":3,"./panel":4}],3:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _react = require("react");

var _react2 = _interopRequireDefault(_react);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = _react2.default.createClass({
    displayName: "loader",


    render: function render() {
        return this.props.is_fetching ? _react2.default.createElement(
            "div",
            { className: "loading" },
            "Please wait..."
        ) : this.props.children;
    }

});

},{"react":"react"}],4:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _react = require("react");

var _react2 = _interopRequireDefault(_react);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = _react2.default.createClass({
    displayName: "panel",


    render: function render() {
        return _react2.default.createElement(
            "div",
            { className: "panel panel-default" },
            this.props.title && _react2.default.createElement(
                "div",
                { className: "panel-heading" },
                this.props.title
            ),
            _react2.default.createElement(
                "div",
                { className: "panel-body" },
                this.props.children
            )
        );
    }
});

},{"react":"react"}],5:[function(require,module,exports){
'use strict';

var _promisePolyfill = require('promise-polyfill');

var _promisePolyfill2 = _interopRequireDefault(_promisePolyfill);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

if (!window.Promise) {
    window.Promise = _promisePolyfill2.default;
}

},{"promise-polyfill":"promise-polyfill"}],6:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _jschannel = require('jschannel');

var _jschannel2 = _interopRequireDefault(_jschannel);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var ChannelClient = function ChannelClient() {
    var chan = _jschannel2.default.build({
        window: window.opener,
        origin: '*',
        scope: 'ioi_login'
    });

    this.authorize = function (auth) {
        chan.call({
            method: 'authorize',
            params: auth,
            success: function success() {},
            error: function error(err) {
                return console.error(err);
            }
        });
    };

    this.deny = function (auth) {
        chan.call({
            method: 'deny',
            params: auth,
            success: function success() {},
            error: function error(err) {
                return console.error(err);
            }
        });
    };
};

var RedirectClient = function RedirectClient() {

    this.authorize = function (auth) {
        if (auth.redirect_uri) {
            window.location.href = auth.redirect_uri;
        }
    };

    this.deny = function (auth) {
        if (auth.redirect_uri) {
            window.location.href = auth.redirect_uri;
        }
    };
};

var client = window.opener ? new ChannelClient() : new RedirectClient();

exports.default = client;

},{"jschannel":"jschannel"}],7:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _jsCookie = require('js-cookie');

var _jsCookie2 = _interopRequireDefault(_jsCookie);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var options = {
    path: ''
};

exports.default = {

    get: function get(key) {
        return _jsCookie2.default.get(key, options);
    },

    set: function set(key, value) {
        _jsCookie2.default.set(key, value, options);
    },

    remove: function remove(key) {
        _jsCookie2.default.remove(key, options);
    }
};

},{"js-cookie":"js-cookie"}],8:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.getHistory = getHistory;

var _reactRouter = require('react-router');

var _history = require('history');

var history = (0, _reactRouter.useRouterHistory)(_history.createHistory)({ basename: '/' });

function getHistory() {
    return history;
}

},{"history":"history","react-router":"react-router"}],9:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _cookie_storage = require('./cookie_storage');

var _cookie_storage2 = _interopRequireDefault(_cookie_storage);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var key = 'token';

var token = {

    set: function set(token) {
        if (token) {
            _cookie_storage2.default.set(key, token);
        } else {
            _cookie_storage2.default.remove(key);
        }
    },

    get: function get() {
        return _cookie_storage2.default.get(key);
    },

    check: function check() {
        return !!this.get();
    }

};

var h = window.location.hash.substr(1);
if (h.indexOf('token=') !== -1) {
    var t = h.split('=');
    token.set(t[1]);
    window.location.hash = '';
}

exports.default = token;

},{"./cookie_storage":7}],10:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

require('whatwg-fetch');

var _token = require('libs/token');

var _token2 = _interopRequireDefault(_token);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function getRequestBody(data) {
    var body = new FormData();
    for (var k in data) {
        if (data.hasOwnProperty(k)) {
            body.append(k, data[k]);
        }
    }
    return body;
}

function post(url, data) {
    var body = getRequestBody(data);
    var headers = new Headers({
        'X-Requested-With': 'XMLHttpRequest'
    });
    var req = {
        credentials: 'same-origin',
        method: 'POST',
        headers: headers,
        body: body
    };
    return fetch(url, req).then(function (response) {
        if (response.status >= 200 && response.status < 300 || response.status == 422) {
            return response.ok ? response.json() : response.json().then(function (err) {
                return Promise.reject(err);
            });
        } else {
            return Promise.reject();
        }
    });
}

exports.default = {

    login: function login(data) {
        _token2.default.set(null);
        return post('/login', data).then(function (res) {
            _token2.default.set(res.access_token);
        });
    },

    resetPassword: function resetPassword(data) {
        return post('/password/email', data);
    },

    newPassword: function newPassword(data) {
        return post('/password/reset', data);
    },

    registration: function registration(data) {
        return post('/registration', data).then(function (res) {
            if (res.success) {
                _token2.default.set(res.token.access_token);
            }
        });
    }

};

},{"libs/token":9,"whatwg-fetch":"whatwg-fetch"}],11:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _react = require('react');

var _react2 = _interopRequireDefault(_react);

var _token = require('libs/token');

var _token2 = _interopRequireDefault(_token);

var _section = require('./login/section');

var _section2 = _interopRequireDefault(_section);

var _section3 = require('./reset_password/section');

var _section4 = _interopRequireDefault(_section3);

var _section5 = require('./new_password/section');

var _section6 = _interopRequireDefault(_section5);

var _section7 = require('./registration/section');

var _section8 = _interopRequireDefault(_section7);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var sections = {
    Login: _section2.default,
    ResetPassword: _section4.default,
    NewPassword: _section6.default,
    Registration: _section8.default
};

exports.default = _react2.default.createClass({
    displayName: 'authenticated',


    getInitialState: function getInitialState() {
        return {
            is_logged: _token2.default.check(),
            section: 'Login'
        };
    },

    setSection: function setSection(section) {
        this.setState({ section: section });
    },

    setLogged: function setLogged(is_logged) {
        this.setState({ is_logged: is_logged });
    },

    render: function render() {
        if (this.state.is_logged) {
            return this.props.children;
        }
        return _react2.default.createElement(sections[this.state.section], { setSection: this.setSection, setLogged: this.setLogged });
    }

});

},{"./login/section":13,"./new_password/section":15,"./registration/section":17,"./reset_password/section":19,"libs/token":9,"react":"react"}],12:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _react = require("react");

var _react2 = _interopRequireDefault(_react);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = _react2.default.createClass({
    displayName: "form",


    render: function render() {
        var _this = this;

        return _react2.default.createElement(
            "form",
            { onSubmit: this.props.submit },
            _react2.default.createElement(
                "div",
                { className: "form-group" },
                _react2.default.createElement(
                    "label",
                    null,
                    "Email"
                ),
                _react2.default.createElement("input", { type: "email", className: "form-control", value: this.props.data.email, onChange: function onChange(e) {
                        return _this.props.setData({ email: e.target.value });
                    } }),
                this.props.errors.email && _react2.default.createElement(
                    "span",
                    { className: "error" },
                    this.props.errors.email
                )
            ),
            _react2.default.createElement(
                "div",
                { className: "form-group" },
                _react2.default.createElement(
                    "label",
                    null,
                    "Password"
                ),
                _react2.default.createElement("input", { type: "password", className: "form-control", value: this.props.data.password, onChange: function onChange(e) {
                        return _this.props.setData({ password: e.target.value });
                    } }),
                this.props.errors.password && _react2.default.createElement(
                    "span",
                    { className: "error" },
                    this.props.errors.password
                )
            ),
            _react2.default.createElement(
                "button",
                { className: "btn btn-block btn-primary", type: "submit" },
                "Login"
            )
        );
    }
});

},{"react":"react"}],13:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _react = require('react');

var _react2 = _interopRequireDefault(_react);

var _components = require('components');

var _form = require('./form');

var _form2 = _interopRequireDefault(_form);

var _api = require('../../api');

var _api2 = _interopRequireDefault(_api);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = _react2.default.createClass({
    displayName: 'section',


    getInitialState: function getInitialState() {
        return {
            data: {
                email: '',
                password: ''
            },
            errors: {},
            is_fetching: false
        };
    },

    setData: function setData(data) {
        var data = Object.assign({}, this.state.data, data);
        this.setState({ data: data });
    },

    submit: function submit(e) {
        var _this = this;

        e.preventDefault();
        this.setState({ is_fetching: true }, function () {
            _api2.default.login(_this.state.data).then(function () {
                _this.setState({ is_fetching: false });
                _this.props.setLogged(true);
            }).catch(function (errors) {
                _this.setState({
                    is_fetching: false,
                    errors: errors
                });
            });
        });
    },

    render: function render() {
        var _this2 = this;

        return _react2.default.createElement(
            _components.Loader,
            { is_fetching: this.state.is_fetching },
            _react2.default.createElement(
                'div',
                null,
                _react2.default.createElement(
                    _components.Panel,
                    { title: 'Login' },
                    _react2.default.createElement(_form2.default, { values: this.state.values, setData: this.setData, data: this.state.data, errors: this.state.errors, onSubmit: this.submit })
                ),
                _react2.default.createElement(
                    _components.Panel,
                    null,
                    _react2.default.createElement(
                        'div',
                        { className: 'row' },
                        _react2.default.createElement(
                            'div',
                            { className: 'col-xs-6' },
                            _react2.default.createElement(
                                'button',
                                { className: 'btn btn-block btn-primary', onClick: function onClick() {
                                        return _this2.props.setSection('Registration');
                                    } },
                                'Create account'
                            )
                        ),
                        _react2.default.createElement(
                            'div',
                            { className: 'col-xs-6' },
                            _react2.default.createElement(
                                'button',
                                { className: 'btn btn-block btn-primary', onClick: function onClick() {
                                        return _this2.props.setSection('ResetPassword');
                                    } },
                                'Reset password'
                            )
                        )
                    )
                ),
                _react2.default.createElement(
                    _components.Panel,
                    { title: 'Or sign up with' },
                    _react2.default.createElement(
                        'div',
                        { className: 'row' },
                        _react2.default.createElement(
                            'div',
                            { className: 'col-xs-6' },
                            _react2.default.createElement(
                                'a',
                                { className: 'btn btn-block btn-default', href: '/oauth_client/redirect/facebook' + window.location.search, onClick: function onClick() {
                                        return _this2.setState({ is_fetching: true });
                                    } },
                                'Facebook'
                            )
                        ),
                        _react2.default.createElement(
                            'div',
                            { className: 'col-xs-6' },
                            _react2.default.createElement(
                                'a',
                                { className: 'btn btn-block btn-default', href: '/oauth_client/redirect/google' + window.location.search, onClick: function onClick() {
                                        return _this2.setState({ is_fetching: true });
                                    } },
                                'Google'
                            )
                        )
                    )
                )
            )
        );
    }

});

},{"../../api":10,"./form":12,"components":2,"react":"react"}],14:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _react = require("react");

var _react2 = _interopRequireDefault(_react);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = _react2.default.createClass({
    displayName: "form",


    render: function render() {
        var _this = this;

        return _react2.default.createElement(
            "form",
            { onSubmit: this.props.submit },
            _react2.default.createElement(
                "div",
                { className: "form-group" },
                _react2.default.createElement(
                    "label",
                    null,
                    "Email"
                ),
                _react2.default.createElement("input", { type: "text", className: "form-control", value: this.props.data.email, onChange: function onChange(e) {
                        return _this.props.setData({ email: e.target.value });
                    } }),
                this.props.errors.email && _react2.default.createElement(
                    "span",
                    { className: "error" },
                    this.props.errors.email
                )
            ),
            _react2.default.createElement(
                "div",
                { className: "form-group" },
                _react2.default.createElement(
                    "label",
                    null,
                    "Token"
                ),
                _react2.default.createElement("input", { type: "text", className: "form-control", value: this.props.data.token, onChange: function onChange(e) {
                        return _this.props.setData({ token: e.target.value });
                    } }),
                this.props.errors.token && _react2.default.createElement(
                    "span",
                    { className: "error" },
                    this.props.errors.token
                )
            ),
            _react2.default.createElement(
                "div",
                { className: "form-group" },
                _react2.default.createElement(
                    "label",
                    null,
                    "New password"
                ),
                _react2.default.createElement("input", { type: "password", className: "form-control", value: this.props.data.password, onChange: function onChange(e) {
                        return _this.props.setData({ password: e.target.value });
                    } }),
                this.props.errors.password && _react2.default.createElement(
                    "span",
                    { className: "error" },
                    this.props.errors.password
                )
            ),
            _react2.default.createElement(
                "div",
                { className: "form-group" },
                _react2.default.createElement(
                    "label",
                    null,
                    "Confirm password"
                ),
                _react2.default.createElement("input", { type: "password", className: "form-control", value: this.props.data.password_confirmation, onChange: function onChange(e) {
                        return _this.props.setData({ password_confirmation: e.target.value });
                    } }),
                this.props.errors.password_confirmation && _react2.default.createElement(
                    "span",
                    { className: "error" },
                    this.props.errors.password_confirmation
                )
            ),
            _react2.default.createElement(
                "button",
                { className: "btn btn-block btn-primary", type: "submit" },
                "Continue"
            )
        );
    }
});

},{"react":"react"}],15:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _react = require('react');

var _react2 = _interopRequireDefault(_react);

var _components = require('components');

var _form = require('./form');

var _form2 = _interopRequireDefault(_form);

var _api = require('../../api');

var _api2 = _interopRequireDefault(_api);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = _react2.default.createClass({
    displayName: 'section',


    getInitialState: function getInitialState() {
        return {
            data: {
                email: '',
                token: '',
                password: '',
                password_confirmation: ''
            },
            errors: {},
            is_fetching: false,
            is_password_changed: false
        };
    },

    setData: function setData(data) {
        var data = Object.assign({}, this.state.data, data);
        this.setState({ data: data });
    },

    submit: function submit(e) {
        var _this = this;

        e.preventDefault();
        this.setState({ is_fetching: true }, function () {
            _api2.default.newPassword(_this.state.data).then(function () {
                _this.setState({
                    is_fetching: false,
                    is_password_changed: true
                });
            }).catch(function (errors) {
                _this.setState({
                    is_fetching: false,
                    errors: errors
                });
            });
        });
    },

    render: function render() {
        var _this2 = this;

        return _react2.default.createElement(
            _components.Loader,
            { is_fetching: this.state.is_fetching },
            _react2.default.createElement(
                _components.Panel,
                { title: 'New password' },
                this.state.is_password_changed ? _react2.default.createElement(
                    'div',
                    null,
                    _react2.default.createElement(
                        'div',
                        { className: 'alert alert-success' },
                        'Password changed, you can login now'
                    ),
                    _react2.default.createElement(
                        'button',
                        { className: 'btn btn-primary', onClick: function onClick() {
                                return _this2.props.setSection('Login');
                            } },
                        'Continue'
                    )
                ) : _react2.default.createElement(_form2.default, { values: this.state.values, setData: this.setData, data: this.state.data, errors: this.state.errors, submit: this.submit })
            )
        );
    }
});

},{"../../api":10,"./form":14,"components":2,"react":"react"}],16:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _react = require("react");

var _react2 = _interopRequireDefault(_react);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = _react2.default.createClass({
    displayName: "form",


    render: function render() {
        var _this = this;

        return _react2.default.createElement(
            "form",
            { onSubmit: this.props.submit },
            _react2.default.createElement(
                "div",
                { className: "form-group" },
                _react2.default.createElement(
                    "label",
                    null,
                    "Email"
                ),
                _react2.default.createElement("input", { type: "email", className: "form-control", value: this.props.data.email, onChange: function onChange(e) {
                        return _this.props.setData({ email: e.target.value });
                    }, autoComplete: "off" }),
                this.props.errors.email && _react2.default.createElement(
                    "span",
                    { className: "error" },
                    this.props.errors.email
                )
            ),
            _react2.default.createElement(
                "div",
                { className: "form-group" },
                _react2.default.createElement(
                    "label",
                    null,
                    "Password"
                ),
                _react2.default.createElement("input", { type: "password", className: "form-control", value: this.props.data.password, onChange: function onChange(e) {
                        return _this.props.setData({ password: e.target.value });
                    }, autoComplete: "off" }),
                this.props.errors.password && _react2.default.createElement(
                    "span",
                    { className: "error" },
                    this.props.errors.password
                )
            ),
            _react2.default.createElement(
                "div",
                { className: "form-group" },
                _react2.default.createElement(
                    "label",
                    null,
                    "Confirm password"
                ),
                _react2.default.createElement("input", { type: "password", className: "form-control", value: this.props.data.password_confirmation, onChange: function onChange(e) {
                        return _this.props.setData({ password_confirmation: e.target.value });
                    }, autoComplete: "off" }),
                this.props.errors.password_confirmation && _react2.default.createElement(
                    "span",
                    { className: "error" },
                    this.props.errors.password_confirmation
                )
            ),
            _react2.default.createElement(
                "button",
                { className: "btn btn-block btn-primary", type: "submit" },
                "Continue"
            )
        );
    }
});

},{"react":"react"}],17:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _react = require('react');

var _react2 = _interopRequireDefault(_react);

var _components = require('components');

var _form = require('./form');

var _form2 = _interopRequireDefault(_form);

var _api = require('../../api');

var _api2 = _interopRequireDefault(_api);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = _react2.default.createClass({
    displayName: 'section',


    getInitialState: function getInitialState() {
        return {
            data: {
                email: '',
                password: '',
                password_confirmation: ''
            },
            errors: {},
            is_fetching: false
        };
    },

    setData: function setData(data) {
        var data = Object.assign({}, this.state.data, data);
        this.setState({ data: data });
    },

    submit: function submit(e) {
        var _this = this;

        e.preventDefault();
        this.setState({ is_fetching: true }, function () {
            _api2.default.registration(_this.state.data).then(function (res) {
                _this.setState({ errors: {}, is_fetching: false });
                _this.props.setLogged(true);
            }).catch(function (errors) {
                _this.setState({
                    is_fetching: false,
                    errors: errors
                });
            });
        });
    },

    render: function render() {
        return _react2.default.createElement(
            _components.Loader,
            { is_fetching: this.state.is_fetching },
            _react2.default.createElement(
                _components.Panel,
                { title: 'Registration' },
                _react2.default.createElement(_form2.default, { values: this.state.values, setData: this.setData, data: this.state.data, errors: this.state.errors, submit: this.submit })
            )
        );
    }
});

},{"../../api":10,"./form":16,"components":2,"react":"react"}],18:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _react = require("react");

var _react2 = _interopRequireDefault(_react);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = _react2.default.createClass({
    displayName: "form",


    render: function render() {
        var _this = this;

        return _react2.default.createElement(
            "form",
            { onSubmit: this.props.submit },
            _react2.default.createElement(
                "div",
                { className: "form-group" },
                _react2.default.createElement(
                    "label",
                    null,
                    "Email"
                ),
                _react2.default.createElement("input", { type: "email", className: "form-control", value: this.props.data.email, onChange: function onChange(e) {
                        return _this.props.setData({ email: e.target.value });
                    } }),
                this.props.errors.email && _react2.default.createElement(
                    "span",
                    { className: "error" },
                    this.props.errors.email
                )
            ),
            _react2.default.createElement(
                "button",
                { className: "btn btn-block btn-primary", type: "submit" },
                "Continue"
            )
        );
    }
});

},{"react":"react"}],19:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _react = require('react');

var _react2 = _interopRequireDefault(_react);

var _components = require('components');

var _form = require('./form');

var _form2 = _interopRequireDefault(_form);

var _api = require('../../api');

var _api2 = _interopRequireDefault(_api);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = _react2.default.createClass({
    displayName: 'section',


    getInitialState: function getInitialState() {
        return {
            data: {
                email: ''
            },
            errors: {},
            is_fetching: false,
            is_code_sent: false
        };
    },

    setData: function setData(data) {
        var data = Object.assign({}, this.state.data, data);
        this.setState({ data: data });
    },

    submit: function submit(e) {
        var _this = this;

        e.preventDefault();
        this.setState({ is_fetching: true }, function () {
            _api2.default.resetPassword(_this.state.data).then(function () {
                _this.setState({
                    is_fetching: false,
                    is_code_sent: true
                });
            }).catch(function (errors) {
                _this.setState({
                    is_fetching: false,
                    errors: errors
                });
            });
        });
    },

    render: function render() {
        var _this2 = this;

        return _react2.default.createElement(
            _components.Loader,
            { is_fetching: this.state.is_fetching },
            _react2.default.createElement(
                _components.Panel,
                { title: 'Password restore' },
                this.state.is_code_sent ? _react2.default.createElement(
                    'div',
                    null,
                    _react2.default.createElement(
                        'div',
                        { className: 'alert alert-success' },
                        'Code has been sent to email.'
                    ),
                    _react2.default.createElement(
                        'button',
                        { className: 'btn btn-block btn-primary', onClick: function onClick() {
                                return _this2.props.setSection('NewPassword');
                            } },
                        'Continue'
                    )
                ) : _react2.default.createElement(_form2.default, { values: this.state.values, setData: this.setData, data: this.state.data, errors: this.state.errors, submit: this.submit })
            )
        );
    }
});

},{"../../api":10,"./form":18,"components":2,"react":"react"}],20:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _authenticated = require('./components/authenticated');

var _authenticated2 = _interopRequireDefault(_authenticated);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = {
    components: {
        Authenticated: _authenticated2.default
    }
};

},{"./components/authenticated":11}],21:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

require('whatwg-fetch');

var _token = require('libs/token');

var _token2 = _interopRequireDefault(_token);

var _client = require('libs/client');

var _client2 = _interopRequireDefault(_client);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function request(method, url) {
    var headers = new Headers({
        'X-Requested-With': 'XMLHttpRequest',
        'Authorization': 'Bearer ' + _token2.default.get()
    });
    var req = {
        credentials: 'same-origin',
        method: method,
        headers: headers
    };
    return fetch(url, req).then(function (response) {
        return response.json().then(function (data) {
            if (response.status !== 200) {
                if (data.error == 'access_denied') {
                    _token2.default.set(null);
                }
                return Promise.reject(data);
            }
            return Promise.resolve(data);
        });
    });
}

exports.default = {

    getAuthDetails: function getAuthDetails() {
        return request('GET', '/oauth_server/authorization' + window.location.search);
    },

    authorize: function authorize() {
        return request('POST', '/oauth_server/authorization/authorize' + window.location.search).then(function (res) {
            return _client2.default.authorize(res);
        });
    },

    deny: function deny(client_details) {
        return request('POST', '/oauth_server/authorization/deny' + window.location.search).then(function (res) {
            return _client2.default.deny(res);
        });
    }

};

},{"libs/client":6,"libs/token":9,"whatwg-fetch":"whatwg-fetch"}],22:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _react = require('react');

var _react2 = _interopRequireDefault(_react);

var _components = require('components');

var _api = require('../api');

var _api2 = _interopRequireDefault(_api);

var _client = require('libs/client');

var _client2 = _interopRequireDefault(_client);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = _react2.default.createClass({
    displayName: 'authorization',


    getInitialState: function getInitialState() {
        return {
            is_fetching: true,
            auth_details: false,
            error: false
        };
    },

    componentWillMount: function componentWillMount() {
        var _this = this;

        _api2.default.getAuthDetails().then(function (auth_details) {
            console.log(auth_details);
            _this.setState({
                auth_details: auth_details,
                error: false,
                is_fetching: false
            });
        }).catch(function (error) {
            _this.setState({
                auth_details: false,
                error: error,
                is_fetching: false
            });
        });
    },

    authorize: function authorize() {
        _api2.default.authorize();
    },

    deny: function deny() {
        _api2.default.deny();
    },

    render: function render() {
        return _react2.default.createElement(
            _components.Loader,
            { is_fetching: this.state.is_fetching },
            _react2.default.createElement(
                _components.Panel,
                { title: 'Authorization' },
                this.state.auth_details && _react2.default.createElement(
                    'div',
                    null,
                    _react2.default.createElement(
                        'p',
                        null,
                        _react2.default.createElement(
                            'strong',
                            null,
                            this.state.auth_details.client.name
                        ),
                        ' platform require authorization'
                    ),
                    _react2.default.createElement(
                        'div',
                        { className: 'row' },
                        _react2.default.createElement(
                            'div',
                            { className: 'col-xs-6' },
                            _react2.default.createElement(
                                'button',
                                { className: 'btn btn-block btn-success', onClick: this.authorize },
                                'Authorize'
                            )
                        ),
                        _react2.default.createElement(
                            'div',
                            { className: 'col-xs-6' },
                            _react2.default.createElement(
                                'button',
                                { className: 'btn btn-block btn-danger', onClick: this.deny },
                                'Deny'
                            )
                        )
                    )
                ),
                this.state.error && _react2.default.createElement(
                    'div',
                    { className: 'alert alert-danger' },
                    this.state.error.error_description
                )
            )
        );
    }
});

},{"../api":21,"components":2,"libs/client":6,"react":"react"}],23:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _routes = require('./routes');

var _routes2 = _interopRequireDefault(_routes);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = { routes: _routes2.default };

},{"./routes":24}],24:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _react = require('react');

var _react2 = _interopRequireDefault(_react);

var _reactRouter = require('react-router');

var _authorization = require('./components/authorization');

var _authorization2 = _interopRequireDefault(_authorization);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = _react2.default.createElement(_reactRouter.Route, { path: '/authorization', component: _authorization2.default });

},{"./components/authorization":22,"react":"react","react-router":"react-router"}],25:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _react = require("react");

var _react2 = _interopRequireDefault(_react);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = _react2.default.createClass({
    displayName: "not_found",

    render: function render() {
        return _react2.default.createElement(
            "div",
            { className: "alert alert-danger" },
            "Sorry! Page not found."
        );
    }
});

},{"react":"react"}],26:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _react = require("react");

var _react2 = _interopRequireDefault(_react);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = _react2.default.createClass({
    displayName: "server_error",

    render: function render() {
        return _react2.default.createElement(
            "div",
            { className: "alert alert-danger" },
            "Whoops! Something went wrong on our end."
        );
    }
});

},{"react":"react"}],27:[function(require,module,exports){
arguments[4][23][0].apply(exports,arguments)
},{"./routes":28,"dup":23}],28:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _react = require('react');

var _react2 = _interopRequireDefault(_react);

var _reactRouter = require('react-router');

var _not_found = require('./components/not_found');

var _not_found2 = _interopRequireDefault(_not_found);

var _server_error = require('./components/server_error');

var _server_error2 = _interopRequireDefault(_server_error);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = _react2.default.createElement(
    _reactRouter.Route,
    null,
    _react2.default.createElement(_reactRouter.Route, { path: '/server-error', component: _server_error2.default }),
    _react2.default.createElement(_reactRouter.Route, { path: '*', component: _not_found2.default })
);

},{"./components/not_found":25,"./components/server_error":26,"react":"react","react-router":"react-router"}],29:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _react = require("react");

var _react2 = _interopRequireDefault(_react);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = _react2.default.createClass({
    displayName: "layout",


    render: function render() {
        return _react2.default.createElement(
            "div",
            null,
            _react2.default.createElement(
                "div",
                { className: "container" },
                this.props.children
            )
        );
    }
});

},{"react":"react"}],30:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
    value: true
});

var _layout = require('./components/layout');

var _layout2 = _interopRequireDefault(_layout);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = {
    components: {
        Layout: _layout2.default
    }
};

},{"./components/layout":29}]},{},[1])
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJyZXNvdXJjZXMvZnJvbnRlbmQvYXBwLmpzIiwicmVzb3VyY2VzL2Zyb250ZW5kL2NvbXBvbmVudHMvaW5kZXguanMiLCJyZXNvdXJjZXMvZnJvbnRlbmQvY29tcG9uZW50cy9sb2FkZXIuanMiLCJyZXNvdXJjZXMvZnJvbnRlbmQvY29tcG9uZW50cy9wYW5lbC5qcyIsInJlc291cmNlcy9mcm9udGVuZC9saWJzL2Jvb3RzdHJhcC5qcyIsInJlc291cmNlcy9mcm9udGVuZC9saWJzL2NsaWVudC5qcyIsInJlc291cmNlcy9mcm9udGVuZC9saWJzL2Nvb2tpZV9zdG9yYWdlLmpzIiwicmVzb3VyY2VzL2Zyb250ZW5kL2xpYnMvaGlzdG9yeS5qcyIsInJlc291cmNlcy9mcm9udGVuZC9saWJzL3Rva2VuLmpzIiwicmVzb3VyY2VzL2Zyb250ZW5kL21vZHVsZXMvYXV0aC9hcGkuanMiLCJyZXNvdXJjZXMvZnJvbnRlbmQvbW9kdWxlcy9hdXRoL2NvbXBvbmVudHMvYXV0aGVudGljYXRlZC5qcyIsInJlc291cmNlcy9mcm9udGVuZC9tb2R1bGVzL2F1dGgvY29tcG9uZW50cy9sb2dpbi9mb3JtLmpzIiwicmVzb3VyY2VzL2Zyb250ZW5kL21vZHVsZXMvYXV0aC9jb21wb25lbnRzL2xvZ2luL3NlY3Rpb24uanMiLCJyZXNvdXJjZXMvZnJvbnRlbmQvbW9kdWxlcy9hdXRoL2NvbXBvbmVudHMvbmV3X3Bhc3N3b3JkL2Zvcm0uanMiLCJyZXNvdXJjZXMvZnJvbnRlbmQvbW9kdWxlcy9hdXRoL2NvbXBvbmVudHMvbmV3X3Bhc3N3b3JkL3NlY3Rpb24uanMiLCJyZXNvdXJjZXMvZnJvbnRlbmQvbW9kdWxlcy9hdXRoL2NvbXBvbmVudHMvcmVnaXN0cmF0aW9uL2Zvcm0uanMiLCJyZXNvdXJjZXMvZnJvbnRlbmQvbW9kdWxlcy9hdXRoL2NvbXBvbmVudHMvcmVnaXN0cmF0aW9uL3NlY3Rpb24uanMiLCJyZXNvdXJjZXMvZnJvbnRlbmQvbW9kdWxlcy9hdXRoL2NvbXBvbmVudHMvcmVzZXRfcGFzc3dvcmQvZm9ybS5qcyIsInJlc291cmNlcy9mcm9udGVuZC9tb2R1bGVzL2F1dGgvY29tcG9uZW50cy9yZXNldF9wYXNzd29yZC9zZWN0aW9uLmpzIiwicmVzb3VyY2VzL2Zyb250ZW5kL21vZHVsZXMvYXV0aC9pbmRleC5qcyIsInJlc291cmNlcy9mcm9udGVuZC9tb2R1bGVzL2F1dGhvcml6YXRpb24vYXBpLmpzIiwicmVzb3VyY2VzL2Zyb250ZW5kL21vZHVsZXMvYXV0aG9yaXphdGlvbi9jb21wb25lbnRzL2F1dGhvcml6YXRpb24uanMiLCJyZXNvdXJjZXMvZnJvbnRlbmQvbW9kdWxlcy9hdXRob3JpemF0aW9uL2luZGV4LmpzIiwicmVzb3VyY2VzL2Zyb250ZW5kL21vZHVsZXMvYXV0aG9yaXphdGlvbi9yb3V0ZXMuanMiLCJyZXNvdXJjZXMvZnJvbnRlbmQvbW9kdWxlcy9lcnJvcl9wYWdlcy9jb21wb25lbnRzL25vdF9mb3VuZC5qcyIsInJlc291cmNlcy9mcm9udGVuZC9tb2R1bGVzL2Vycm9yX3BhZ2VzL2NvbXBvbmVudHMvc2VydmVyX2Vycm9yLmpzIiwicmVzb3VyY2VzL2Zyb250ZW5kL21vZHVsZXMvZXJyb3JfcGFnZXMvcm91dGVzLmpzIiwicmVzb3VyY2VzL2Zyb250ZW5kL21vZHVsZXMvbGF5b3V0L2NvbXBvbmVudHMvbGF5b3V0LmpzIiwicmVzb3VyY2VzL2Zyb250ZW5kL21vZHVsZXMvbGF5b3V0L2luZGV4LmpzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBOzs7QUNBQTs7QUFDQTs7QUFFQTs7OztBQUNBOzs7O0FBQ0E7O0FBQ0E7O0FBRUE7Ozs7QUFDQTs7OztBQUNBOzs7O0FBQ0E7Ozs7OztBQUVBLG1CQUFTLE1BQVQsQ0FDSTtBQUFBO0FBQUEsTUFBUSxTQUFTLDBCQUFqQjtBQUNJO0FBQUE7QUFBQSxVQUFPLE1BQUssR0FBWixFQUFnQixXQUFXLGlCQUFPLFVBQVAs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QkFBTSxXQUFOLENBQWtCO0FBQUE7O0FBQzdCLFlBQVEsa0JBQVc7QUFDZixlQUNJO0FBQUE7QUFBQSxjQUFLLFdBQVUsb0JBQWY7QUFBQTtBQUFBLFNBREo7QUFLSDtBQVA0QixDQUFsQixDOzs7Ozs7Ozs7QUNGZjs7Ozs7O2tCQUVlLGdCQUFNLFdBQU4sQ0FBa0I7QUFBQTs7QUFDN0IsWUFBUSxrQkFBVztBQUNmLGVBQ0k7QUFBQTtBQUFBLGNBQUssV0FBVSxvQkFBZjtBQUFBO0FBQUEsU0FESjtBQUtIO0FBUDRCLENBQWxCLEM7Ozs7Ozs7Ozs7O0FDRmY7Ozs7QUFDQTs7QUFFQTs7OztBQUNBOzs7Ozs7a0JBR0k7QUFBQTtBQUFBO0FBQ0ksd0RBQU8sTUFBSyxlQUFaLEVBQTRCLGlDQUE1QixHQURKO0FBRUksd0RBQU8sTUFBSyxHQUFaLEVBQWdCLDhCQUFoQjtBQUZKLEM7Ozs7Ozs7OztBQ1BKOzs7Ozs7a0JBRWUsZ0JBQU0sV0FBTixDQUFrQjtBQUFBOzs7QUFFN0IsWUFBUSxrQkFBVztBQUNmLGVBQ0k7QUFBQTtBQUFBO0FBQ0k7QUFBQTtBQUFBLGtCQUFLLFdBQVUsV0FBZjtBQUNLLHFCQUFLLEtBQUwsQ0FBVztBQURoQjtBQURKLFNBREo7QUFPSDtBQVY0QixDQUFsQixDOzs7Ozs7Ozs7QUNGZjs7Ozs7O2tCQUVlO0FBQ1gsZ0JBQVk7QUFDUjtBQURRO0FBREQsQyIsImZpbGUiOiJnZW5lcmF0ZWQuanMiLCJzb3VyY2VSb290IjoiIiwic291cmNlc0NvbnRlbnQiOlsiKGZ1bmN0aW9uIGUodCxuLHIpe2Z1bmN0aW9uIHMobyx1KXtpZighbltvXSl7aWYoIXRbb10pe3ZhciBhPXR5cGVvZiByZXF1aXJlPT1cImZ1bmN0aW9uXCImJnJlcXVpcmU7aWYoIXUmJmEpcmV0dXJuIGEobywhMCk7aWYoaSlyZXR1cm4gaShvLCEwKTt2YXIgZj1uZXcgRXJyb3IoXCJDYW5ub3QgZmluZCBtb2R1bGUgJ1wiK28rXCInXCIpO3Rocm93IGYuY29kZT1cIk1PRFVMRV9OT1RfRk9VTkRcIixmfXZhciBsPW5bb109e2V4cG9ydHM6e319O3Rbb11bMF0uY2FsbChsLmV4cG9ydHMsZnVuY3Rpb24oZSl7dmFyIG49dFtvXVsxXVtlXTtyZXR1cm4gcyhuP246ZSl9LGwsbC5leHBvcnRzLGUsdCxuLHIpfXJldHVybiBuW29dLmV4cG9ydHN9dmFyIGk9dHlwZW9mIHJlcXVpcmU9PVwiZnVuY3Rpb25cIiYmcmVxdWlyZTtmb3IodmFyIG89MDtvPHIubGVuZ3RoO28rKylzKHJbb10pO3JldHVybiBzfSkiLCJpbXBvcnQgJ2xpYnMvYm9vdHN0cmFwJ1xuaW1wb3J0ICdsaWJzL3Rva2VuJ1xuXG5pbXBvcnQgUmVhY3QgZnJvbSAncmVhY3QnXG5pbXBvcnQgUmVhY3RET00gZnJvbSAncmVhY3QtZG9tJ1xuaW1wb3J0IHsgUm91dGVyLCBSb3V0ZSB9IGZyb20gJ3JlYWN0LXJvdXRlcidcbmltcG9ydCB7IGdldEhpc3RvcnkgfSBmcm9tICdsaWJzL2hpc3RvcnknXG5cbmltcG9ydCBsYXlvdXQgZnJvbSAnbW9kdWxlcy9sYXlvdXQnXG5pbXBvcnQgYXV0aCBmcm9tICdtb2R1bGVzL2F1dGgnXG5pbXBvcnQgYXV0aG9yaXphdGlvbiBmcm9tICdtb2R1bGVzL2F1dGhvcml6YXRpb24nXG5pbXBvcnQgZXJyb3JfcGFnZXMgZnJvbSAnbW9kdWxlcy9lcnJvcl9wYWdlcydcblxuUmVhY3RET00ucmVuZGVyKChcbiAgICA8Um91dGVyIGhpc3Rvcnk9e2dldEhpc3RvcnkoKX0+XG4gICAgICAgIDxSb3V0ZSBwYXRoPVwiL1wiIGNvbXBvbmVudD17bGF5b3V0LmNvbXBvbmVudHMuTGF5b3V0fT5cbiAgICAgICAgICAgIDxSb3V0ZSBjb21wb25lbnQ9e2F1dGguY29tcG9uZW50cy5BdXRoZW50aWNhdGVkfT5cbiAgICAgICAgICAgICAgICB7YXV0aG9yaXphdGlvbi5yb3V0ZXN9XG4gICAgICAgICAgICA8L1JvdXRlPlxuICAgICAgICAgICAge2Vycm9yX3BhZ2VzLnJvdXRlc31cbiAgICAgICAgPC9Sb3V0ZT5cbiAgICA8L1JvdXRlcj5cbiksIGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdhcHAnKSkiLCJpbXBvcnQgTG9hZGVyIGZyb20gJy4vbG9hZGVyJ1xuaW1wb3J0IFBhbmVsIGZyb20gJy4vcGFuZWwnXG5leHBvcnQge1xuICAgIExvYWRlcixcbiAgICBQYW5lbFxufSIsImltcG9ydCBSZWFjdCBmcm9tICdyZWFjdCdcblxuZXhwb3J0IGRlZmF1bHQgUmVhY3QuY3JlYXRlQ2xhc3Moe1xuXG4gICAgcmVuZGVyOiBmdW5jdGlvbigpIHtcbiAgICAgICAgcmV0dXJuIHRoaXMucHJvcHMuaXNfZmV0Y2hpbmcgPyA8ZGl2IGNsYXNzTmFtZT1cImxvYWRpbmdcIj5QbGVhc2Ugd2FpdC4uLjwvZGl2PiA6IHRoaXMucHJvcHMuY2hpbGRyZW5cbiAgICB9XG5cbn0pIiwiaW1wb3J0IFJlYWN0IGZyb20gJ3JlYWN0J1xuXG5leHBvcnQgZGVmYXVsdCBSZWFjdC5jcmVhdGVDbGFzcyh7XG5cbiAgICByZW5kZXI6IGZ1bmN0aW9uKCkge1xuICAgICAgICByZXR1cm4gKFxuICAgICAgICAgICAgPGRpdiBjbGFzc05hbWU9XCJwYW5lbCBwYW5lbC1kZWZhdWx0XCI+XG4gICAgICAgICAgICAgICAge3RoaXMucHJvcHMudGl0bGUgJiYgPGRpdiBjbGFzc05hbWU9XCJwYW5lbC1oZWFkaW5nXCI+e3RoaXMucHJvcHMudGl0bGV9PC9kaXY+fVxuICAgICAgICAgICAgICAgIDxkaXYgY2xhc3NOYW1lPVwicGFuZWwtYm9keVwiPlxuICAgICAgICAgICAgICAgICAgICB7dGhpcy5wcm9wcy5jaGlsZHJlbn1cbiAgICAgICAgICAgICAgICA8L2Rpdj5cbiAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICApXG4gICAgfVxufSk7IiwiaW1wb3J0IFByb21pc2UgZnJvbSAncHJvbWlzZS1wb2x5ZmlsbCc7XG5cbmlmKCF3aW5kb3cuUHJvbWlzZSkge1xuICAgIHdpbmRvdy5Qcm9taXNlID0gUHJvbWlzZTtcbn0iLCJpbXBvcnQgQ2hhbm5lbCBmcm9tICdqc2NoYW5uZWwnXG5cblxudmFyIENoYW5uZWxDbGllbnQgPSBmdW5jdGlvbigpIHtcbiAgICB2YXIgY2hhbiA9IENoYW5uZWwuYnVpbGQoe1xuICAgICAgICB3aW5kb3c6IHdpbmRvdy5vcGVuZXIsXG4gICAgICAgIG9yaWdpbjogJyonLFxuICAgICAgICBzY29wZTogJ2lvaV9sb2dpbidcbiAgICB9KVxuXG4gICAgdGhpcy5hdXRob3JpemUgPSAoYXV0aCkgPT4ge1xuICAgICAgICBjaGFuLmNhbGwoe1xuICAgICAgICAgICAgbWV0aG9kOiAnYXV0aG9yaXplJyxcbiAgICAgICAgICAgIHBhcmFtczogYXV0aCxcbiAgICAgICAgICAgIHN1Y2Nlc3M6ICgpPT4ge30sXG4gICAgICAgICAgICBlcnJvcjogKGVycikgPT4gY29uc29sZS5lcnJvcihlcnIpXG4gICAgICAgIH0pXG4gICAgfVxuXG4gICAgdGhpcy5kZW55ID0gKGF1dGgpID0+IHtcbiAgICAgICAgY2hhbi5jYWxsKHtcbiAgICAgICAgICAgIG1ldGhvZDogJ2RlbnknLFxuICAgICAgICAgICAgcGFyYW1zOiBhdXRoLFxuICAgICAgICAgICAgc3VjY2VzczogKCk9PiB7fSxcbiAgICAgICAgICAgIGVycm9yOiAoZXJyKSA9PiBjb25zb2xlLmVycm9yKGVycilcbiAgICAgICAgfSlcbiAgICB9XG59XG5cblxudmFyIFJlZGlyZWN0Q2xpZW50ID0gZnVuY3Rpb24oKSB7XG5cbiAgICB0aGlzLmF1dGhvcml6ZSA9IChhdXRoKSA9PiB7XG4gICAgICAgIGlmKGF1dGgucmVkaXJlY3RfdXJpKSB7XG4gICAgICAgICAgICB3aW5kb3cubG9jYXRpb24uaHJlZiA9IGF1dGgucmVkaXJlY3RfdXJpXG4gICAgICAgIH1cbiAgICB9XG5cbiAgICB0aGlzLmRlbnkgPSAoYXV0aCkgPT4ge1xuICAgICAgICBpZihhdXRoLnJlZGlyZWN0X3VyaSkge1xuICAgICAgICAgICAgd2luZG93LmxvY2F0aW9uLmhyZWYgPSBhdXRoLnJlZGlyZWN0X3VyaVxuICAgICAgICB9XG4gICAgfVxuXG59XG5cbnZhciBjbGllbnQgPSB3aW5kb3cub3BlbmVyID8gbmV3IENoYW5uZWxDbGllbnQoKSA6IG5ldyBSZWRpcmVjdENsaWVudCgpXG5cbmV4cG9ydCBkZWZhdWx0IGNsaWVudCIsImltcG9ydCBDb29raWUgZnJvbSAnanMtY29va2llJ1xuXG52YXIgb3B0aW9ucyA9IHtcbiAgICBwYXRoOiAnJ1xufVxuXG5leHBvcnQgZGVmYXVsdCB7XG5cbiAgICBnZXQ6IGZ1bmN0aW9uKGtleSkge1xuICAgICAgICByZXR1cm4gQ29va2llLmdldChrZXksIG9wdGlvbnMpO1xuICAgIH0sXG5cbiAgICBzZXQ6IGZ1bmN0aW9uKGtleSwgdmFsdWUpIHtcbiAgICAgICAgQ29va2llLnNldChrZXksIHZhbHVlLCBvcHRpb25zKTtcbiAgICB9LFxuXG4gICAgcmVtb3ZlOiBmdW5jdGlvbihrZXkpIHtcbiAgICAgICAgQ29va2llLnJlbW92ZShrZXksIG9wdGlvbnMpO1xuICAgIH1cbn0iLCJpbXBvcnQgeyB1c2VSb3V0ZXJIaXN0b3J5LCBSb3V0ZXIsIFJvdXRlIH0gZnJvbSAncmVhY3Qtcm91dGVyJ1xuaW1wb3J0IHsgY3JlYXRlSGlzdG9yeSB9IGZyb20gJ2hpc3RvcnknXG5cbnZhciBoaXN0b3J5ID0gdXNlUm91dGVySGlzdG9yeShjcmVhdGVIaXN0b3J5KSh7IGJhc2VuYW1lOiAnLycgfSlcblxuZXhwb3J0IGZ1bmN0aW9uIGdldEhpc3RvcnkoKSB7XG4gICAgcmV0dXJuIGhpc3Rvcnlcbn0iLCJpbXBvcnQgc3RvcmFnZSBmcm9tICcuL2Nvb2tpZV9zdG9yYWdlJ1xuXG52YXIga2V5ID0gJ3Rva2VuJztcblxudmFyIHRva2VuID0ge1xuXG4gICAgc2V0OiBmdW5jdGlvbih0b2tlbikge1xuICAgICAgICBpZih0b2tlbikge1xuICAgICAgICAgICAgc3RvcmFnZS5zZXQoa2V5LCB0b2tlbik7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICBzdG9yYWdlLnJlbW92ZShrZXkpO1xuICAgICAgICB9XG4gICAgfSxcblxuICAgIGdldDogZnVuY3Rpb24oKSB7XG4gICAgICAgIHJldHVybiBzdG9yYWdlLmdldChrZXkpO1xuICAgIH0sXG5cbiAgICBjaGVjazogZnVuY3Rpb24oKSB7XG4gICAgICAgIHJldHVybiAhIXRoaXMuZ2V0KCk7XG4gICAgfVxuXG59XG5cblxudmFyIGggPSB3aW5kb3cubG9jYXRpb24uaGFzaC5zdWJzdHIoMSk7XG5pZihoLmluZGV4T2YoJ3Rva2VuPScpICE9PSAtMSkge1xuICAgIHZhciB0ID0gaC5zcGxpdCgnPScpO1xuICAgIHRva2VuLnNldCh0WzFdKTtcbiAgICB3aW5kb3cubG9jYXRpb24uaGFzaCA9ICcnO1xufVxuXG5cbmV4cG9ydCBkZWZhdWx0IHRva2VuIiwiaW1wb3J0ICd3aGF0d2ctZmV0Y2gnXG5pbXBvcnQgdG9rZW4gZnJvbSAnbGlicy90b2tlbidcblxuZnVuY3Rpb24gZ2V0UmVxdWVzdEJvZHkoZGF0YSkge1xuICAgIHZhciBib2R5ID0gbmV3IEZvcm1EYXRhKCk7XG4gICAgZm9yKHZhciBrIGluIGRhdGEpIHtcbiAgICAgICAgaWYoZGF0YS5oYXNPd25Qcm9wZXJ0eShrKSkge1xuICAgICAgICAgICAgYm9keS5hcHBlbmQoaywgZGF0YVtrXSlcbiAgICAgICAgfVxuICAgIH1cbiAgICByZXR1cm4gYm9keTtcbn1cblxuZnVuY3Rpb24gcG9zdCh1cmwsIGRhdGEpIHtcbiAgICB2YXIgYm9keSA9IGdldFJlcXVlc3RCb2R5KGRhdGEpXG4gICAgdmFyIGhlYWRlcnMgPSBuZXcgSGVhZGVycyh7XG4gICAgICAgICdYLVJlcXVlc3RlZC1XaXRoJzogJ1hNTEh0dHBSZXF1ZXN0J1xuICAgIH0pXG4gICAgdmFyIHJlcSA9IHtcbiAgICAgICAgY3JlZGVudGlhbHM6ICdzYW1lLW9yaWdpbicsXG4gICAgICAgIG1ldGhvZDogJ1BPU1QnLFxuICAgICAgICBoZWFkZXJzLFxuICAgICAgICBib2R5XG4gICAgfVxuICAgIHJldHVybiBmZXRjaCh1cmwsIHJlcSlcbiAgICAgICAgLnRoZW4oKHJlc3BvbnNlKSA9PiB7XG4gICAgICAgICAgICBpZigocmVzcG9uc2Uuc3RhdHVzID49IDIwMCAmJiByZXNwb25zZS5zdGF0dXMgPCAzMDApIHx8IHJlc3BvbnNlLnN0YXR1cyA9PSA0MjIpIHtcbiAgICAgICAgICAgICAgICByZXR1cm4gcmVzcG9uc2Uub2sgPyByZXNwb25zZS5qc29uKCkgOiByZXNwb25zZS5qc29uKCkudGhlbihlcnIgPT4gUHJvbWlzZS5yZWplY3QoZXJyKSlcbiAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuIFByb21pc2UucmVqZWN0KCk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pXG59XG5cblxuZXhwb3J0IGRlZmF1bHQge1xuXG4gICAgbG9naW46IGZ1bmN0aW9uKGRhdGEpIHtcbiAgICAgICAgdG9rZW4uc2V0KG51bGwpO1xuICAgICAgICByZXR1cm4gcG9zdCgnL2xvZ2luJywgZGF0YSlcbiAgICAgICAgICAgIC50aGVuKChyZXMpID0+IHtcbiAgICAgICAgICAgICAgICB0b2tlbi5zZXQocmVzLmFjY2Vzc190b2tlbik7XG4gICAgICAgICAgICB9KVxuICAgIH0sXG5cbiAgICByZXNldFBhc3N3b3JkOiBmdW5jdGlvbihkYXRhKSB7XG4gICAgICAgIHJldHVybiBwb3N0KCcvcGFzc3dvcmQvZW1haWwnLCBkYXRhKVxuICAgIH0sXG5cbiAgICBuZXdQYXNzd29yZDogZnVuY3Rpb24oZGF0YSkge1xuICAgICAgICByZXR1cm4gcG9zdCgnL3Bhc3N3b3JkL3Jlc2V0JywgZGF0YSlcbiAgICB9LFxuXG4gICAgcmVnaXN0cmF0aW9uOiBmdW5jdGlvbihkYXRhKSB7XG4gICAgICAgIHJldHVybiBwb3N0KCcvcmVnaXN0cmF0aW9uJywgZGF0YSlcbiAgICAgICAgICAgIC50aGVuKChyZXMpID0+IHtcbiAgICAgICAgICAgICAgICBpZihyZXMuc3VjY2Vzcykge1xuICAgICAgICAgICAgICAgICAgICB0b2tlbi5zZXQocmVzLnRva2VuLmFjY2Vzc190b2tlbik7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSlcbiAgICB9XG5cbn0iLCJpbXBvcnQgUmVhY3QgZnJvbSAncmVhY3QnXG5pbXBvcnQgdG9rZW4gZnJvbSAnbGlicy90b2tlbidcblxuaW1wb3J0IExvZ2luIGZyb20gJy4vbG9naW4vc2VjdGlvbidcbmltcG9ydCBSZXNldFBhc3N3b3JkIGZyb20gJy4vcmVzZXRfcGFzc3dvcmQvc2VjdGlvbidcbmltcG9ydCBOZXdQYXNzd29yZCBmcm9tICcuL25ld19wYXNzd29yZC9zZWN0aW9uJ1xuaW1wb3J0IFJlZ2lzdHJhdGlvbiBmcm9tICcuL3JlZ2lzdHJhdGlvbi9zZWN0aW9uJ1xuXG52YXIgc2VjdGlvbnMgPSB7XG4gICAgTG9naW4sXG4gICAgUmVzZXRQYXNzd29yZCxcbiAgICBOZXdQYXNzd29yZCxcbiAgICBSZWdpc3RyYXRpb25cbn1cblxuZXhwb3J0IGRlZmF1bHQgUmVhY3QuY3JlYXRlQ2xhc3Moe1xuXG4gICAgZ2V0SW5pdGlhbFN0YXRlOiBmdW5jdGlvbigpIHtcbiAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgIGlzX2xvZ2dlZDogdG9rZW4uY2hlY2soKSxcbiAgICAgICAgICAgIHNlY3Rpb246ICdMb2dpbidcbiAgICAgICAgfVxuICAgIH0sXG5cblxuICAgIHNldFNlY3Rpb246IGZ1bmN0aW9uKHNlY3Rpb24pIHtcbiAgICAgICAgdGhpcy5zZXRTdGF0ZSh7IHNlY3Rpb24gfSk7XG4gICAgfSxcblxuXG4gICAgc2V0TG9nZ2VkOiBmdW5jdGlvbihpc19sb2dnZWQpIHtcbiAgICAgICAgdGhpcy5zZXRTdGF0ZSh7IGlzX2xvZ2dlZCB9KVxuICAgIH0sXG5cbiAgICByZW5kZXI6IGZ1bmN0aW9uKCkge1xuICAgICAgICBpZih0aGlzLnN0YXRlLmlzX2xvZ2dlZCkge1xuICAgICAgICAgICAgcmV0dXJuIHRoaXMucHJvcHMuY2hpbGRyZW5cbiAgICAgICAgfVxuICAgICAgICByZXR1cm4gUmVhY3QuY3JlYXRlRWxlbWVudChzZWN0aW9uc1t0aGlzLnN0YXRlLnNlY3Rpb25dLCB7IHNldFNlY3Rpb246IHRoaXMuc2V0U2VjdGlvbiwgc2V0TG9nZ2VkOiB0aGlzLnNldExvZ2dlZCB9KVxuICAgIH1cblxufSk7IiwiaW1wb3J0IFJlYWN0IGZyb20gJ3JlYWN0J1xuXG5leHBvcnQgZGVmYXVsdCBSZWFjdC5jcmVhdGVDbGFzcyh7XG5cbiAgICByZW5kZXI6IGZ1bmN0aW9uKCkge1xuICAgICAgICByZXR1cm4gKFxuICAgICAgICAgICAgPGZvcm0gb25TdWJtaXQ9e3RoaXMucHJvcHMuc3VibWl0fT5cbiAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzTmFtZT1cImZvcm0tZ3JvdXBcIj5cbiAgICAgICAgICAgICAgICAgICAgPGxhYmVsPkVtYWlsPC9sYWJlbD5cbiAgICAgICAgICAgICAgICAgICAgPGlucHV0IHR5cGU9XCJlbWFpbFwiIGNsYXNzTmFtZT1cImZvcm0tY29udHJvbFwiIHZhbHVlPXt0aGlzLnByb3BzLmRhdGEuZW1haWx9IG9uQ2hhbmdlPXsoZSk9PnRoaXMucHJvcHMuc2V0RGF0YSh7IGVtYWlsOiBlLnRhcmdldC52YWx1ZX0pfS8+XG4gICAgICAgICAgICAgICAgICAgIHt0aGlzLnByb3BzLmVycm9ycy5lbWFpbCAmJiA8c3BhbiBjbGFzc05hbWU9XCJlcnJvclwiPnt0aGlzLnByb3BzLmVycm9ycy5lbWFpbH08L3NwYW4+fVxuICAgICAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICAgICAgICAgIDxkaXYgY2xhc3NOYW1lPVwiZm9ybS1ncm91cFwiPlxuICAgICAgICAgICAgICAgICAgICA8bGFiZWw+UGFzc3dvcmQ8L2xhYmVsPlxuICAgICAgICAgICAgICAgICAgICA8aW5wdXQgdHlwZT1cInBhc3N3b3JkXCIgY2xhc3NOYW1lPVwiZm9ybS1jb250cm9sXCIgdmFsdWU9e3RoaXMucHJvcHMuZGF0YS5wYXNzd29yZH0gb25DaGFuZ2U9eyhlKT0+dGhpcy5wcm9wcy5zZXREYXRhKHsgcGFzc3dvcmQ6IGUudGFyZ2V0LnZhbHVlfSl9Lz5cbiAgICAgICAgICAgICAgICAgICAge3RoaXMucHJvcHMuZXJyb3JzLnBhc3N3b3JkICYmIDxzcGFuIGNsYXNzTmFtZT1cImVycm9yXCI+e3RoaXMucHJvcHMuZXJyb3JzLnBhc3N3b3JkfTwvc3Bhbj59XG4gICAgICAgICAgICAgICAgPC9kaXY+XG4gICAgICAgICAgICAgICAgPGJ1dHRvbiBjbGFzc05hbWU9XCJidG4gYnRuLWJsb2NrIGJ0bi1wcmltYXJ5XCIgdHlwZT1cInN1Ym1pdFwiPkxvZ2luPC9idXR0b24+XG4gICAgICAgICAgICA8L2Zvcm0+XG4gICAgICAgIClcbiAgICB9XG59KTsiLCJpbXBvcnQgUmVhY3QgZnJvbSAncmVhY3QnXG5pbXBvcnQgeyBQYW5lbCwgTG9hZGVyIH0gZnJvbSAnY29tcG9uZW50cydcbmltcG9ydCBGb3JtIGZyb20gJy4vZm9ybSdcbmltcG9ydCBhcGkgZnJvbSAnLi4vLi4vYXBpJ1xuXG5leHBvcnQgZGVmYXVsdCBSZWFjdC5jcmVhdGVDbGFzcyh7XG5cbiAgICBnZXRJbml0aWFsU3RhdGU6IGZ1bmN0aW9uKCkge1xuICAgICAgICByZXR1cm4ge1xuICAgICAgICAgICAgZGF0YToge1xuICAgICAgICAgICAgICAgIGVtYWlsOiAnJyxcbiAgICAgICAgICAgICAgICBwYXNzd29yZDogJydcbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICBlcnJvcnM6IHt9LFxuICAgICAgICAgICAgaXNfZmV0Y2hpbmc6IGZhbHNlXG4gICAgICAgIH1cbiAgICB9LFxuXG5cbiAgICBzZXREYXRhOiBmdW5jdGlvbihkYXRhKSB7XG4gICAgICAgIHZhciBkYXRhID0gT2JqZWN0LmFzc2lnbih7fSwgdGhpcy5zdGF0ZS5kYXRhLCBkYXRhKVxuICAgICAgICB0aGlzLnNldFN0YXRlKHsgZGF0YSB9KVxuICAgIH0sXG5cblxuICAgIHN1Ym1pdDogZnVuY3Rpb24oZSkge1xuICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgIHRoaXMuc2V0U3RhdGUoeyBpc19mZXRjaGluZzogdHJ1ZX0sICgpID0+IHtcbiAgICAgICAgICAgIGFwaS5sb2dpbih0aGlzLnN0YXRlLmRhdGEpXG4gICAgICAgICAgICAgICAgLnRoZW4oKCk9PntcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5zZXRTdGF0ZSh7IGlzX2ZldGNoaW5nOiBmYWxzZSB9KTtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5wcm9wcy5zZXRMb2dnZWQodHJ1ZSk7XG4gICAgICAgICAgICAgICAgfSlcbiAgICAgICAgICAgICAgICAuY2F0Y2goKGVycm9ycyk9PntcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5zZXRTdGF0ZSh7XG4gICAgICAgICAgICAgICAgICAgICAgICBpc19mZXRjaGluZzogZmFsc2UsXG4gICAgICAgICAgICAgICAgICAgICAgICBlcnJvcnNcbiAgICAgICAgICAgICAgICAgICAgfSlcbiAgICAgICAgICAgICAgICB9KVxuICAgICAgICB9KVxuICAgIH0sXG5cblxuICAgIHJlbmRlcjogZnVuY3Rpb24oKSB7XG4gICAgICAgIHJldHVybiAoXG4gICAgICAgICAgICA8TG9hZGVyIGlzX2ZldGNoaW5nPXt0aGlzLnN0YXRlLmlzX2ZldGNoaW5nfT5cbiAgICAgICAgICAgICAgICA8ZGl2PlxuICAgICAgICAgICAgICAgICAgICA8UGFuZWwgdGl0bGU9XCJMb2dpblwiPlxuICAgICAgICAgICAgICAgICAgICAgICAgPEZvcm0gdmFsdWVzPXt0aGlzLnN0YXRlLnZhbHVlc30gc2V0RGF0YT17dGhpcy5zZXREYXRhfSBkYXRhPXt0aGlzLnN0YXRlLmRhdGF9IGVycm9ycz17dGhpcy5zdGF0ZS5lcnJvcnN9IG9uU3VibWl0PXt0aGlzLnN1Ym1pdH0vPlxuICAgICAgICAgICAgICAgICAgICA8L1BhbmVsPlxuXG4gICAgICAgICAgICAgICAgICAgIDxQYW5lbD5cbiAgICAgICAgICAgICAgICAgICAgICAgIDxkaXYgY2xhc3NOYW1lPVwicm93XCI+XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgPGRpdiBjbGFzc05hbWU9XCJjb2wteHMtNlwiPlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8YnV0dG9uIGNsYXNzTmFtZT1cImJ0biBidG4tYmxvY2sgYnRuLXByaW1hcnlcIiBvbkNsaWNrPXsoKT0+dGhpcy5wcm9wcy5zZXRTZWN0aW9uKCdSZWdpc3RyYXRpb24nKX0+Q3JlYXRlIGFjY291bnQ8L2J1dHRvbj5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICA8L2Rpdj5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzTmFtZT1cImNvbC14cy02XCI+XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxidXR0b24gY2xhc3NOYW1lPVwiYnRuIGJ0bi1ibG9jayBidG4tcHJpbWFyeVwiIG9uQ2xpY2s9eygpPT50aGlzLnByb3BzLnNldFNlY3Rpb24oJ1Jlc2V0UGFzc3dvcmQnKX0+UmVzZXQgcGFzc3dvcmQ8L2J1dHRvbj5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICA8L2Rpdj5cbiAgICAgICAgICAgICAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICAgICAgICAgICAgICA8L1BhbmVsPlxuXG4gICAgICAgICAgICAgICAgICAgIDxQYW5lbCB0aXRsZT1cIk9yIHNpZ24gdXAgd2l0aFwiPlxuICAgICAgICAgICAgICAgICAgICAgICAgPGRpdiBjbGFzc05hbWU9XCJyb3dcIj5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzTmFtZT1cImNvbC14cy02XCI+XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxhIGNsYXNzTmFtZT1cImJ0biBidG4tYmxvY2sgYnRuLWRlZmF1bHRcIiBocmVmPXsnL29hdXRoX2NsaWVudC9yZWRpcmVjdC9mYWNlYm9vaycgKyB3aW5kb3cubG9jYXRpb24uc2VhcmNofSBvbkNsaWNrPXsoKT0+dGhpcy5zZXRTdGF0ZSh7aXNfZmV0Y2hpbmc6IHRydWV9KX0+RmFjZWJvb2s8L2E+XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgPC9kaXY+XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgPGRpdiBjbGFzc05hbWU9XCJjb2wteHMtNlwiPlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8YSBjbGFzc05hbWU9XCJidG4gYnRuLWJsb2NrIGJ0bi1kZWZhdWx0XCIgaHJlZj17Jy9vYXV0aF9jbGllbnQvcmVkaXJlY3QvZ29vZ2xlJyArIHdpbmRvdy5sb2NhdGlvbi5zZWFyY2h9IG9uQ2xpY2s9eygpPT50aGlzLnNldFN0YXRlKHtpc19mZXRjaGluZzogdHJ1ZX0pfT5Hb29nbGU8L2E+XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgPC9kaXY+XG4gICAgICAgICAgICAgICAgICAgICAgICA8L2Rpdj5cbiAgICAgICAgICAgICAgICAgICAgPC9QYW5lbD5cblxuICAgICAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICAgICAgPC9Mb2FkZXI+XG4gICAgICAgIClcbiAgICB9XG5cbn0pOyIsImltcG9ydCBSZWFjdCBmcm9tICdyZWFjdCdcblxuZXhwb3J0IGRlZmF1bHQgUmVhY3QuY3JlYXRlQ2xhc3Moe1xuXG4gICAgcmVuZGVyOiBmdW5jdGlvbigpIHtcbiAgICAgICAgcmV0dXJuIChcbiAgICAgICAgICAgIDxmb3JtIG9uU3VibWl0PXt0aGlzLnByb3BzLnN1Ym1pdH0+XG4gICAgICAgICAgICAgICAgPGRpdiBjbGFzc05hbWU9XCJmb3JtLWdyb3VwXCI+XG4gICAgICAgICAgICAgICAgICAgIDxsYWJlbD5FbWFpbDwvbGFiZWw+XG4gICAgICAgICAgICAgICAgICAgIDxpbnB1dCB0eXBlPVwidGV4dFwiIGNsYXNzTmFtZT1cImZvcm0tY29udHJvbFwiIHZhbHVlPXt0aGlzLnByb3BzLmRhdGEuZW1haWx9IG9uQ2hhbmdlPXsoZSk9PnRoaXMucHJvcHMuc2V0RGF0YSh7IGVtYWlsOiBlLnRhcmdldC52YWx1ZX0pfS8+XG4gICAgICAgICAgICAgICAgICAgIHt0aGlzLnByb3BzLmVycm9ycy5lbWFpbCAmJiA8c3BhbiBjbGFzc05hbWU9XCJlcnJvclwiPnt0aGlzLnByb3BzLmVycm9ycy5lbWFpbH08L3NwYW4+fVxuICAgICAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICAgICAgICAgIDxkaXYgY2xhc3NOYW1lPVwiZm9ybS1ncm91cFwiPlxuICAgICAgICAgICAgICAgICAgICA8bGFiZWw+VG9rZW48L2xhYmVsPlxuICAgICAgICAgICAgICAgICAgICA8aW5wdXQgdHlwZT1cInRleHRcIiBjbGFzc05hbWU9XCJmb3JtLWNvbnRyb2xcIiB2YWx1ZT17dGhpcy5wcm9wcy5kYXRhLnRva2VufSBvbkNoYW5nZT17KGUpPT50aGlzLnByb3BzLnNldERhdGEoeyB0b2tlbjogZS50YXJnZXQudmFsdWV9KX0vPlxuICAgICAgICAgICAgICAgICAgICB7dGhpcy5wcm9wcy5lcnJvcnMudG9rZW4gJiYgPHNwYW4gY2xhc3NOYW1lPVwiZXJyb3JcIj57dGhpcy5wcm9wcy5lcnJvcnMudG9rZW59PC9zcGFuPn1cbiAgICAgICAgICAgICAgICA8L2Rpdj5cbiAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzTmFtZT1cImZvcm0tZ3JvdXBcIj5cbiAgICAgICAgICAgICAgICAgICAgPGxhYmVsPk5ldyBwYXNzd29yZDwvbGFiZWw+XG4gICAgICAgICAgICAgICAgICAgIDxpbnB1dCB0eXBlPVwicGFzc3dvcmRcIiBjbGFzc05hbWU9XCJmb3JtLWNvbnRyb2xcIiB2YWx1ZT17dGhpcy5wcm9wcy5kYXRhLnBhc3N3b3JkfSBvbkNoYW5nZT17KGUpPT50aGlzLnByb3BzLnNldERhdGEoeyBwYXNzd29yZDogZS50YXJnZXQudmFsdWV9KX0vPlxuICAgICAgICAgICAgICAgICAgICB7dGhpcy5wcm9wcy5lcnJvcnMucGFzc3dvcmQgJiYgPHNwYW4gY2xhc3NOYW1lPVwiZXJyb3JcIj57dGhpcy5wcm9wcy5lcnJvcnMucGFzc3dvcmR9PC9zcGFuPn1cbiAgICAgICAgICAgICAgICA8L2Rpdj5cbiAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzTmFtZT1cImZvcm0tZ3JvdXBcIj5cbiAgICAgICAgICAgICAgICAgICAgPGxhYmVsPkNvbmZpcm0gcGFzc3dvcmQ8L2xhYmVsPlxuICAgICAgICAgICAgICAgICAgICA8aW5wdXQgdHlwZT1cInBhc3N3b3JkXCIgY2xhc3NOYW1lPVwiZm9ybS1jb250cm9sXCIgdmFsdWU9e3RoaXMucHJvcHMuZGF0YS5wYXNzd29yZF9jb25maXJtYXRpb259IG9uQ2hhbmdlPXsoZSk9PnRoaXMucHJvcHMuc2V0RGF0YSh7IHBhc3N3b3JkX2NvbmZpcm1hdGlvbjogZS50YXJnZXQudmFsdWV9KX0vPlxuICAgICAgICAgICAgICAgICAgICB7dGhpcy5wcm9wcy5lcnJvcnMucGFzc3dvcmRfY29uZmlybWF0aW9uICYmIDxzcGFuIGNsYXNzTmFtZT1cImVycm9yXCI+e3RoaXMucHJvcHMuZXJyb3JzLnBhc3N3b3JkX2NvbmZpcm1hdGlvbn08L3NwYW4+fVxuICAgICAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICAgICAgICAgIDxidXR0b24gY2xhc3NOYW1lPVwiYnRuIGJ0bi1ibG9jayBidG4tcHJpbWFyeVwiIHR5cGU9XCJzdWJtaXRcIj5Db250aW51ZTwvYnV0dG9uPlxuICAgICAgICAgICAgPC9mb3JtPlxuICAgICAgICApXG4gICAgfVxufSk7IiwiaW1wb3J0IFJlYWN0IGZyb20gJ3JlYWN0J1xuaW1wb3J0IHsgUGFuZWwsIExvYWRlciB9IGZyb20gJ2NvbXBvbmVudHMnXG5pbXBvcnQgRm9ybSBmcm9tICcuL2Zvcm0nXG5pbXBvcnQgYXBpIGZyb20gJy4uLy4uL2FwaSdcblxuZXhwb3J0IGRlZmF1bHQgUmVhY3QuY3JlYXRlQ2xhc3Moe1xuXG4gICAgZ2V0SW5pdGlhbFN0YXRlOiBmdW5jdGlvbigpIHtcbiAgICAgICAgcmV0dXJuIHtcbiAgICAgICAgICAgIGRhdGE6IHtcbiAgICAgICAgICAgICAgICBlbWFpbDogJycsXG4gICAgICAgICAgICAgICAgdG9rZW46ICcnLFxuICAgICAgICAgICAgICAgIHBhc3N3b3JkOiAnJyxcbiAgICAgICAgICAgICAgICBwYXNzd29yZF9jb25maXJtYXRpb246ICcnLFxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIGVycm9yczoge30sXG4gICAgICAgICAgICBpc19mZXRjaGluZzogZmFsc2UsXG4gICAgICAgICAgICBpc19wYXNzd29yZF9jaGFuZ2VkOiBmYWxzZVxuICAgICAgICB9XG4gICAgfSxcblxuXG4gICAgc2V0RGF0YTogZnVuY3Rpb24oZGF0YSkge1xuICAgICAgICB2YXIgZGF0YSA9IE9iamVjdC5hc3NpZ24oe30sIHRoaXMuc3RhdGUuZGF0YSwgZGF0YSlcbiAgICAgICAgdGhpcy5zZXRTdGF0ZSh7IGRhdGEgfSlcbiAgICB9LFxuXG5cbiAgICBzdWJtaXQ6IGZ1bmN0aW9uKGUpIHtcbiAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICB0aGlzLnNldFN0YXRlKHsgaXNfZmV0Y2hpbmc6IHRydWV9LCAoKSA9PiB7XG4gICAgICAgICAgICBhcGkubmV3UGFzc3dvcmQodGhpcy5zdGF0ZS5kYXRhKVxuICAgICAgICAgICAgICAgIC50aGVuKCgpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5zZXRTdGF0ZSh7XG4gICAgICAgICAgICAgICAgICAgICAgICBpc19mZXRjaGluZzogZmFsc2UsXG4gICAgICAgICAgICAgICAgICAgICAgICBpc19wYXNzd29yZF9jaGFuZ2VkOiB0cnVlXG4gICAgICAgICAgICAgICAgICAgIH0pXG4gICAgICAgICAgICAgICAgfSlcbiAgICAgICAgICAgICAgICAuY2F0Y2goKGVycm9ycyk9PntcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5zZXRTdGF0ZSh7XG4gICAgICAgICAgICAgICAgICAgICAgICBpc19mZXRjaGluZzogZmFsc2UsXG4gICAgICAgICAgICAgICAgICAgICAgICBlcnJvcnNcbiAgICAgICAgICAgICAgICAgICAgfSlcbiAgICAgICAgICAgICAgICB9KVxuICAgICAgICB9KVxuICAgIH0sXG5cblxuICAgIHJlbmRlcjogZnVuY3Rpb24oKSB7XG4gICAgICAgIHJldHVybiAoXG4gICAgICAgICAgICA8TG9hZGVyIGlzX2ZldGNoaW5nPXt0aGlzLnN0YXRlLmlzX2ZldGNoaW5nfT5cbiAgICAgICAgICAgICAgICA8UGFuZWwgdGl0bGU9XCJOZXcgcGFzc3dvcmRcIj5cbiAgICAgICAgICAgICAgICAgICAge3RoaXMuc3RhdGUuaXNfcGFzc3dvcmRfY2hhbmdlZCA/XG4gICAgICAgICAgICAgICAgICAgICAgICA8ZGl2PlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxkaXYgY2xhc3NOYW1lPVwiYWxlcnQgYWxlcnQtc3VjY2Vzc1wiPlBhc3N3b3JkIGNoYW5nZWQsIHlvdSBjYW4gbG9naW4gbm93PC9kaXY+XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgPGJ1dHRvbiBjbGFzc05hbWU9XCJidG4gYnRuLXByaW1hcnlcIiBvbkNsaWNrPXsoKT0+dGhpcy5wcm9wcy5zZXRTZWN0aW9uKCdMb2dpbicpfT5Db250aW51ZTwvYnV0dG9uPlxuICAgICAgICAgICAgICAgICAgICAgICAgPC9kaXY+XG4gICAgICAgICAgICAgICAgICAgICAgICA6XG4gICAgICAgICAgICAgICAgICAgICAgICA8Rm9ybSB2YWx1ZXM9e3RoaXMuc3RhdGUudmFsdWVzfSBzZXREYXRhPXt0aGlzLnNldERhdGF9IGRhdGE9e3RoaXMuc3RhdGUuZGF0YX0gZXJyb3JzPXt0aGlzLnN0YXRlLmVycm9yc30gc3VibWl0PXt0aGlzLnN1Ym1pdH0vPlxuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgPC9QYW5lbD5cbiAgICAgICAgICAgIDwvTG9hZGVyPlxuICAgICAgICApXG4gICAgfVxufSk7IiwiaW1wb3J0IFJlYWN0IGZyb20gJ3JlYWN0J1xuXG5leHBvcnQgZGVmYXVsdCBSZWFjdC5jcmVhdGVDbGFzcyh7XG5cbiAgICByZW5kZXI6IGZ1bmN0aW9uKCkge1xuICAgICAgICByZXR1cm4gKFxuICAgICAgICAgICAgPGZvcm0gb25TdWJtaXQ9e3RoaXMucHJvcHMuc3VibWl0fT5cbiAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzTmFtZT1cImZvcm0tZ3JvdXBcIj5cbiAgICAgICAgICAgICAgICAgICAgPGxhYmVsPkVtYWlsPC9sYWJlbD5cbiAgICAgICAgICAgICAgICAgICAgPGlucHV0IHR5cGU9XCJlbWFpbFwiIGNsYXNzTmFtZT1cImZvcm0tY29udHJvbFwiIHZhbHVlPXt0aGlzLnByb3BzLmRhdGEuZW1haWx9IG9uQ2hhbmdlPXsoZSk9PnRoaXMucHJvcHMuc2V0RGF0YSh7IGVtYWlsOiBlLnRhcmdldC52YWx1ZX0pfSBhdXRvQ29tcGxldGU9XCJvZmZcIi8+XG4gICAgICAgICAgICAgICAgICAgIHt0aGlzLnByb3BzLmVycm9ycy5lbWFpbCAmJiA8c3BhbiBjbGFzc05hbWU9XCJlcnJvclwiPnt0aGlzLnByb3BzLmVycm9ycy5lbWFpbH08L3NwYW4+fVxuICAgICAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICAgICAgICAgIDxkaXYgY2xhc3NOYW1lPVwiZm9ybS1ncm91cFwiPlxuICAgICAgICAgICAgICAgICAgICA8bGFiZWw+UGFzc3dvcmQ8L2xhYmVsPlxuICAgICAgICAgICAgICAgICAgICA8aW5wdXQgdHlwZT1cInBhc3N3b3JkXCIgY2xhc3NOYW1lPVwiZm9ybS1jb250cm9sXCIgdmFsdWU9e3RoaXMucHJvcHMuZGF0YS5wYXNzd29yZH0gb25DaGFuZ2U9eyhlKT0+dGhpcy5wcm9wcy5zZXREYXRhKHsgcGFzc3dvcmQ6IGUudGFyZ2V0LnZhbHVlfSl9IGF1dG9Db21wbGV0ZT1cIm9mZlwiLz5cbiAgICAgICAgICAgICAgICAgICAge3RoaXMucHJvcHMuZXJyb3JzLnBhc3N3b3JkICYmIDxzcGFuIGNsYXNzTmFtZT1cImVycm9yXCI+e3RoaXMucHJvcHMuZXJyb3JzLnBhc3N3b3JkfTwvc3Bhbj59XG4gICAgICAgICAgICAgICAgPC9kaXY+XG4gICAgICAgICAgICAgICAgPGRpdiBjbGFzc05hbWU9XCJmb3JtLWdyb3VwXCI+XG4gICAgICAgICAgICAgICAgICAgIDxsYWJlbD5Db25maXJtIHBhc3N3b3JkPC9sYWJlbD5cbiAgICAgICAgICAgICAgICAgICAgPGlucHV0IHR5cGU9XCJwYXNzd29yZFwiIGNsYXNzTmFtZT1cImZvcm0tY29udHJvbFwiIHZhbHVlPXt0aGlzLnByb3BzLmRhdGEucGFzc3dvcmRfY29uZmlybWF0aW9ufSBvbkNoYW5nZT17KGUpPT50aGlzLnByb3BzLnNldERhdGEoeyBwYXNzd29yZF9jb25maXJtYXRpb246IGUudGFyZ2V0LnZhbHVlfSl9IGF1dG9Db21wbGV0ZT1cIm9mZlwiLz5cbiAgICAgICAgICAgICAgICAgICAge3RoaXMucHJvcHMuZXJyb3JzLnBhc3N3b3JkX2NvbmZpcm1hdGlvbiAmJiA8c3BhbiBjbGFzc05hbWU9XCJlcnJvclwiPnt0aGlzLnByb3BzLmVycm9ycy5wYXNzd29yZF9jb25maXJtYXRpb259PC9zcGFuPn1cbiAgICAgICAgICAgICAgICA8L2Rpdj5cbiAgICAgICAgICAgICAgICA8YnV0dG9uIGNsYXNzTmFtZT1cImJ0biBidG4tYmxvY2sgYnRuLXByaW1hcnlcIiB0eXBlPVwic3VibWl0XCI+Q29udGludWU8L2J1dHRvbj5cbiAgICAgICAgICAgIDwvZm9ybT5cbiAgICAgICAgKVxuICAgIH1cbn0pOyIsImltcG9ydCBSZWFjdCBmcm9tICdyZWFjdCdcbmltcG9ydCB7IFBhbmVsLCBMb2FkZXIgfSBmcm9tICdjb21wb25lbnRzJ1xuaW1wb3J0IEZvcm0gZnJvbSAnLi9mb3JtJ1xuaW1wb3J0IGFwaSBmcm9tICcuLi8uLi9hcGknXG5cbmV4cG9ydCBkZWZhdWx0IFJlYWN0LmNyZWF0ZUNsYXNzKHtcblxuICAgIGdldEluaXRpYWxTdGF0ZTogZnVuY3Rpb24oKSB7XG4gICAgICAgIHJldHVybiB7XG4gICAgICAgICAgICBkYXRhOiB7XG4gICAgICAgICAgICAgICAgZW1haWw6ICcnLFxuICAgICAgICAgICAgICAgIHBhc3N3b3JkOiAnJyxcbiAgICAgICAgICAgICAgICBwYXNzd29yZF9jb25maXJtYXRpb246ICcnLFxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIGVycm9yczoge30sXG4gICAgICAgICAgICBpc19mZXRjaGluZzogZmFsc2VcbiAgICAgICAgfVxuICAgIH0sXG5cblxuICAgIHNldERhdGE6IGZ1bmN0aW9uKGRhdGEpIHtcbiAgICAgICAgdmFyIGRhdGEgPSBPYmplY3QuYXNzaWduKHt9LCB0aGlzLnN0YXRlLmRhdGEsIGRhdGEpXG4gICAgICAgIHRoaXMuc2V0U3RhdGUoeyBkYXRhIH0pXG4gICAgfSxcblxuXG4gICAgc3VibWl0OiBmdW5jdGlvbihlKSB7XG4gICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgdGhpcy5zZXRTdGF0ZSh7IGlzX2ZldGNoaW5nOiB0cnVlfSwgKCkgPT4ge1xuICAgICAgICAgICAgYXBpLnJlZ2lzdHJhdGlvbih0aGlzLnN0YXRlLmRhdGEpXG4gICAgICAgICAgICAgICAgLnRoZW4oKHJlcykgPT4ge1xuICAgICAgICAgICAgICAgICAgICB0aGlzLnNldFN0YXRlKHsgZXJyb3JzOiB7fSwgaXNfZmV0Y2hpbmc6IGZhbHNlIH0pO1xuICAgICAgICAgICAgICAgICAgICB0aGlzLnByb3BzLnNldExvZ2dlZCh0cnVlKTtcbiAgICAgICAgICAgICAgICB9KVxuICAgICAgICAgICAgICAgIC5jYXRjaCgoZXJyb3JzKT0+e1xuICAgICAgICAgICAgICAgICAgICB0aGlzLnNldFN0YXRlKHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGlzX2ZldGNoaW5nOiBmYWxzZSxcbiAgICAgICAgICAgICAgICAgICAgICAgIGVycm9yc1xuICAgICAgICAgICAgICAgICAgICB9KVxuICAgICAgICAgICAgICAgIH0pXG4gICAgICAgIH0pXG4gICAgfSxcblxuICAgIHJlbmRlcjogZnVuY3Rpb24oKSB7XG4gICAgICAgIHJldHVybiAoXG4gICAgICAgICAgICA8TG9hZGVyIGlzX2ZldGNoaW5nPXt0aGlzLnN0YXRlLmlzX2ZldGNoaW5nfT5cbiAgICAgICAgICAgICAgICA8UGFuZWwgdGl0bGU9XCJSZWdpc3RyYXRpb25cIj5cbiAgICAgICAgICAgICAgICAgICAgPEZvcm0gdmFsdWVzPXt0aGlzLnN0YXRlLnZhbHVlc30gc2V0RGF0YT17dGhpcy5zZXREYXRhfSBkYXRhPXt0aGlzLnN0YXRlLmRhdGF9IGVycm9ycz17dGhpcy5zdGF0ZS5lcnJvcnN9IHN1Ym1pdD17dGhpcy5zdWJtaXR9Lz5cbiAgICAgICAgICAgICAgICA8L1BhbmVsPlxuICAgICAgICAgICAgPC9Mb2FkZXI+XG4gICAgICAgIClcbiAgICB9XG59KTsiLCJpbXBvcnQgUmVhY3QgZnJvbSAncmVhY3QnXG5cbmV4cG9ydCBkZWZhdWx0IFJlYWN0LmNyZWF0ZUNsYXNzKHtcblxuICAgIHJlbmRlcjogZnVuY3Rpb24oKSB7XG4gICAgICAgIHJldHVybiAoXG4gICAgICAgICAgICA8Zm9ybSBvblN1Ym1pdD17dGhpcy5wcm9wcy5zdWJtaXR9PlxuICAgICAgICAgICAgICAgIDxkaXYgY2xhc3NOYW1lPVwiZm9ybS1ncm91cFwiPlxuICAgICAgICAgICAgICAgICAgICA8bGFiZWw+RW1haWw8L2xhYmVsPlxuICAgICAgICAgICAgICAgICAgICA8aW5wdXQgdHlwZT1cImVtYWlsXCIgY2xhc3NOYW1lPVwiZm9ybS1jb250cm9sXCIgdmFsdWU9e3RoaXMucHJvcHMuZGF0YS5lbWFpbH0gb25DaGFuZ2U9eyhlKT0+dGhpcy5wcm9wcy5zZXREYXRhKHsgZW1haWw6IGUudGFyZ2V0LnZhbHVlfSl9Lz5cbiAgICAgICAgICAgICAgICAgICAge3RoaXMucHJvcHMuZXJyb3JzLmVtYWlsICYmIDxzcGFuIGNsYXNzTmFtZT1cImVycm9yXCI+e3RoaXMucHJvcHMuZXJyb3JzLmVtYWlsfTwvc3Bhbj59XG4gICAgICAgICAgICAgICAgPC9kaXY+XG4gICAgICAgICAgICAgICAgPGJ1dHRvbiBjbGFzc05hbWU9XCJidG4gYnRuLWJsb2NrIGJ0bi1wcmltYXJ5XCIgdHlwZT1cInN1Ym1pdFwiPkNvbnRpbnVlPC9idXR0b24+XG4gICAgICAgICAgICA8L2Zvcm0+XG4gICAgICAgIClcbiAgICB9XG59KSIsImltcG9ydCBSZWFjdCBmcm9tICdyZWFjdCdcbmltcG9ydCB7IFBhbmVsLCBMb2FkZXIgfSBmcm9tICdjb21wb25lbnRzJ1xuaW1wb3J0IEZvcm0gZnJvbSAnLi9mb3JtJ1xuaW1wb3J0IGFwaSBmcm9tICcuLi8uLi9hcGknXG5cbmV4cG9ydCBkZWZhdWx0IFJlYWN0LmNyZWF0ZUNsYXNzKHtcblxuICAgIGdldEluaXRpYWxTdGF0ZTogZnVuY3Rpb24oKSB7XG4gICAgICAgIHJldHVybiB7XG4gICAgICAgICAgICBkYXRhOiB7XG4gICAgICAgICAgICAgICAgZW1haWw6ICcnXG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgZXJyb3JzOiB7fSxcbiAgICAgICAgICAgIGlzX2ZldGNoaW5nOiBmYWxzZSxcbiAgICAgICAgICAgIGlzX2NvZGVfc2VudDogZmFsc2VcbiAgICAgICAgfVxuICAgIH0sXG5cblxuICAgIHNldERhdGE6IGZ1bmN0aW9uKGRhdGEpIHtcbiAgICAgICAgdmFyIGRhdGEgPSBPYmplY3QuYXNzaWduKHt9LCB0aGlzLnN0YXRlLmRhdGEsIGRhdGEpXG4gICAgICAgIHRoaXMuc2V0U3RhdGUoeyBkYXRhIH0pXG4gICAgfSxcblxuXG4gICAgc3VibWl0OiBmdW5jdGlvbihlKSB7XG4gICAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgICAgdGhpcy5zZXRTdGF0ZSh7IGlzX2ZldGNoaW5nOiB0cnVlfSwgKCkgPT4ge1xuICAgICAgICAgICAgYXBpLnJlc2V0UGFzc3dvcmQodGhpcy5zdGF0ZS5kYXRhKVxuICAgICAgICAgICAgICAgIC50aGVuKCgpID0+IHtcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5zZXRTdGF0ZSh7XG4gICAgICAgICAgICAgICAgICAgICAgICBpc19mZXRjaGluZzogZmFsc2UsXG4gICAgICAgICAgICAgICAgICAgICAgICBpc19jb2RlX3NlbnQ6IHRydWVcbiAgICAgICAgICAgICAgICAgICAgfSlcbiAgICAgICAgICAgICAgICB9KVxuICAgICAgICAgICAgICAgIC5jYXRjaCgoZXJyb3JzKT0+e1xuICAgICAgICAgICAgICAgICAgICB0aGlzLnNldFN0YXRlKHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGlzX2ZldGNoaW5nOiBmYWxzZSxcbiAgICAgICAgICAgICAgICAgICAgICAgIGVycm9yc1xuICAgICAgICAgICAgICAgICAgICB9KVxuICAgICAgICAgICAgICAgIH0pXG4gICAgICAgIH0pXG4gICAgfSxcblxuICAgIHJlbmRlcjogZnVuY3Rpb24oKSB7XG4gICAgICAgIHJldHVybiAoXG4gICAgICAgICAgICA8TG9hZGVyIGlzX2ZldGNoaW5nPXt0aGlzLnN0YXRlLmlzX2ZldGNoaW5nfT5cbiAgICAgICAgICAgICAgICA8UGFuZWwgdGl0bGU9XCJQYXNzd29yZCByZXN0b3JlXCI+XG4gICAgICAgICAgICAgICAgICAgIHt0aGlzLnN0YXRlLmlzX2NvZGVfc2VudCA/XG4gICAgICAgICAgICAgICAgICAgICAgICA8ZGl2PlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxkaXYgY2xhc3NOYW1lPVwiYWxlcnQgYWxlcnQtc3VjY2Vzc1wiPkNvZGUgaGFzIGJlZW4gc2VudCB0byBlbWFpbC48L2Rpdj5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICA8YnV0dG9uIGNsYXNzTmFtZT1cImJ0biBidG4tYmxvY2sgYnRuLXByaW1hcnlcIiBvbkNsaWNrPXsoKT0+dGhpcy5wcm9wcy5zZXRTZWN0aW9uKCdOZXdQYXNzd29yZCcpfT5Db250aW51ZTwvYnV0dG9uPlxuICAgICAgICAgICAgICAgICAgICAgICAgPC9kaXY+XG4gICAgICAgICAgICAgICAgICAgICAgICA6XG4gICAgICAgICAgICAgICAgICAgICAgICA8Rm9ybSB2YWx1ZXM9e3RoaXMuc3RhdGUudmFsdWVzfSBzZXREYXRhPXt0aGlzLnNldERhdGF9IGRhdGE9e3RoaXMuc3RhdGUuZGF0YX0gZXJyb3JzPXt0aGlzLnN0YXRlLmVycm9yc30gc3VibWl0PXt0aGlzLnN1Ym1pdH0vPlxuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgPC9QYW5lbD5cbiAgICAgICAgICAgIDwvTG9hZGVyPlxuICAgICAgICApXG4gICAgfVxufSk7IiwiaW1wb3J0IEF1dGhlbnRpY2F0ZWQgZnJvbSAnLi9jb21wb25lbnRzL2F1dGhlbnRpY2F0ZWQnXG5cbmV4cG9ydCBkZWZhdWx0IHtcbiAgICBjb21wb25lbnRzOiB7XG4gICAgICAgIEF1dGhlbnRpY2F0ZWRcbiAgICB9XG59IiwiaW1wb3J0ICd3aGF0d2ctZmV0Y2gnXG5pbXBvcnQgdG9rZW4gZnJvbSAnbGlicy90b2tlbidcbmltcG9ydCBjbGllbnQgZnJvbSAnbGlicy9jbGllbnQnXG5cblxuZnVuY3Rpb24gcmVxdWVzdChtZXRob2QsIHVybCkge1xuICAgIHZhciBoZWFkZXJzID0gbmV3IEhlYWRlcnMoe1xuICAgICAgICAnWC1SZXF1ZXN0ZWQtV2l0aCc6ICdYTUxIdHRwUmVxdWVzdCcsXG4gICAgICAgICdBdXRob3JpemF0aW9uJzogJ0JlYXJlciAnICsgdG9rZW4uZ2V0KClcbiAgICB9KVxuICAgIHZhciByZXEgPSB7XG4gICAgICAgIGNyZWRlbnRpYWxzOiAnc2FtZS1vcmlnaW4nLFxuICAgICAgICBtZXRob2QsXG4gICAgICAgIGhlYWRlcnNcbiAgICB9XG4gICAgcmV0dXJuIGZldGNoKHVybCwgcmVxKVxuICAgICAgICAudGhlbigocmVzcG9uc2UpID0+IHtcbiAgICAgICAgICAgIHJldHVybiByZXNwb25zZS5qc29uKCkudGhlbigoZGF0YSkgPT4ge1xuICAgICAgICAgICAgICAgIGlmKHJlc3BvbnNlLnN0YXR1cyAhPT0gMjAwKSB7XG4gICAgICAgICAgICAgICAgICAgIGlmKGRhdGEuZXJyb3IgPT0gJ2FjY2Vzc19kZW5pZWQnKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICB0b2tlbi5zZXQobnVsbCk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIFByb21pc2UucmVqZWN0KGRhdGEpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICByZXR1cm4gUHJvbWlzZS5yZXNvbHZlKGRhdGEpO1xuICAgICAgICAgICAgfSlcbiAgICAgICAgfSlcbn1cblxuXG5leHBvcnQgZGVmYXVsdCB7XG5cbiAgICBnZXRBdXRoRGV0YWlsczogZnVuY3Rpb24oKSB7XG4gICAgICAgIHJldHVybiByZXF1ZXN0KCdHRVQnLCAnL29hdXRoX3NlcnZlci9hdXRob3JpemF0aW9uJyArIHdpbmRvdy5sb2NhdGlvbi5zZWFyY2gpXG4gICAgfSxcblxuICAgIGF1dGhvcml6ZTogZnVuY3Rpb24oKSB7XG4gICAgICAgIHJldHVybiByZXF1ZXN0KCdQT1NUJywgJy9vYXV0aF9zZXJ2ZXIvYXV0aG9yaXphdGlvbi9hdXRob3JpemUnICsgd2luZG93LmxvY2F0aW9uLnNlYXJjaClcbiAgICAgICAgICAgIC50aGVuKHJlcz0+Y2xpZW50LmF1dGhvcml6ZShyZXMpKVxuICAgIH0sXG5cbiAgICBkZW55OiBmdW5jdGlvbihjbGllbnRfZGV0YWlscykge1xuICAgICAgICByZXR1cm4gcmVxdWVzdCgnUE9TVCcsICcvb2F1dGhfc2VydmVyL2F1dGhvcml6YXRpb24vZGVueScgKyB3aW5kb3cubG9jYXRpb24uc2VhcmNoKVxuICAgICAgICAgICAgLnRoZW4ocmVzPT5jbGllbnQuZGVueShyZXMpKVxuICAgIH1cblxufSIsImltcG9ydCBSZWFjdCBmcm9tICdyZWFjdCdcbmltcG9ydCB7IFBhbmVsLCBMb2FkZXIgfSBmcm9tICdjb21wb25lbnRzJ1xuaW1wb3J0IGFwaSBmcm9tICcuLi9hcGknXG5pbXBvcnQgY2xpZW50IGZyb20gJ2xpYnMvY2xpZW50J1xuXG5leHBvcnQgZGVmYXVsdCBSZWFjdC5jcmVhdGVDbGFzcyh7XG5cbiAgICBnZXRJbml0aWFsU3RhdGU6IGZ1bmN0aW9uKCkge1xuICAgICAgICByZXR1cm4ge1xuICAgICAgICAgICAgaXNfZmV0Y2hpbmc6IHRydWUsXG4gICAgICAgICAgICBhdXRoX2RldGFpbHM6IGZhbHNlLFxuICAgICAgICAgICAgZXJyb3I6IGZhbHNlXG4gICAgICAgIH1cbiAgICB9LFxuXG5cbiAgICBjb21wb25lbnRXaWxsTW91bnQ6IGZ1bmN0aW9uKCkge1xuICAgICAgICBhcGkuZ2V0QXV0aERldGFpbHMoKVxuICAgICAgICAgICAgLnRoZW4oKGF1dGhfZGV0YWlscykgPT4ge1xuICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKGF1dGhfZGV0YWlscylcbiAgICAgICAgICAgICAgICB0aGlzLnNldFN0YXRlKHtcbiAgICAgICAgICAgICAgICAgICAgYXV0aF9kZXRhaWxzLFxuICAgICAgICAgICAgICAgICAgICBlcnJvcjogZmFsc2UsXG4gICAgICAgICAgICAgICAgICAgIGlzX2ZldGNoaW5nOiBmYWxzZVxuICAgICAgICAgICAgICAgIH0pXG4gICAgICAgICAgICB9KVxuICAgICAgICAgICAgLmNhdGNoKChlcnJvcikgPT4ge1xuICAgICAgICAgICAgICAgIHRoaXMuc2V0U3RhdGUoe1xuICAgICAgICAgICAgICAgICAgICBhdXRoX2RldGFpbHM6IGZhbHNlLFxuICAgICAgICAgICAgICAgICAgICBlcnJvcjogZXJyb3IsXG4gICAgICAgICAgICAgICAgICAgIGlzX2ZldGNoaW5nOiBmYWxzZVxuICAgICAgICAgICAgICAgIH0pXG4gICAgICAgICAgICB9KVxuICAgIH0sXG5cbiAgICBhdXRob3JpemU6IGZ1bmN0aW9uKCkge1xuICAgICAgICBhcGkuYXV0aG9yaXplKCk7XG4gICAgfSxcblxuICAgIGRlbnk6IGZ1bmN0aW9uKCkge1xuICAgICAgICBhcGkuZGVueSgpO1xuICAgIH0sXG5cbiAgICByZW5kZXI6IGZ1bmN0aW9uKCkge1xuICAgICAgICByZXR1cm4gKFxuICAgICAgICAgICAgPExvYWRlciBpc19mZXRjaGluZz17dGhpcy5zdGF0ZS5pc19mZXRjaGluZ30+XG4gICAgICAgICAgICAgICAgPFBhbmVsIHRpdGxlPVwiQXV0aG9yaXphdGlvblwiPlxuICAgICAgICAgICAgICAgICAgICB7dGhpcy5zdGF0ZS5hdXRoX2RldGFpbHMgJiZcbiAgICAgICAgICAgICAgICAgICAgICAgIDxkaXY+XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgPHA+PHN0cm9uZz57dGhpcy5zdGF0ZS5hdXRoX2RldGFpbHMuY2xpZW50Lm5hbWV9PC9zdHJvbmc+IHBsYXRmb3JtIHJlcXVpcmUgYXV0aG9yaXphdGlvbjwvcD5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzTmFtZT1cInJvd1wiPlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzTmFtZT1cImNvbC14cy02XCI+XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8YnV0dG9uIGNsYXNzTmFtZT1cImJ0biBidG4tYmxvY2sgYnRuLXN1Y2Nlc3NcIiBvbkNsaWNrPXt0aGlzLmF1dGhvcml6ZX0+QXV0aG9yaXplPC9idXR0b24+XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzTmFtZT1cImNvbC14cy02XCI+XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8YnV0dG9uIGNsYXNzTmFtZT1cImJ0biBidG4tYmxvY2sgYnRuLWRhbmdlclwiIG9uQ2xpY2s9e3RoaXMuZGVueX0+RGVueTwvYnV0dG9uPlxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8L2Rpdj5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICA8L2Rpdj5cbiAgICAgICAgICAgICAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIHsgdGhpcy5zdGF0ZS5lcnJvciAmJlxuICAgICAgICAgICAgICAgICAgICAgICAgPGRpdiBjbGFzc05hbWU9XCJhbGVydCBhbGVydC1kYW5nZXJcIj57dGhpcy5zdGF0ZS5lcnJvci5lcnJvcl9kZXNjcmlwdGlvbn08L2Rpdj5cbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIDwvUGFuZWw+XG4gICAgICAgICAgICA8L0xvYWRlcj5cbiAgICAgICAgKVxuICAgIH1cbn0pIiwiaW1wb3J0IHJvdXRlcyBmcm9tICcuL3JvdXRlcydcblxuZXhwb3J0IGRlZmF1bHQgeyByb3V0ZXMgfSIsImltcG9ydCBSZWFjdCBmcm9tICdyZWFjdCdcbmltcG9ydCB7IFJvdXRlIH0gZnJvbSAncmVhY3Qtcm91dGVyJ1xuXG5pbXBvcnQgQ29tcG9uZW50IGZyb20gJy4vY29tcG9uZW50cy9hdXRob3JpemF0aW9uJ1xuXG5leHBvcnQgZGVmYXVsdCAoXG4gICAgPFJvdXRlIHBhdGg9XCIvYXV0aG9yaXphdGlvblwiIGNvbXBvbmVudD17Q29tcG9uZW50fS8+XG4pIiwiaW1wb3J0IFJlYWN0IGZyb20gJ3JlYWN0J1xuXG5leHBvcnQgZGVmYXVsdCBSZWFjdC5jcmVhdGVDbGFzcyh7XG4gICAgcmVuZGVyOiBmdW5jdGlvbigpIHtcbiAgICAgICAgcmV0dXJuIChcbiAgICAgICAgICAgIDxkaXYgY2xhc3NOYW1lPVwiYWxlcnQgYWxlcnQtZGFuZ2VyXCI+XG4gICAgICAgICAgICAgICAgU29ycnkhIFBhZ2Ugbm90IGZvdW5kLlxuICAgICAgICAgICAgPC9kaXY+XG4gICAgICAgIClcbiAgICB9XG59KSIsImltcG9ydCBSZWFjdCBmcm9tICdyZWFjdCdcblxuZXhwb3J0IGRlZmF1bHQgUmVhY3QuY3JlYXRlQ2xhc3Moe1xuICAgIHJlbmRlcjogZnVuY3Rpb24oKSB7XG4gICAgICAgIHJldHVybiAoXG4gICAgICAgICAgICA8ZGl2IGNsYXNzTmFtZT1cImFsZXJ0IGFsZXJ0LWRhbmdlclwiPlxuICAgICAgICAgICAgICAgIFdob29wcyEgU29tZXRoaW5nIHdlbnQgd3Jvbmcgb24gb3VyIGVuZC5cbiAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICApXG4gICAgfVxufSkiLCJpbXBvcnQgUmVhY3QgZnJvbSAncmVhY3QnXG5pbXBvcnQgeyBSb3V0ZSB9IGZyb20gJ3JlYWN0LXJvdXRlcidcblxuaW1wb3J0IE5vdEZvdW5kIGZyb20gJy4vY29tcG9uZW50cy9ub3RfZm91bmQnXG5pbXBvcnQgU2VydmVyRXJyb3IgZnJvbSAnLi9jb21wb25lbnRzL3NlcnZlcl9lcnJvcidcblxuZXhwb3J0IGRlZmF1bHQgKFxuICAgIDxSb3V0ZT5cbiAgICAgICAgPFJvdXRlIHBhdGg9XCIvc2VydmVyLWVycm9yXCIgY29tcG9uZW50PXtTZXJ2ZXJFcnJvcn0vPlxuICAgICAgICA8Um91dGUgcGF0aD1cIipcIiBjb21wb25lbnQ9e05vdEZvdW5kfSAvPlxuICAgIDwvUm91dGU+XG4pIiwiaW1wb3J0IFJlYWN0IGZyb20gJ3JlYWN0J1xuXG5leHBvcnQgZGVmYXVsdCBSZWFjdC5jcmVhdGVDbGFzcyh7XG5cbiAgICByZW5kZXI6IGZ1bmN0aW9uKCkge1xuICAgICAgICByZXR1cm4gKFxuICAgICAgICAgICAgPGRpdj5cbiAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzTmFtZT1cImNvbnRhaW5lclwiPlxuICAgICAgICAgICAgICAgICAgICB7dGhpcy5wcm9wcy5jaGlsZHJlbn1cbiAgICAgICAgICAgICAgICA8L2Rpdj5cbiAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICApXG4gICAgfVxufSk7IiwiaW1wb3J0IExheW91dCBmcm9tICcuL2NvbXBvbmVudHMvbGF5b3V0J1xuXG5leHBvcnQgZGVmYXVsdCB7XG4gICAgY29tcG9uZW50czoge1xuICAgICAgICBMYXlvdXRcbiAgICB9XG59Il19
