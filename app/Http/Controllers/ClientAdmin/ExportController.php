<?php

namespace App\Http\Controllers\ClientAdmin;

use App\Http\Controllers\ClientAdmin\Controller;
use Illuminate\Http\Request;
use App\User;

class ExportController extends Controller
{
    
    public function users(Request $request) {
        $callback = function() {
            $fh = fopen('php://output', 'w');
            $columns = [
                'ID', 
                'Created at', 
                'First name', 
                'Last name',
                'Primary email',
                'Secondary email',
                'Role',
                'Subscription',
                'Last activity',
                'Admin',
                'Banned',
            ];
            fputcsv($fh, $columns);

            User::with('emails', 'clients')
                ->whereHas('clients', function($query) {
                    $query->where('client_id', $this->context->client()->id);
                })->chunk(100, function($users) use ($fh) {
                    foreach($users as $user) {
                        $row = [
                            $user->id,
                            $user->created_at,
                            $user->first_name,
                            $user->last_name,
                            $user->primary_email,
                            $user->secondary_email,
                            $user->role,
                            $user->subscription_news ? 'yes' : 'no',
                            $user->clients[0]->pivot->last_activity,
                            $user->clients[0]->pivot->admin ? 'yes' : 'no',
                            $user->clients[0]->pivot->banned ? 'yes' : 'no'
                        ];
                        fputcsv($fh, $row);
                    }
                });
            fclose($fh);
        };
        $file = preg_replace('/[^a-z0-9]+/', '_', strtolower($this->context->client()->name)).'_'.date('Ymd_His').'.csv';
        return $this->outputFile($file, $callback);
    }




    private function outputFile($file_name, $callback) {
        $headers = array(
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename='.$file_name,
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        );
        return response()->stream($callback, 200, $headers);
    }

}