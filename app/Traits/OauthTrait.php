<?php
namespace App\Traits;

trait OauthTrait
{
	public function get_custom_client()
    {
        return $xyz = new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId'                => config('app.oauth_app_id'),
            'clientSecret'            => config('app.oauth_app_password'),
            'redirectUri'             => config('app.oauth_redirect_uri'),
            'urlAuthorize'            => config('app.oauth_authority').config('app.oauth_authorize_endpoint'),
            'urlAccessToken'          => config('app.oauth_authority').config('app.oauth_token_endpoint'),
            'urlResourceOwnerDetails' => '',
            'scopes'                  => config('app.oauth_scopes'),
        ]);
    }
}
