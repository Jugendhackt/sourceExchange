<?php

namespace App\Controller;

use App\Repository\TopicRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    public function __construct(TopicRepository $topics)
    {
        $this->topics = $topics;
    }

    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $topics = $this->topics->findBy([], ['timestamp' => 'ASC']);
        $popularTopics = $this->topics->getTopicsFromLastWeek();
        usort($popularTopics, function ($a, $b)
        {
            return $a->getSourceCount() < $b->getSourceCount();
        });

        return $this->render('default/index.html.twig', [
            'topics' => $topics,
            'popularTopics' => $popularTopics
        ]);
    }

    /**
     * @Route("/single", name="single")
     */
    public function single()
    {
        return $this->render('default/single-view.html.twig');
    }
}
