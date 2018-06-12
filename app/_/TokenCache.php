<?php

use Illuminate\Http\Request;
namespace App\TokenStore;
use App\Traits\OauthTrait;
use App\User;
use App\Models\UserToken;
use Auth;
use Session;

class TokenCache {

    use OauthTrait;

    protected function getObject()
    {
        return UserToken::where('user_id',Auth()->id)->first();
    }

    public function storeTokens($access_token, $refresh_token, $expires)
    {
        // $_SESSION['access_token'] = $access_token;
        // $_SESSION['refresh_token'] = $refresh_token;
        // $_SESSION['token_expires'] = $expires;
        if(Auth::check())
        {
            $tokenData = $this->getObject();
            $tokenData->access_token = $access_token;
            $tokenData->refresh_token = $refresh_token;
            $tokenData->token_expires = $expires;
            $tokenData->update();
        } else {
            session([
                'access_token' => $access_token,
                'refresh_token' => $refresh_token,
                'token_expires' => $expires
            ]);
        }
    }

    public function clearTokens($r)
    {
        // unset($_SESSION['access_token']);
        // unset($_SESSION['refresh_token']);
        // unset($_SESSION['token_expires']);
        if(Auth::check())
        {
            $tokenData = UserToken::where('user_id',Auth()->id)->first();
            $tokenData->access_token= null;
            $tokenData->refresh_token = null;
            $tokenData->token_expires = null;
            $tokenData->update();
            if($r->session()->has('oauth_state')) $r->session()->forget('oauth_state');
        } else {
            if($r->session()->has('access_token'))
            {
                $r->session()->forget('access_token');
                $r->session()->forget('refresh_token');
                $r->session()->forget('token_expires');
                if($r->session()->has('oauth_state')) $r->session()->forget('oauth_state');
            }
        }
    }

    public function getAccessToken()
    {
        if(Auth::check())
        {
            $tokenData = $this->getObject();
            $at = $tokenData->access_token;
            $rt = $tokenData->refresh_token;
            $te = $tokenData->token_expires;
        } else {
            $at = session('access_token');
            $rt = session('refresh_token');
            $te = session('token_expires');
        }
        if (empty($at) || empty($rt) || empty($te)) return '';

        $now = time() + 300;
        if ($te <= $now) {
            $oauthClient = $this->get_custom_client();

            try {
                $newToken = $oauthClient->getAccessToken('refresh_token', [
                    'refresh_token' => $rt
                ]);
                $this->storeTokens($newToken->getToken(), $newToken->getRefreshToken(), $newToken->getExpires());
                return $newToken->getToken();
            } catch (League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
                return '';
            }
        } else return $at;
    }

    public function getAccessTokenn()
    {
        // Check if tokens exist
        if (empty($_SESSION['access_token']) || empty($_SESSION['refresh_token']) || empty($_SESSION['token_expires'])) {
            return '';
        }

        // Check if token is expired
        //Get current time + 5 minutes (to allow for time differences)
        $now = time() + 300;
        if ($_SESSION['token_expires'] <= $now) {
            // Token is expired (or very close to it)
            // so let's refresh

            // Initialize the OAuth client
            $oauthClient = $this->get_custom_client();

            try {
                $newToken = $oauthClient->getAccessToken('refresh_token', [
                    'refresh_token' => $_SESSION['refresh_token']
                ]);

                // Store the new values
                $this->   storeTokens($newToken->getToken(), $newToken->getRefreshToken(), $newToken->getExpires());

                return $newToken->getToken();
            } catch (League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
                return '';
            }
        } else {
            // Token is still valid, just return it
            return $_SESSION['access_token'];
        }
    }
}
