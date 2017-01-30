import React from 'react'

export default React.createClass({

    render: function() {
        return (
            <form onSubmit={this.props.submit}>
                <div className="form-group">
                    <label>Email</label>
                    <input type="email" className="form-control" value={this.props.data.email} onChange={(e)=>this.props.setData({ email: e.target.value})} autoComplete="off"/>
                    {this.props.errors.email && <span className="error">{this.props.errors.email}</span>}
                </div>
                <div className="form-group">
                    <label>Password</label>
                    <input type="password" className="form-control" value={this.props.data.password} onChange={(e)=>this.props.setData({ password: e.target.value})} autoComplete="off"/>
                    {this.props.errors.password && <span className="error">{this.props.errors.password}</span>}
                </div>
                <div className="form-group">
                    <label>Confirm password</label>
                    <input type="password" className="form-control" value={this.props.data.password_confirmation} onChange={(e)=>this.props.setData({ password_confirmation: e.target.value})} autoComplete="off"/>
                    {this.props.errors.password_confirmation && <span className="error">{this.props.errors.password_confirmation}</span>}
                </div>
                <button className="btn btn-block btn-primary" type="submit">Continue</button>
                <button className="btn btn-block btn-default" onClick={this.props.cancel}>Cancel</button>
            </form>
        )
    }
});