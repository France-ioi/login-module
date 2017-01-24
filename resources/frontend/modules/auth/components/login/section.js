import React from 'react'
import { Panel, Loader } from 'components'
import Form from './form'
import api from '../../api'

export default React.createClass({

    getInitialState: function() {
        return {
            data: {
                email: '',
                password: ''
            },
            errors: {},
            is_fetching: false
        }
    },


    setData: function(data) {
        var data = Object.assign({}, this.state.data, data)
        this.setState({ data })
    },


    submit: function(e) {
        e.preventDefault();
        this.setState({ is_fetching: true}, () => {
            api.login(this.state.data)
                .then(()=>{
                    this.setState({ is_fetching: false });
                    this.props.setLogged(true);
                })
                .catch((errors)=>{
                    this.setState({
                        is_fetching: false,
                        errors
                    })
                })
        })
    },


    render: function() {
        return (
            <Loader is_fetching={this.state.is_fetching}>
                <div>
                    <Panel title="Login">
                        <Form values={this.state.values} setData={this.setData} data={this.state.data} errors={this.state.errors} onSubmit={this.submit}/>
                    </Panel>

                    <Panel>
                        <div className="row">
                            <div className="col-xs-6">
                                <button className="btn btn-block btn-primary" onClick={()=>this.props.setSection('Registration')}>Create account</button>
                            </div>
                            <div className="col-xs-6">
                                <button className="btn btn-block btn-primary" onClick={()=>this.props.setSection('ResetPassword')}>Reset password</button>
                            </div>
                        </div>
                    </Panel>

                    <Panel title="Or sign up with">
                        <div className="row">
                            <div className="col-xs-6">
                                <a className="btn btn-block btn-default" href={'/oauth_client/redirect/facebook' + window.location.search} onClick={()=>this.setState({is_fetching: true})}>Facebook</a>
                            </div>
                            <div className="col-xs-6">
                                <a className="btn btn-block btn-default" href={'/oauth_client/redirect/google' + window.location.search} onClick={()=>this.setState({is_fetching: true})}>Google</a>
                            </div>
                        </div>
                    </Panel>

                </div>
            </Loader>
        )
    }

});