import Channel from 'jschannel'


var ChannelClient = function() {
    var chan = Channel.build({
        window: window.opener,
        origin: '*',
        scope: 'ioi_login'
    })

    this.authorize = (auth) => {
        chan.call({
            method: 'authorize',
            params: auth,
            success: ()=> {},
            error: (err) => console.error(err)
        })
    }

    this.deny = (auth) => {
        chan.call({
            method: 'deny',
            params: auth,
            success: ()=> {},
            error: (err) => console.error(err)
        })
    }
}


var RedirectClient = function() {

    this.authorize = (auth) => {
        if(auth.redirect_uri) {
            window.location.href = auth.redirect_uri
        }
    }

    this.deny = (auth) => {
        if(auth.redirect_uri) {
            window.location.href = auth.redirect_uri
        }
    }

}

var client = window.opener ? new ChannelClient() : new RedirectClient()

export default client