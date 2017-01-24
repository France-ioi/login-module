import React from 'react'
import { Panel, Loader } from 'components'
import Form from './form'
import api from '../../api'

export default React.createClass({

    getInitialState: function() {
        return {
            data: {
                email: '',
                password: '',
                password_confirmation: '',
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
            api.registration(this.state.data)
                .then((res) => {
                    this.setState({ errors: {}, is_fetching: false });
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
                <Panel title="Registration">
                    <Form values={this.state.values} setData={this.setData} data={this.state.data} errors={this.state.errors} submit={this.submit}/>
                </Panel>
            </Loader>
        )
    }
});