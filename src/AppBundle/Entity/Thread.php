<?php
/**
 * Created by PhpStorm.
 * User: Home
 * Date: 7/15/2017
 * Time: 8:47 AM
 */
// src/MyProject/MyBundle/Entity/Thread.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\CommentBundle\Entity\Thread as BaseThread;

/**
 * @ORM\Entity
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class Thread extends BaseThread
{
    /**
     * @var string $id
     *
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    protected $id;
}