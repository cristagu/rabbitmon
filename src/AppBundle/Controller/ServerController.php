<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Queue;
use AppBundle\Entity\Server;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Server controller.
 *
 * @Route("server")
 */
class ServerController extends Controller
{
    /**
     * Lists all server entities.
     *
     * @Route("/", name="server_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $servers = $em->getRepository('AppBundle:Server')->findAll();

        return $this->render('server/index.html.twig', array(
            'servers' => $servers,
        ));
    }

    /**
     * Creates a new server entity.
     *
     * @Route("/new", name="server_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $server = new Server();
        $form = $this->createForm('AppBundle\Form\ServerType', $server);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($server);
            $em->flush();

            return $this->redirectToRoute('server_show', array('name' => $server->getName()));
        }

        return $this->render('server/new.html.twig', array(
            'server' => $server,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a server entity.
     *
     * @Route("/{name}", name="server_show")
     * @Method("GET")
     */
    public function showAction(Server $server)
    {
        $deleteForm = $this->createDeleteForm($server);

        return $this->render('server/show.html.twig', array(
            'server' => $server,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing server entity.
     *
     * @Route("/{name}/edit", name="server_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Server $server)
    {
        $deleteForm = $this->createDeleteForm($server);
        $editForm = $this->createForm('AppBundle\Form\ServerType', $server);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('server_edit', array('name' => $server->getName()));
        }

        return $this->render('server/edit.html.twig', array(
            'server' => $server,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing server entity.
     *
     * @Route("/{name}/new-queue", name="new_queue")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param Server $server
     *
     * @return Response
     */
    public function newQueueAction(Request $request, Server $server)
    {
        $queue = new Queue();
        $form = $this->createForm('AppBundle\Form\QueueType', $queue, [
            'server' => $server
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $queue->setServer($server);
            $em->persist($queue);
            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('queue/new.html.twig', array(
            'queue' => $queue,
            'form' => $form->createView(),
        ));
    }

    /**
     * Deletes a server entity.
     *
     * @Route("/{name}", name="server_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Server $server)
    {
        $form = $this->createDeleteForm($server);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($server);
            $em->flush();
        }

        return $this->redirectToRoute('server_index');
    }

    /**
     * Creates a form to delete a server entity.
     *
     * @param Server $server The server entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Server $server)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('server_delete', array('name' => $server->getName())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * @Route("/{name}/vhost-names", name="vhost_names")
     *
     * @param Request $request
     * @param Server $server
     * @param string $vhost
     *
     * @return JsonResponse
     */
    public function getVhostNamesAction(Request $request, Server $server)
    {
        return new JsonResponse(
            [
                'hey' => 'yo'
            ]
        );
    }

    /**
     * @Route("/{name}/{vhost}/queue-names", name="queue_names")
     *
     * @param Request $request
     * @param Server $server
     * @param string $vhost
     *
     * @return JsonResponse
     */
    public function getQueueNamesAction(Request $request, Server $server, string $vhost)
    {
        $query = $request->get('q');

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

        if ($vhost == '_') {
            $vhost = '%2F';
        }

        $resp = $client->get($vhost, $options);
        $data = json_decode($resp->getBody()->getContents(), true);

        $names = [];

        foreach ($data as $queueData) {
            if (!empty($query)) {
                if (strstr($queueData['name'], $query)) {
                    $names[] = $queueData['name'];
                }
            } else {
                $names[] = $queueData['name'];
            }
        }

        return new JsonResponse($names);
    }
}
