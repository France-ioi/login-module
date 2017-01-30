import React from 'react'
import { Panel, Loader } from 'components'
import Form from './form'
import api from '../../api'

export default React.createClass({

    getInitialState: function() {
        return {
            data: {
                email: '',
                token: '',
                password: '',
                password_confirmation: '',
            },
            errors: {},
            is_fetching: false,
            is_password_changed: false
        }
    },


    setData: function(data) {
        var data = Object.assign({}, this.state.data, data)
        this.setState({ data })
    },


    submit: function(e) {
        e.preventDefault();
        this.setState({ is_fetching: true}, () => {
            api.newPassword(this.state.data)
                .then(() => {
                    this.setState({
                        is_fetching: false,
                        is_password_changed: true
                    })
                })
                .catch((errors)=>{
                    this.setState({
                        is_fetching: false,
                        errors
                    })
                })
        })
    },


    navLogin: function() {
        this.props.setSection('Login');
    },

    render: function() {
        return (
            <Loader is_fetching={this.state.is_fetching}>
                <Panel title="New password">
                    {this.state.is_password_changed ?
                        <div>
                            <div className="alert alert-success">Password changed, you can login now</div>
                            <button className="btn btn-primary" onClick={this.navLogin}>Continue</button>
                        </div>
                        :
                        <Form values={this.state.values} setData={this.setData} data={this.state.data} errors={this.state.errors} submit={this.submit} cancel={this.navLogin}/>
                    }
                </Panel>
            </Loader>
        )
    }
});