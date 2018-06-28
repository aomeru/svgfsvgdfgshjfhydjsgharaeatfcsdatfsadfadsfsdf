<?php

namespace App\Http\Controllers\App;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Traits\OauthTrait;
use App\Traits\CommonTrait;
use App\TokenStore\TokenCache;
use Session;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use App\User;
use App\Models\Department;
use App\Models\Unit;
use GuzzleHttp\Exception\RequestException;

class LoginController extends Controller
{
    use OauthTrait, CommonTrait;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login()
    {
        // Auth::loginUsingId('18031680');
        // Auth::loginUsingId('18031748');
        // return redirect()->route('portal');

        $oauthClient = $this->get_custom_client();
        $authorizationUrl = $oauthClient->getAuthorizationUrl();
        Session::put('oauth_state', $oauthClient->getState());
        return redirect()->away($authorizationUrl);
    }

    public function get_token(Request $r)
    {
        if (isset($_GET['code'])) {
            if (empty($_GET['state']) || ($_GET['state'] !== $r->session()->get('oauth_state'))) {
                return $this->kill_process($r,'error',"There is an issue with your login, please try again. [EP00001:OAUTH STATE]");
            }
            $r->session()->forget('oauth_state');

            $oauthClient = $this->get_custom_client();

            try {
                $accessToken = $oauthClient->getAccessToken('authorization_code', [
                    'code' => $_GET['code']
                ]);

                $tokenCache = new TokenCache;
                $tokenCache->storeTokens($accessToken->getToken(), $accessToken->getRefreshToken(), $accessToken->getExpires());

                return redirect()->route('process_login');

            }
            catch (League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
                return $this->kill_process($r,'error',"There is an issue with your login, please try again. [EP00002:TOKEN ERROR]");
                // exit('ERROR getting tokens: '.$e->getMessage());
            }
        } elseif (isset($_GET['error'])) {
            return $this->kill_process($r,'error',"There is an issue with your login, please try again. [EP00003]");
            // exit('ERROR: '.$_GET['error'].' - '.$_GET['error_description']);
        }
    }

    public function auth_login(Request $r)
    {
        $tokenCache = new TokenCache;
        $graph = new Graph();
        $graph->setApiVersion("beta")->setAccessToken($tokenCache->getAccessToken());
        $res = $graph->createRequest('GET', '/me')->setReturnType(Model\User::class)->execute();
        if(!$this->is_staff($res->getUserPrincipalName())) return $this->kill_process($r,'denied',"You are not a staff of this organization.");

        // $user = $this->is_user($res->getUserPrincipalName(),$res->getGivenName(),$res->getSurname()); // check if record exists or create
        $user = $this->is_user($res, $tokenCache->getAccessToken());

        // session([ // set needed values
        //     'userinfo' => [
        //         'display_name' => $res->getDisplayName(),
        //         'job_title' => $res->getJobTitle(),
        //         'dept' => $res->getDepartment(),
        //         'city' => $res->getCity(),
        //         'state' => $res->getState(),
        //         'location' => $res->getCity().', '.$res->getState(),
        //         'photo' => $this->get_image($tokenCache->getAccessToken())
        //     ]
        // ]);
        if($user->status != 'active') return $this->kill_process($r,'denied',"You are not permitted to use the ERP Portal.");
        Auth::login($user);
        $this->log(Auth::user()->id,'User Logged into the ERP Portal',$r->path(),'auth');
        return redirect()->route('portal');
    }

    public function is_staff($e)
    {
        $val = explode('@', $e);
        if($val[1] != 'salvicpetroleum.com') return false; else return true;
    }

    public function kill_process($r, $t='', $m)
    {
        $tokenCache = new TokenCache;
        $tokenCache->clearTokens($r);
        switch($t) {
            case 'denied':
            $r->session()->flash('access_denied',$m);
            break;

            case 'success':
            $r->session()->flash('success',$m);
            break;

            default:
            $r->session()->flash('error',$m);
            break;
        }

        return redirect()->route('home');
    }

    public function is_user($r, $t)
    {
        $user = User::where('email',$r->getUserPrincipalName())->first();
        if($user == null)
        {
            return $this->create_user($r, $t);
        } else {
            return $this->update_user($user, $r, $t);
        }
    }

    private function create_user($r, $t)
    {
        $new = new User;
        $new->firstname = $r->getGivenName();
        $new->lastname = $r->getSurname();
        $new->email = $r->getUserPrincipalName();
        $new->job_title = $r->getJobTitle();

        if($r->getCity() != null) $new->city = $r->getCity();

        if($r->getState() != null) $new->state = $r->getState();

        $unit_id = $this->get_unit($r->getDepartment());
        if($unit_id) $new->unit_id = $unit_id;

        $photo = $this->get_image($t);
        if($photo) $new->photo = $photo;
        $new->save();
        return $new;
    }

    private function update_user($u, $r, $t)
    {
        $u->firstname = $r->getGivenName();
        $u->lastname = $r->getSurname();
        $u->email = $r->getUserPrincipalName();
        $u->job_title = $r->getJobTitle();

        if($r->getCity() != null) $u->city = $r->getCity();

        if($r->getState() != null) $u->state = $r->getState();

        $unit_id = $this->get_unit($r->getDepartment());
        if($unit_id){
            if($u->unit_id == null)
            {
                $u->unit_id = $unit_id;
            }
        }

        $photo = $this->get_image($t);
        if($photo) $u->photo = $photo;
        $u->update();
        return $u;
    }

    private function get_unit($d)
    {
        if($d != null)
        {
            $dept = Department::where('title',$d)->first();
            if($dept == null)
            {
                $dept = new Department;
                $dept->title = $d;
                $dept->save();
            }
            $unit = Unit::where('title',$dept->title)->first();
            if($unit == null)
            {
                $unit = new Unit;
                $unit->title = $dept->title;
                $unit->department_id = $dept->id;
                $unit->save();
            }
            return $unit->id;
        }
        return false;
    }

    public function get_image($token)
    {
        $hasPhoto = true;
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'https://graph.microsoft.com/v1.0/'
        ]);

        try {

            $res = $client->request('GET', 'me/photo/$value', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer '.$token
                ]
            ]);
        }
        catch (RequestException $e) {
            // $res = json_decode($e->getResponse()->getBody(true));
            // if($res->error->code == )
            $hasPhoto = false;
        }

        return $hasPhoto ? base64_encode((string) $res->getBody()) : $hasPhoto;
    }

    public function logout(Request $r)
    {
        if(Auth::check()) $this->log(Auth::user()->id,'User Logged out of the ERP Portal',$r->path(),'auth');
        Auth::logout();
        $r->session()->flush();
        return $this->kill_process($r, 'success', 'Logout Successful');
    }
}
