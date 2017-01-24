import React from 'react'
import { Route } from 'react-router'

import NotFound from './components/not_found'
import ServerError from './components/server_error'

export default (
    <Route>
        <Route path="/server-error" component={ServerError}/>
        <Route path="*" component={NotFound} />
    </Route>
)