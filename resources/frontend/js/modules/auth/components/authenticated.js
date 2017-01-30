import React from 'react'
import token from 'libs/token'

import Login from './login/section'
import ResetPassword from './reset_password/section'
import NewPassword from './new_password/section'
import Registration from './registration/section'

var sections = {
    Login,
    ResetPassword,
    NewPassword,
    Registration
}

export default React.createClass({

    getInitialState: function() {
        return {
            is_logged: token.check(),
            section: 'Login'
        }
    },


    setSection: function(section) {
        this.setState({ section });
    },


    setLogged: function(is_logged) {
        this.setState({ is_logged })
    },

    render: function() {
        if(this.state.is_logged) {
            return this.props.children
        }
        return React.createElement(sections[this.state.section], { setSection: this.setSection, setLogged: this.setLogged })
    }

});