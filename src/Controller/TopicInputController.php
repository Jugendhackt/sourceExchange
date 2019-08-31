<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
//Formulare einbinden
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\DateTime;

use App\Entity\Topic;
use App\Entity\TopicUser;
use Doctrine\ORM\EntityManagerInterface;

class TopicInputController extends AbstractController
{

    public function __construct(EntityManagerInterface $emi)
    {
        $this->emi = $emi;
    }

    /**
     * @Route("/topic_input", name="topic_input")
     */
    public function inputTopic(Request $req)
    {
        $form = $this->createFormBuilder()
            ->add('topic', TextType::class)
            ->add('description', TextType::class)
            ->getForm();

        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid())
        {
            $data=$form->getData();

            $entityManager = $this->getDoctrine()->getManager();

            $user = $this->getUser();

            $newTopic = new Topic();
            $newTopic->setTitle($data['topic']);
            $newTopic->setDescription($data['description']);
            $newTopic->setUser($user);
            $newTopic->setTimestamp(new \DateTime);


            $entityManager->persist($newTopic);
            $entityManager->flush();

            return $this->redirectToRoute('topic_input');

        }

        return $this->render('topic_input/index.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'TopicInputController',
        ]);
    }

    /*public function index()
    {
        return $this->render('topic_input/index.html.twig', [
            'controller_name' => 'TopicInputController',
        ]);
    }*/
}
