<?php
return [
    'header' => 'Right to data access, portability, and right to be forgotten',
    'description' => 'In compliance with the GDPR regulations, this tool makes it easy for you to control what data this authentication module stores about you, as well as the data any platform connected to it stores about you.',

    's1' => [
        'title' => 'a) View / export data stored about me',
        'description' => 'You may choose to view or export all of the data we store about you. This authentication module doesn’t have access to the data stored about you in each platform that you use it for, but can automatically load pages from each platform.',
        'client' => 'Data stored about me by platform',
        'self' => 'Data stored by this authentication module',
        'summary' => 'View summary',
        'export' => 'Export all',
        'not_available' => 'preview not available'
    ],

    's2' => [
        'title' => 'b) Delete data stored about me',
        'description' => 'You may choose to delete some or all of the data this authentication module stores about you, or that any of the connected platform stores about you.',
        'self' => 'Delete all data about me from all platforms connected to this account, and delete my account',
        'client' => 'Delete all data about me from platform',
    ],

    'submit' => 'Delete',

    'confirmation' => [
        'title' => 'Delete all data about user',
        'cb1' => 'I confirm that I request for all the data stored about me on the following platforms to be deleted:',
        'self' => 'Authentication module',
        'client' => 'Platform',
        'cb2' => 'I understand that this operation can’t be cancelled, and that everything I did on this platform will be deleted without any way for me to recover it.',
        'submit' => 'Confirm'
    ]

];