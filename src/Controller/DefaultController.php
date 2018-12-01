<?php

namespace App\Controller;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use Symfony\Component\HttpFoundation\Response;

class DefaultController
{
    public function index()
    {
        $apiClient = new Client();
        try {
            $response = $apiClient->request('GET', 'http://webserver/api/v1/partners?status=active',[
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-type' => 'application/json'
                ]
            ]);
        } catch(TransferException $ex) {
            return new Response(sprintf('An error occurred when accessing the API: %s', $ex->getMessage(), 400 ));
        }

        $result = json_decode($response->getBody()->getContents());
        foreach($result->data as $partner) {
            echo $partner->name;
        }
    }
}