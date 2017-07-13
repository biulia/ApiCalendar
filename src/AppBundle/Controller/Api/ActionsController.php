<?php
/**
 * Created by PhpStorm.
 * User: Iulia
 * Date: 7/11/2017
 * Time: 12:20 PM
 */
namespace AppBundle\Controller\Api;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Response;
//use AppBundle\Entity\User;
class ActionsController  extends FOSRestController  {

    /**
     * @Rest\View
     */
    public function addAction ()
    {

        return new Response('add');
    }
    public function selectAction ()
    {
       return print_r('select');
    }
    public function deleteAction ()
    {
        return print_r('delete');
    }
    public function updateAction ()
    {
        return print_r('update');
    }
}