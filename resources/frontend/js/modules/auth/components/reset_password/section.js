import React from 'react'
import { Panel, Loader } from 'components'
import Form from './form'
import api from '../../api'

export default React.createClass({

    getInitialState: function() {
        return {
            data: {
                email: ''
            },
            errors: {},
            is_fetching: false,
            is_code_sent: false
        }
    },


    setData: function(data) {
        var data = Object.assign({}, this.state.data, data)
        this.setState({ data })
    },


    submit: function(e) {
        e.preventDefault();
        this.setState({ is_fetching: true}, () => {
            api.resetPassword(this.state.data)
                .then(() => {
                    this.setState({
                        is_fetching: false,
                        is_code_sent: true
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

    render: function() {
        return (
            <Loader is_fetching={this.state.is_fetching}>
                <Panel title="Password restore">
                    {this.state.is_code_sent ?
                        <div>
                            <div className="alert alert-success">Code has been sent to email.</div>
                            <button className="btn btn-block btn-primary" onClick={()=>this.props.setSection('NewPassword')}>Continue</button>
                        </div>
                        :
                        <Form values={this.state.values} setData={this.setData} data={this.state.data} errors={this.state.errors} submit={this.submit}/>
                    }
                </Panel>
            </Loader>
        )
    }
});