<?php

declare(strict_types=1);

namespace App\Presenters;

use Microsoft\Graph\Exception\GraphException;
use Nette;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use GuzzleHttp\Client;
use Nette\Utils\Json;


final class GraphPresenter extends Nette\Application\UI\Presenter
{
  private $rsp;

  public function renderUserslist()
  {

    // PHASE nr.1 - Get the Auth Token

    $client = new Client();

    $promise = $client->postAsync(OAUTH_AUTHORITY.OAUTH_TOKEN_ENDPOINT, [
      'form_params' => [
      'grant_type' => 'client_credentials',
      'client_id' => OAUTH_APP_ID,
      'scope' => 'https://graph.microsoft.com/.default',
      'client_secret' => OAUTH_APP_PASSWORD
      ]])->then(function ($response) {
        $this->rsp=Json::decode($response->getBody()->getContents());
      });

    $promise->wait();

    // PHASE nr.2 - Get the Graph data

    if(isset($this->rsp->access_token)){

      $graph = new Graph();
      $graph->setAccessToken($this->rsp->access_token);

      try {
        $users = $graph->createRequest('GET', '/users')->setReturnType(Model\User::class)->execute();
      } catch (GraphException $e) {
        $this->flashMessage('Error in Graph request:' . $e->getMessage());
        $this->redirect('this');
      }

      $this->template->users=$users;

    }else{
      $this->flashMessage('The token is empty. Something went wrong in Phase1');
      $this->redirect('this');
    }


  }

}
