import 'libs/bootstrap'
import 'libs/token'

import React from 'react'
import ReactDOM from 'react-dom'
import { Router, Route } from 'react-router'
import { getHistory } from 'libs/history'

import layout from 'modules/layout'
import auth from 'modules/auth'
import authorization from 'modules/authorization'
import error_pages from 'modules/error_pages'

ReactDOM.render((
    <Router history={getHistory()}>
        <Route path="/" component={layout.components.Layout}>
            <Route component={auth.components.Authenticated}>
                {authorization.routes}
            </Route>
            {error_pages.routes}
        </Route>
    </Router>
), document.getElementById('app'))