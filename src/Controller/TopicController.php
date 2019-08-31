<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Topic;


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

    /**
 * @Route("/topic/{id}", name="topic_show")
 */
public function show($id)
{
    $topic = $this->getDoctrine()
        ->getRepository(Topic::class)
        ->find($id);

    if (!$topic) {
        throw $this->createNotFoundException(
            'No topic found for id '.$id
        );
    }

    

    // or render a template
    // in the template, print things with {{ product.name }}
    return $this->render('default/single-view.html.twig', ['topic' => $topic]);
}
}