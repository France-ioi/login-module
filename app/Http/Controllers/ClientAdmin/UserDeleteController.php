<?php
namespace App\Http\Controllers\ClientAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\ClientAdmin\Controller;
use App\User;
use App\Client;
use App\LoginModule\Platform\AdminInterface;
use App\LoginModule\Platform\PlatformUser;

class UserDeleteController extends Controller {


    public function index($client_id, $user_id, Request $request) {
        $user = $this->getUser($user_id);
        $lm_delete_available = $user->clients->search(function($client) {
            return !$client->pivot->deleted && !empty($client->admin_interface_url);
        }) === false;
        return view('client_admin.user_delete.index', [
            'client' => $this->context->client(),
            'user' => $user,
            'user_clients' => $user->clients,
            'lm_delete_available' => $lm_delete_available
        ]);
    }


    public function platformRedirect($client_id, $user_id, Request $request) {
        $target_client = Client::findOrFail($request->get('target_client_id'));
        $admin_interface = new AdminInterface($target_client);
        if($admin_interface->available()) {
            $unlink_user_url = route('client_admin.user_delete_unlink', [
                'client_id' => $client_id,
                'user_id' => $user_id,
                'target_client_id' => $target_client->id
            ]);
            $url = $admin_interface->userDelete($user_id, $unlink_user_url);
            return redirect($url);
        }
        return redirect()->back()->withError('Admin interface not available at '.$target_client->name);
    }    


    public function unlinkUser($client_id, $user_id, Request $request) {
        PlatformUser::delete($request->get('target_client_id'), $user_id);
        $url = route('client_admin.user_delete', [
            'client_id' => $client_id,
            'user_id' => $user_id            
        ]);
        $target_client = Client::findOrFail($request->get('target_client_id'));
        return redirect($url)->with(['status' => 'Account deleted at '.$target_client->name]);
    }


    public function delete($client_id, $user_id, Request $request) {
        $user = $this->getUser($user_id);
        $user->delete();
        return redirect()->route('client_admin.users', $client_id)->with(['status' => 'User deleted']);
    }

}