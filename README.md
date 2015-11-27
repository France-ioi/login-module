# Common login module

This login module is meant to be shared between a few websites (including declick.org, france-ioi.org).

You can include it in an iframe, and listen to the messages described in [this doc](https://docs.google.com/document/d/1KfekxhPBbZYbtf3ybvrjnN2Naqh79x3ekYL0Q7aStdo/edit?usp=sharing).

## Setup

Clone and:

    bower install
    composer install

Setup a database and import table structure from `user.sql`.

Copy `config_local_template.php` into `config_local.php` and override the configuration.

Include an iframe in your page, pointing to `login.html`, it should send messages to the main page through `postMessage`.

TODO: add a small demo page.
