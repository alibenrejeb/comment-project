<?php

namespace App\Controller;

use App\Encoder\JWTEncoder;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use KnpU\OAuth2ClientBundle\Client\Provider\FacebookClient;
use League\OAuth2\Client\Provider\FacebookUser;

class FacebookController extends AbstractController
{
    #[Route('/connect/facebook', name: 'connect_facebook_start')]
    public function connect(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            ->getClient('facebook_main') // key used in config/packages/knpu_oauth2_client.yaml
            ->redirect([
                'public_profile',
                'email' // the scopes you want to access
            ]);
    }

    #[Route('/connect/facebook/check', name: 'connect_facebook_check')]
    public function connectCheck(Request $request, ClientRegistry $clientRegistry)
    {
        /** @var FacebookClient $client */
        $client = $clientRegistry->getClient('facebook_main');

        try {
            // the exact class depends on which provider you're using
            /** @var FacebookUser $user */
            $user = $client->fetchUser();

            // do something with all this new power!
            // e.g. $name = $user->getFirstName();
            var_dump($user);
            die;
            // ...
        } catch (IdentityProviderException $e) {
            // something went wrong!
            // probably you should return the reason to the user
            var_dump($e->getMessage());
            die;
        }
    }
}