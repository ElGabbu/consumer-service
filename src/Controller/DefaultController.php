<?php

namespace App\Controller;

use App\Entity\Partner;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AbstractController
{
    public function index(EntityManagerInterface $entityManager)
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
            $existingPartner = $this->getDoctrine()
                ->getRepository(Partner::class)
                ->findOneBy(['name' => $partner->name]);

            if($existingPartner) {
                echo sprintf('%s already listed in the database', $partner->name);
                continue;
            }

            $newPartner = new Partner();
            $newPartner->setName($partner->name);

            $entityManager->persist($newPartner);
            $entityManager->flush();

            echo sprintf('%s added in the database', $partner->name);
        }
    }
}