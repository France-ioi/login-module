import React from 'react'

export default React.createClass({

    render: function() {
        return (
            <form onSubmit={this.props.submit}>
                <div className="form-group">
                    <label>Email</label>
                    <input type="text" className="form-control" value={this.props.data.email} onChange={(e)=>this.props.setData({ email: e.target.value})}/>
                    {this.props.errors.email && <span className="error">{this.props.errors.email}</span>}
                </div>
                <div className="form-group">
                    <label>Token</label>
                    <input type="text" className="form-control" value={this.props.data.token} onChange={(e)=>this.props.setData({ token: e.target.value})}/>
                    {this.props.errors.token && <span className="error">{this.props.errors.token}</span>}
                </div>
                <div className="form-group">
                    <label>New password</label>
                    <input type="password" className="form-control" value={this.props.data.password} onChange={(e)=>this.props.setData({ password: e.target.value})}/>
                    {this.props.errors.password && <span className="error">{this.props.errors.password}</span>}
                </div>
                <div className="form-group">
                    <label>Confirm password</label>
                    <input type="password" className="form-control" value={this.props.data.password_confirmation} onChange={(e)=>this.props.setData({ password_confirmation: e.target.value})}/>
                    {this.props.errors.password_confirmation && <span className="error">{this.props.errors.password_confirmation}</span>}
                </div>
                <button className="btn btn-block btn-primary" type="submit">Continue</button>
            </form>
        )
    }
});