<?php

namespace AppBundle\Controller\Api;

use AppBundle\Repository\EventsRepository;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use AppBundle\Form\EventsType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
                "location":"Cluj-Napoca"
            }
    */
        try {
            /** @var EventsRepository $eventsRepository */

            $em = $this->getDoctrine()->getManager();
            $eventsRepository = $em->getRepository('AppBundle:Events');

            if (!$eventsRepository->isGranted('34342532243')) {
                return $this->apiResponse('UNAUTHORIZED', Response::HTTP_UNAUTHORIZED, "AppBundle:Events:view.html.twig");
            }
            $form = $this->createForm(EventsType::class, null, [
                'csrf_protection' => false,
            ]);
            $form->submit($request->request->all());
            $form->getData();
            $eventPost = $form->getData();
            $em->persist($eventPost);
            $em->flush();
            $updateStartTime = $eventsRepository->find(intval($eventPost->getId()));
            $updateStartTime->setStartTime($request->get('startTime')?$request->get('startTime') : null );
            $em->persist($updateStartTime);
            $em->flush();
            return $this->apiResponse('inserted', Response::HTTP_CREATED, "AppBundle:Events:view.html.twig");
        } catch (\Exception $exception) {
            return $this->apiResponse('HTTP BAD REQUEST', Response::HTTP_BAD_REQUEST, "AppBundle:Events:view.html.twig");
        }
    }

    public function selectAction ()
    {
        $eventsResult = $this->getDoctrine()->getRepository('AppBundle:Events');
        $events = null;
        try {
            $events = $eventsResult->findBy(array(),array('startTimes'=>'ASC'));
            return $this->apiResponse($events,Response::HTTP_OK,"AppBundle:Events:view.html.twig");
        } catch (\Exception $exception) {
            throw new NotFoundHttpException(Response::HTTP_NOT_FOUND);
        }
    }
    public function selectOneAction ($id) {

        $eventResult = $this->getDoctrine()->getRepository('AppBundle:Events');
        try {
            $event = $eventResult->find($id);
            return $this->apiResponse($event,Response::HTTP_OK,"AppBundle:Events:view.html.twig");
        } catch (\Exception $exception) {
            throw new NotFoundHttpException(sprintf('The event \'%s\' was not found.', $id));
        }
    }
    public function updateHourEventAction ($id, Request $request)
    {
        $event = $this->getDoctrine()->getRepository('AppBundle:Events')->find(array('id'=>$id));
        try {
            $form = $this->createForm(EventsType::class, $event, [
            'csrf_protection' => false,
            ]);
            $form->submit($request->request->all(), false);
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->apiResponse('Updated event hour',Response::HTTP_OK,"AppBundle:Events:view.html.twig");
        } catch (\Exception $exception) {
            throw new NotFoundHttpException(sprintf('The event \'%s\' was not updated.', $id));
        }
    }
    public function deleteAction ($id, Request $request)
    {
        $eventResult = $this->getDoctrine()->getRepository('AppBundle:Events');
        try {
            $event = $eventResult->find($id);
            $em = $this->getDoctrine()->getManager();
            $em->remove($event);
            $em->flush();
            return $this->apiResponse('Deleted',Response::HTTP_OK,"AppBundle:Events:view.html.twig");
        } catch (\Exception $exception) {
            throw new NotFoundHttpException(sprintf('The event \'%s\' was not found and has not been deleted', $id));
        }
    }
    public function updateEventAction ($id, Request $request)
    {
        try{
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
            return $this->apiResponse('Updated',Response::HTTP_OK,"AppBundle:Events:view.html.twig");
        } catch (\Exception $exception) {
            throw new NotFoundHttpException(sprintf('The event \'%s\' was not found and has not been deleted', $id));
        }
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