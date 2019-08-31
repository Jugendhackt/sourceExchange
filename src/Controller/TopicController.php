<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TopicRepository;
use App\Entity\Topic;

class TopicController extends AbstractController
{

    public function __construct(TopicRepository $topics)
    {
      $this->topic = $topics;
    }

    /**
     * @Route("/topic/{topic}", name="topic")
     */
    public function index(Topic $topic)
    {
        dump($topic);
        return $this->render('topic/index.html.twig', [
            'controller_name' => 'TopicController',
            'topic' => $topic
        ]);
    }
}
