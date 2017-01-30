import React from 'react'

export default React.createClass({

    render: function() {
        return this.props.is_fetching ? <div className="loading">Please wait...</div> : this.props.children
    }

})