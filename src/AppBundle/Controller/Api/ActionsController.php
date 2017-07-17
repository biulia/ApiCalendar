<?php

namespace AppBundle\Controller\Api;

use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use AppBundle\Form\EventsType;
use AppBundle\Entity\Events;
use Symfony\Component\HttpFoundation\Request;


class ActionsController extends FOSRestController  {

    public function addAction(Request $request)
    {
        /*  Body->postman
                param{date}->2017-07-04 00:00:00
            {
                "name":"Birthday",
                "description" : "Iulia's Birthday",
                "startTime":"2017-10-30 00:00:00",
                "endTime":"60",
                "comment":"Buy cake",
                "location":"Cluj-Napoa"
            }
    */
        $date = $request->get('date');
        if(!empty($date)) {
            $form = $this->createForm(EventsType::class, null, [
                'csrf_protection' => false,
            ]);
            $form->submit($request->request->all());
            $form->getData();
            $eventPost = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($eventPost);
            $em->flush();
            if(!empty($eventPost->getId())){
                $event = $this->getDoctrine()->getManager();
                $updateStartTime = $event->getRepository('AppBundle:Events')->find(intval($eventPost->getId()));
                $updateStartTime->setStartTime('2017-10-30');
                $event->flush();
            }
            return $this->apiResponse('Inserted',Response::HTTP_CREATED,"AppBundle:Events:addEvent.html.twig");
        }
        return $this->apiResponse();
    }

    public function selectAction ()
    {

        $eventsResult = $this->getDoctrine()->getRepository('AppBundle:Events')->findBy(array(),array('startTime'=>'ASC'));
        if (!empty($eventsResult)) {
            return $this->apiResponse($eventsResult,Response::HTTP_OK,"AppBundle:Events:addEvent.html.twig");
        }
        return $this->apiResponse();

    }
    public function selectOneAction ($id)
    {
        $eventResult = $this->getDoctrine()->getRepository('AppBundle:Events')->find($id);
        if (!empty($eventResult)) {
            return $this->apiResponse($eventResult,Response::HTTP_OK,"AppBundle:Events:addEvent.html.twig");
        }
        return $this->apiResponse();
    }
    public function updateHourEventAction ($id, Request $request)
    {
        $eventResult = $this->getDoctrine()->getRepository('AppBundle:Events')->find($id);
        if (empty($eventResult)) {
            return $this->apiResponse();
        }
        $form = $this->createForm(EventsType::class, $eventResult, [
            'csrf_protection' => false,
        ]);
        $form->submit($request->request->all(), false);

        if (!$form->isValid()) {
            return $form;
        }
        $em = $this->getDoctrine()->getManager();
        $em->flush();
        return $this->apiResponse('Updated event hour',Response::HTTP_OK,"AppBundle:Events:addEvent.html.twig");
    }
    public function deleteAction ($id, Request $request)
    {
        $eventResult = $this->getDoctrine()->getRepository('AppBundle:Events')->find($id);
        if (empty($eventResult)) {
            return $this->apiResponse();
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($eventResult);
        $em->flush();
        return $this->apiResponse('Deleted',Response::HTTP_OK,"AppBundle:Events:addEvent.html.twig");
    }
    public function updateEventAction ($id, Request $request)
    {
        $eventResult = $this->getDoctrine()->getRepository('AppBundle:Events')->find($id);
        if (empty($eventResult)) {
            return $this->apiResponse();
        }
        $form = $this->createForm(EventsType::class, $eventResult, [
            'csrf_protection' => false,
        ]);
        $form->submit($request->request->all());
        $em = $this->getDoctrine()->getManager();
        $em->flush();
        return $this->apiResponse('Updated',Response::HTTP_OK,"AppBundle:Events:addEvent.html.twig");
    }

    public function apiResponse($message='', $statusCodes='', $template=''){
        if(empty($statusCodes) || empty($message)){
            $view = $this->view('No result', Response::HTTP_NOT_FOUND);
            return $this->get('fos_rest.view_handler')->handle($view);
        }
        $view = $this->view($message, $statusCodes)
            ->setTemplate($template);
        return $this->get('fos_rest.view_handler')->handle($view);
    }
}