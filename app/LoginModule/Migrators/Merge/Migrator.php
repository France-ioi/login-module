<?php

namespace App\LoginModule\Migrators\Merge;

use App\OriginInstance;
use App\User;
use App\Email;
use App\ObsoletePassword;
use App\Badge;
use App\AuthConnection;
use App\OfficialDomain;
use App\Client;


class Migrator
{

    const CHUNK_SIZE = 100;

    protected $command;
    protected $connection;
    protected $original_instance_id;


    public function __construct($command, $connection) {
        $this->command = $command;
        $this->connection = $connection;
        $this->origin_instance = OriginInstance::findOrFail($command->argument('id'));
    }


    public function run() {
        $oauth_clients_map = $this->migrateOAuthClients();
        $this->command->info('OAuthClients merged');
        $this->migrateUsers($oauth_clients_map);
        $this->command->info('Users merged');
        $this->migrateOfficialDomains();
        $this->command->info('OfficialDomains merged');
    }


    private function migrateOAuthClients() {
        $existing_clients = Client::get()->pluck('id', 'redirect');
        $new_clients = Data::queryOAuthClients($this->connection);
        $map = [];
        foreach($new_clients as $new_client) {
            if(isset($existing_clients[$new_client->redirect])) {
                $map[$new_client->id] = $existing_clients[$new_client->redirect];
            } else {
                $client = Client::create(collect($new_client->getAttributes())->except('id')->toArray());
                $map[$new_client->id] = $client->id;
            }
        }
        return $map;
    }


    private function migrateUsers($oauth_clients_map) {
        $offset = 0;
        while(count($external_users = Data::queryUsers($this->connection, $offset, self::CHUNK_SIZE))) {
            foreach($external_users as $external_user) {
                \DB::transaction(function() use ($external_user, $oauth_clients_map) {
                    if($new_user = $this->saveUser($external_user)) {
                        $this->saveEmails($new_user, $external_user->emails);
                        $this->savePasswords($new_user, $external_user->obsolete_passwords);
                        $this->saveBadges($new_user, $external_user->badges);
                        $this->saveAuthConnections($new_user, $external_user->auth_connections);
                        $this->saveOAuth($new_user, $external_user, $oauth_clients_map);
                    }
                });
            }
            $offset += self::CHUNK_SIZE;
        }
    }


    private function findExistingUsers($external_user) {
        $q = false;
        if(!is_null($external_user->login)) {
            $q = User::where('login', $external_user->login);
        } else {
            $emails = $external_user->emails->pluck('email');
            if(count($emails)) {
                $q = User::whereHas('emails', function($sq) use ($emails) {
                    $sq->whereIn('email', $emails);
                });
            }
        }
        return $q ? $q->with('emails')->get() : null;
    }


    private function saveUser($external_user) {
        //dd($external_user->getAttributes());
        $new_user = new User(collect($external_user->getAttributes())->except('id')->toArray());
        $new_user->origin_instance_id = $this->origin_instance->id;
        if($existing_users = $this->findExistingUsers($external_user)) {

            $exist = $existing_users->search(function($user) {
                return $user->origin_instance_id === $this->origin_instance->id;
            });
            if($exist !== false) {
                return false;
            }

            $primary_user = $existing_users->filter(function($user) {
                return is_null($user->origin_instance_id);
            })->shift();

            if($primary_user) {
                $new_user->primary_user_id = $primary_user->id;
            }
        }
        $new_user->save();
        return $new_user;
    }



    private function saveEmails($new_user, $new_emails) {
        foreach($new_emails as $new_email) {
            $new_email = new Email(collect($new_email->getAttributes())->except('id')->toArray());
            $new_email->origin_instance_id = $new_user->origin_instance_id;
            $new_user->emails()->save($new_email);
        }
    }


    private function savePasswords($new_user, $passwords) {
        foreach($passwords as $pwd) {
            $new_pwd = new ObsoletePassword(collect($pwd->getAttributes())->except('id')->toArray());
            $new_user->obsolete_passwords()->save($new_pwd);
        }
    }


    private function saveBadges($new_user, $badges) {
        foreach($badges as $badge) {
            $new_badge = new Badge(collect($badge->getAttributes())->except('id')->toArray());
            $new_user->badges()->save($new_badge);
        }
    }


    private function saveAuthConnections($new_user, $auth_connections) {
        foreach($auth_connections as $auth_connection) {
            $new_connection = new AuthConnection(collect($auth_connection->getAttributes())->except('id')->toArray());
            $new_user->auth_connections()->save($new_connection);
        }
    }



    private function saveOAuth($new_user, $external_user, $clients_map) {
        foreach(['oauth_access_tokens', 'oauth_auth_codes'] as $table) {
            $rows = $this->connection->table($table)
                ->select('*')
                ->where('user_id', $external_user->id)
                ->get()
                ->map(function($item) use ($new_user, $external_user, $clients_map) {
                    $item->client_id = $clients_map[$item->client_id];
                    $item->user_id = $new_user->id;
                    return (array) $item;
                })
                ->toArray();
            \DB::table($table)->insert($rows);
            unset($rows);
        }
        //TODO: does  oauth_refresh_tokens transfer required?
        //TODO: to increase perfomance create index on oauth_auth_codes.user_id field
    }



    private function migrateOfficialDomains() {
        $offset = 0;
        while(count($official_domains = Data::queryOfficialDomains($this->connection, $offset, self::CHUNK_SIZE))) {
            foreach($official_domains as $official_domain) {
                $existing_domain = OfficialDomain::where('country_code', $official_domain->country_code)
                    ->where('domain', $official_domain->domain)
                    ->first();
                if(!$existing_domain) {
                    OfficialDomain::create(collect($official_domain->getAttributes())->except('id')->toArray());
                }
            }
            $offset += self::CHUNK_SIZE;
        }
    }


}