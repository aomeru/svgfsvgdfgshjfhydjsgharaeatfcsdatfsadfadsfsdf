<?php

namespace App\Http\Controllers\App;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Traits\OauthTrait;
use App\TokenStore\TokenCache;
use Session;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use App\User;
use GuzzleHttp\Exception\RequestException;

class LoginController extends Controller
{
    use OauthTrait;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login()
    {
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

        $user = $this->is_user($res->getUserPrincipalName(),$res->getGivenName(),$res->getSurname()); // check if record exists or create

        session([ // set needed values
            'userinfo' => [
                'display_name' => $res->getDisplayName(),
                'job_title' => $res->getJobTitle(),
                'dept' => $res->getDepartment(),
                'city' => $res->getCity(),
                'state' => $res->getState(),
                'location' => $res->getCity().', '.$res->getState(),
                'photo' => $this->get_image($tokenCache->getAccessToken())
            ]
        ]);

        Auth::login($user); // login user
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

    public function is_user($e,$f,$l)
    {
        $user = User::where('email',$e)->first();
        if($user == null)
        {
            $new = new User;
            $new->firstname = $f;
            $new->lastname = $l;
            $new->email = $e;
            $new->save();
            return $new;
        }
        return $user;
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
        Auth::logout();
        $r->session()->flush();
        return $this->kill_process($r, 'success', 'Logout Successful');
    }
}
