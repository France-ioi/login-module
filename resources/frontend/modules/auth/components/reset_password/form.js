import React from 'react'

export default React.createClass({

    render: function() {
        return (
            <form onSubmit={this.props.submit}>
                <div className="form-group">
                    <label>Email</label>
                    <input type="email" className="form-control" value={this.props.data.email} onChange={(e)=>this.props.setData({ email: e.target.value})}/>
                    {this.props.errors.email && <span className="error">{this.props.errors.email}</span>}
                </div>
                <button className="btn btn-block btn-primary" type="submit">Continue</button>
            </form>
        )
    }
})