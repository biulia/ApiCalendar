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
            {
                "name":"Birthday",
                "description" : "Iulia's Birthday",
                "date":"2017-10-30 00:00:00",
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
            $eventPost = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $event =  new Events();
            $event->setStartTime(new \DateTime($request->request->get('start_time')));
            $em->persist($eventPost);
            $em->flush();
            return $this->apiResponse('Inserted',Response::HTTP_CREATED,"AppBundle:Events:addEvent.html.twig");
        }
        return $this->apiResponse();
    }

    public function selectAction ()
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
                        'SELECT e
                FROM AppBundle:Events e
                WHERE 1=1
                ORDER BY e.startTime ASC');
        $products = $query->getResult();;
        /**
         * I would have use this one (but from some reason i have to make some work around to actualy work, anyway i will look into it later)
         *  $eventsResult = $this->getDoctrine()->getRepository('AppBundle:Events')->findBy(array('startTime'=>'ASC'))
         */
        if (!empty($products)) {
            return $this->apiResponse($products,Response::HTTP_OK,"AppBundle:Events:addEvent.html.twig");
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