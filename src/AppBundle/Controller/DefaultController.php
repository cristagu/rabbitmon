<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Queue;
use AppBundle\Entity\Server;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use GuzzleHttp\Client;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        /** @var EntityRepository $repo */
        $repo = $this->getDoctrine()->getManager()->getRepository(Server::class);

        /** @var ArrayCollection $servers */
        $servers = $repo->findAll();

        /** @var Server $server */
        foreach ($servers as $server) {
            $uri = 'http://' . $server->getHost() . ':' . $server->getPort() . '/api/queues/';
            $client = new Client(
                [
                    'base_uri' => $uri
                ]
            );
            $options = [
                'auth' => [
                    $server->getUser(),
                    $server->getPass()
                ]
            ];
            /** @var Queue $queue */
            foreach ($server->getQueues() as $queue) {
                $vhost = $queue->getVhost();
                if ($vhost == '_') {
                    $vhost = '%2F';
                }
                $resp = $client->get($vhost . '/' . $queue->getName(), $options);
                $data = json_decode($resp->getBody()->getContents());

                $queue->setMessages($data->messages);
            }
        }

        // replace this example code with whatever you need
        return $this->render(
            'AppBundle::index.html.twig',
            [
                'servers' => $servers
            ]
        );
    }
}
