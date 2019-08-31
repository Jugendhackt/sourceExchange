<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TopicController extends AbstractController
{
    /**
     * @Route("/topic", name="topic")
     */
    public function index()
    {
        return $this->render('index.html.twig', [
            'controller_name' => 'TopicController',
        ]);
    }

    /**
     * @Route("/mytopics", name="mytopics")
     */
    public function mytopics()
    {
        return $this->render('default/mytopics.html.twig');
    }
}