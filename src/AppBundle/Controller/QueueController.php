<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Queue;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Queue controller.
 *
 * @Route("queue")
 */
class QueueController extends Controller
{
    /**
     * Lists all queue entities.
     *
     * @Route("/", name="queue_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $queues = $em->getRepository('AppBundle:Queue')->findAll();

        return $this->render('queue/index.html.twig', array(
            'queues' => $queues,
        ));
    }

    /**
     * Creates a new queue entity.
     *
     * @Route("/new", name="queue_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $queue = new Queue();
        $form = $this->createForm('AppBundle\Form\QueueType', $queue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($queue);
            $em->flush();

            return $this->redirectToRoute('queue_show', array('id' => $queue->getId()));
        }

        return $this->render('queue/new.html.twig', array(
            'queue' => $queue,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a queue entity.
     *
     * @Route("/{id}", name="queue_show")
     * @Method("GET")
     */
    public function showAction(Queue $queue)
    {
        $deleteForm = $this->createDeleteForm($queue);

        return $this->render('queue/show.html.twig', array(
            'queue' => $queue,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing queue entity.
     *
     * @Route("/{id}/edit", name="queue_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Queue $queue)
    {
        $deleteForm = $this->createDeleteForm($queue);
        $editForm = $this->createForm('AppBundle\Form\QueueType', $queue);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('queue_edit', array('id' => $queue->getId()));
        }

        return $this->render('queue/edit.html.twig', array(
            'queue' => $queue,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a queue entity.
     *
     * @Route("/{id}/delete", name="queue_delete")
     * @Method("GET")
     */
    public function deleteAction(Request $request, Queue $queue)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($queue);
        $em->flush();

        return $this->redirectToRoute('homepage');
    }

    /**
     * Creates a form to delete a queue entity.
     *
     * @param Queue $queue The queue entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Queue $queue)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('queue_delete', array('id' => $queue->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
