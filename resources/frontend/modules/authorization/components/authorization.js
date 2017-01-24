import React from 'react'
import { Panel, Loader } from 'components'
import api from '../api'
import client from 'libs/client'

export default React.createClass({

    getInitialState: function() {
        return {
            is_fetching: true,
            auth_details: false,
            error: false
        }
    },


    componentWillMount: function() {
        api.getAuthDetails()
            .then((auth_details) => {
                console.log(auth_details)
                this.setState({
                    auth_details,
                    error: false,
                    is_fetching: false
                })
            })
            .catch((error) => {
                this.setState({
                    auth_details: false,
                    error: error,
                    is_fetching: false
                })
            })
    },

    authorize: function() {
        api.authorize();
    },

    deny: function() {
        api.deny();
    },

    render: function() {
        return (
            <Loader is_fetching={this.state.is_fetching}>
                <Panel title="Authorization">
                    {this.state.auth_details &&
                        <div>
                            <p><strong>{this.state.auth_details.client.name}</strong> platform require authorization</p>
                            <div className="row">
                                <div className="col-xs-6">
                                    <button className="btn btn-block btn-success" onClick={this.authorize}>Authorize</button>
                                </div>
                                <div className="col-xs-6">
                                    <button className="btn btn-block btn-danger" onClick={this.deny}>Deny</button>
                                </div>
                            </div>
                        </div>
                    }
                    { this.state.error &&
                        <div className="alert alert-danger">{this.state.error.error_description}</div>
                    }
                </Panel>
            </Loader>
        )
    }
})