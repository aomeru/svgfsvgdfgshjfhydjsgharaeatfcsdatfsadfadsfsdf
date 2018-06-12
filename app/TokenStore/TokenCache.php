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

    public function storeTokens($access_token, $refresh_token, $expires)
    {
        session([
            'access_token' => $access_token,
            'refresh_token' => $refresh_token,
            'token_expires' => $expires
        ]);
    }

    public function clearTokens($r)
    {
        if($r->session()->has('access_token'))
        {
            $r->session()->forget('access_token');
            $r->session()->forget('refresh_token');
            $r->session()->forget('token_expires');
        }
        if($r->session()->has('oauth_state')) $r->session()->forget('oauth_state');
    }

    public function getAccessToken()
    {
        $at = session('access_token');
        $rt = session('refresh_token');
        $te = session('token_expires');

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
}
