<?php

namespace App\Http\Controllers\MSOffice;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\OauthTrait;
use App\TokenStore\TokenCache;

class AuthController extends Controller
{
    use OauthTrait;

    public function signinf()
    {
        //dd(config('app.oauth_app_id'));
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Initialize the OAuth client
        // $oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
        //     'clientId'                => env('OAUTH_APP_ID'),
        //     'clientSecret'            => env('OAUTH_APP_PASSWORD'),
        //     'redirectUri'             => env('OAUTH_REDIRECT_URI'),
        //     'urlAuthorize'            => env('OAUTH_AUTHORITY').env('OAUTH_AUTHORIZE_ENDPOINT'),
        //     'urlAccessToken'          => env('OAUTH_AUTHORITY').env('OAUTH_TOKEN_ENDPOINT'),
        //     'urlResourceOwnerDetails' => '',
        //     'scopes'                  => env('OAUTH_SCOPES')
        // ]);

        $xyz = new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId'                => config('app.oauth_app_id'),
            'clientSecret'            => config('app.oauth_app_password'),
            'redirectUri'             => config('app.oauth_redirect_uri'),
            'urlAuthorize'            => config('app.oauth_authority').config('app.oauth_authorize_endpoint'),
            'urlAccessToken'          => config('app.oauth_authority').config('app.oauth_token_endpoint'),
            'urlResourceOwnerDetails' => '',
            'scopes'                  => config('app.oauth_scopes'),
        ]);

        $oauthClient = $xyz;

        //dd($oauthClient);

        // Output the authorization endpoint
        echo 'Auth URL: '.$oauthClient->getAuthorizationUrl();
        exit();
    }



    public function signin()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Initialize the OAuth client
        $oauthClient = $this->get_custom_client();

        // Generate the auth URL
        $authorizationUrl = $oauthClient->getAuthorizationUrl();

        // Save client state so we can validate in response
        $_SESSION['oauth_state'] = $oauthClient->getState();

        // Redirect to authorization endpoint
        header('Location: '.$authorizationUrl);
        exit();
    }



    public function gettoken()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Authorization code should be in the "code" query param
        if (isset($_GET['code'])) {
            // Check that state matches
            if (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth_state'])) {
                exit('State provided in redirect does not match expected value.');
            }

            // Clear saved state
            unset($_SESSION['oauth_state']);

            // Initialize the OAuth client
            $oauthClient = $this->get_custom_client();

            try {
                { // second get token data
                    // Make the token request
                    // $accessToken = $oauthClient->getAccessToken('authorization_code', [
                    //     'code' => $_GET['code']
                    // ]);

                    // echo 'Access token: '.$accessToken->getToken();
                }

                // Make the token request
                $accessToken = $oauthClient->getAccessToken('authorization_code', [
                    'code' => $_GET['code']
                ]);

                // Save the access token and refresh tokens in session
                // This is for demo purposes only. A better method would
                // be to store the refresh token in a secured database
                // $tokenCache = new \App\TokenStore\TokenCache;
                $tokenCache = new TokenCache;
                $tokenCache->storeTokens($accessToken->getToken(), $accessToken->getRefreshToken(), $accessToken->getExpires());

                // Redirect back to mail page
                return redirect()->route('mail');

            }
            catch (League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
                exit('ERROR getting tokens: '.$e->getMessage());
            }
            exit();
        } elseif (isset($_GET['error'])) {
            exit('ERROR: '.$_GET['error'].' - '.$_GET['error_description']);
        }
    }
}
