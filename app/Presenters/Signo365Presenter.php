<?php

declare(strict_types=1);

namespace App\Presenters;

use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use Microsoft\Graph\Exception\GraphException;
use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Nette;


final class Signo365Presenter extends Nette\Application\UI\Presenter
{

  /**
   * Redirect to AAD signin page
   * @throws Nette\Application\AbortException
   */
  public function actionIn()
  {
    $section = $this->getSession('o365auth');

    // Initialize the OAuth client
    $oauthClient = new GenericProvider([
      'clientId'                => OAUTH_APP_ID,
      'clientSecret'            => OAUTH_APP_PASSWORD,
      'redirectUri'             => OAUTH_REDIRECT_URI,
      'urlAuthorize'            => OAUTH_AUTHORITY.OAUTH_AUTHORIZE_ENDPOINT,
      'urlAccessToken'          => OAUTH_AUTHORITY.OAUTH_TOKEN_ENDPOINT,
      'urlResourceOwnerDetails' => '',
      'scopes'                  => OAUTH_SCOPES
    ]);

    $authUrl = $oauthClient->getAuthorizationUrl();

    // Save client state so we can validate in callback
    $section->oauthState = $oauthClient->getState();

    // Redirect to AAD signin page
    $this->redirectUrl($authUrl);
  }

  /**
   * Logout the user
   * @throws Nette\Application\AbortException
   */
  public function actionOut(): void
  {
    $this->getUser()->logout();
    $this->flashMessage('Logout was successful.', 'alert-success');
    $this->redirect('Homepage:default');
  }

  /**
   * After AAD login the login redirect here due Redirect URI
   * @throws Nette\Application\AbortException
   * @throws Nette\Security\AuthenticationException
   */
  public function actionCallback(){
    $section = $this->getSession('o365auth');
    $request = $this->getHttpRequest();

    // Validate state
    $expectedState = $section->oauthState;
    unset($section->oauthState);
    $providedState = $request->getQuery('state');

    if (!isset($expectedState)) {
      $this->flashMessage('It looks you tried to login from address which is not registered as Redirect URI','alert-warning');
      $this->redirect('Homepage:Default');
    }

    if (!isset($providedState) || $expectedState != $providedState) {
      $this->flashMessage("Invalid auth state - The provided auth state did not match the expected value","alert-warning");
      $this->redirect('Homepage:Default');
    }

    // Authorization code should be in the "code" query param
    $authCode = $request->getQuery('code');
    if (isset($authCode)) {
      // Initialize the OAuth client
      $oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
        'clientId'                => OAUTH_APP_ID,
        'clientSecret'            => OAUTH_APP_PASSWORD,
        'redirectUri'             => OAUTH_REDIRECT_URI,
        'urlAuthorize'            => OAUTH_AUTHORITY.OAUTH_AUTHORIZE_ENDPOINT,
        'urlAccessToken'          => OAUTH_AUTHORITY.OAUTH_TOKEN_ENDPOINT,
        'urlResourceOwnerDetails' => '',
        'scopes'                  => OAUTH_SCOPES
      ]);

      try {
        // Make the token request
        $accessToken = $oauthClient->getAccessToken('authorization_code', ['code' => $authCode]);

        $graph = new Graph();
        $graph->setAccessToken($accessToken->getToken());

        $user = $graph->createRequest('GET', '/me')->setReturnType(Model\User::class)->execute();

        // TEMPORARY FOR TESTING! PLEASE REMOVE. See who asked and what is the token
			  $this->flashMessage("Access token received - User:" . $user->getDisplayName() . ", Token:" . $accessToken->getToken(),"alert-warning");

        if(!empty($user->getUserprincipalname())) {

          // HERE YOU should somehow check the user. In my cases I check returned DisplayName and compare with DB and read ID and roles.
          $identity = new Nette\Security\SimpleIdentity(
            $user->getUserprincipalname(),
            'none',
            [
						  'firstname' => $user->getGivenName(),
						  'secondname' => $user->getSurname(),
              'email' => $user->getUserprincipalname()
            ]
          );

          $this->user->login($identity);
          $this->redirect('Homepage:Default');
        }else{
          $this->flashMessage("Something went wrong, because server got empty response","alert-alert");
          $this->redirect('Homepage:Default');
        }
      }
      catch (IdentityProviderException $e) {
        $this->flashMessage("Error requesting access token - " . $e->getMessage(),"alert-alert");
        $this->redirect('Homepage:Default');
      } catch (GraphException $e) {
        $this->flashMessage("Error when reading Graph data - " . $e->getMessage(),"alert-warning");
        $this->redirect('Homepage:Default');
      }
    }

    $this->flashMessage("Error: " . $request->getQuery('error') . " - " . $request->getQuery('error_description'),"alert-alert");
    $this->redirect('Homepage:Default');
  }

}
