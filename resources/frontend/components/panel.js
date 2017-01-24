import React from 'react'

export default React.createClass({

    render: function() {
        return (
            <div className="panel panel-default">
                {this.props.title && <div className="panel-heading">{this.props.title}</div>}
                <div className="panel-body">
                    {this.props.children}
                </div>
            </div>
        )
    }
});