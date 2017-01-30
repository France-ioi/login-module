<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\OAuthClient;
use App\OAuthClientEndpoint;

class ClientCommand extends Command
{

    protected $signature = 'lm:client {name} {id?} {secret?} {redirect_uri?}';
    protected $description = 'Run the command to create new client';

    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'Client name required'],
            ['id', InputArgument::OPTIONAL, 'Client ID'],
            ['secret', InputArgument::OPTIONAL, 'Client secret'],
            ['redirect_uri', InputArgument::OPTIONAL, 'Redirect URI'],
        ];
    }


    public function handle()
    {
        $client = OAuthClient::create($this->getClientParams());
        $this->info('Client created successfully.');
        $this->line('<comment>Client name:</comment> '.$client->name);
        $this->line('<comment>Client ID:</comment> '.$client->id);
        $this->line('<comment>Client secret:</comment> '.$client->secret);
        if($redirect_uri = $this->argument('redirect_uri')) {
            $client->oauth_client_endpoint()->save(
                new OAuthClientEndpoint(['redirect_uri' => $redirect_uri])
            );
            $this->line('<comment>Client endpoint:</comment> '.$redirect_uri);
        }
    }


    private function getClientParams() {
        $p = [
            'name' => $this->argument('name'),
            'id' => $this->argument('id'),
            'secret' => $this->argument('secret')
        ];
        if($p['id'] === null) {
            $p['id'] = $this->getRandomID();
        } else if(OAuthClient::find($p['id'])) {
            $this->error('Client id already used');
            exit(0);
        }
        if($p['secret'] === null) {
            $p['secret'] = str_random(40);
        }
        return $p;
    }


    private function getRandomID() {
        do {
            $id = str_random(40);
        } while(OAuthClient::find($id));
        return $id;
    }
}
