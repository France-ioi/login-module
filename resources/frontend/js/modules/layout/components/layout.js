import React from 'react'

export default React.createClass({

    render: function() {
        return (
            <div>
                <div className="container">
                    {this.props.children}
                </div>
            </div>
        )
    }
});