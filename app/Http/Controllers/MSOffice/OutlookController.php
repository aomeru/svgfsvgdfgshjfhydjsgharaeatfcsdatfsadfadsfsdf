<?php

namespace App\Http\Controllers\MSOffice;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\TokenStore\TokenCache;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;

class OutlookController extends Controller
{
    public function mail()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $tokenCache = new TokenCache;
        $graph = new Graph();
        $graph->setAccessToken($tokenCache->getAccessToken());

        $user = $graph->createRequest('GET', '/me')->setReturnType(Model\User::class)->execute();

        dd($user);

        echo 'User: '.$user->getDisplayName();
    }
}
