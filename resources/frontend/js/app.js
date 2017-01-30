import 'libs/bootstrap'
import 'libs/token'

import React from 'react'
import ReactDOM from 'react-dom'

import Layout from 'modules/layout'
import Auth from 'modules/auth'
import Authorization from 'modules/authorization'


ReactDOM.render((
    <Layout>
        <Auth>
            <Authorization/>
        </Auth>
    </Layout>
), document.getElementById('app'))