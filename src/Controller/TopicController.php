<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Topic;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use App\Entity\Link;
use App\Entity\TopicUser;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType as SymfonyTextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class TopicController extends AbstractController
{
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

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
     * @Route("/topic/new", name="topic_new")
     */
    public function new(Request $request)
    {
        $form = $this->createFormBuilder()
        ->add('topic', TextType::class, [
            'label' => 'Titel',
        ])
        ->add('description', TextType::class, [
            'label' => 'Beschreibung',
        ])
        ->add('submit', SubmitType::class, [
            'label' => 'Thema erstellen',
            'attr' => [
                'class' => 'btn-success btn-block'
                ]
            ])
        ->getForm();

        $form->handleRequest($request);

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

            $topicUser = new TopicUser();
            $topicUser->setUser($this->getUser());
            $topicUser->setTopicId($newTopic);
            $entityManager->persist($topicUser);
            $entityManager->flush();
            dump($topicUser);

            return $this->redirectToRoute(
                'topic_show', [
                    'id' => $newTopic->getId()
                ]
            );
        }

        return $this->render('topic/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/topic/{id}", name="topic_show")
     */
    public function show($id, Request $request)
    {
        $topic = $this->getDoctrine()
            ->getRepository(Topic::class)
            ->find($id);

        if (!$topic) {
            throw $this->createNotFoundException(
                'No topic found for id '.$id
            );
        }

        $topicUsername = $this->getDoctrine()
            ->getRepository(TopicUser::Class)
            ->findOneBy([
                'user' => $topic->getUser()->getId(),
                'topic_id' => $topic->getId()]
            );

        $link = new Link();
        $link->setTopic($topic);


        $preAliasList = array("Super","Ultra","xXpro","CatLover","Destroyer","Paul","Ben","Dracula",
                                "Infinity","Valkon","Snow");
        $postAliasList = array("LP","GamerXx","99","Alpaka","42","Racer","Dangerous","Snicker",
                                "Forest","Ninja","Dragon");

        $userAlias = new TopicUser();
        $userAlias->setTopicId($topic);

        $linkForm = $this->createFormBuilder($link)
            ->add('href', UrlType::class, [
                'label' => 'Link hinzufügen',
                'attr' => [
                    'placeholder' => 'Link eingeben',
                ],
            ])
            ->add('description', SymfonyTextType::class, [
                'label' => 'Anmerkung',
                'attr' => [
                    'placeholder' => 'Anmerkung eingeben',
                ],
            ])
            ->add('Tags', ChoiceType::class, [
                'choices'  => [
                    'Interessant 🤔' => null,
                    'Informativ 🧐' => true,
                    'Lustig 🤣' => false,
                    'Kontrovers 😬' => false,
                ],
                'expanded' => true,
                'multiple' => true,
                'mapped' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Link hinzufügen',
                'attr' => [
                    'class' => 'btn-success btn-block',
                ],
            ])
            ->getForm();

        $linkForm->handleRequest($request);

        if ($linkForm->isSubmitted() && $linkForm->isValid() && $this->isGranted('ROLE_USER'))
        {
            $link = $linkForm->getNormData();
            $link->setUser($this->getUser());
            $this->em->persist($link);
            $this->em->flush();
        }

        $linkForm = $linkForm->createView();

        if ($topicUsername == null)
        {
            $pre = mt_rand(0,10);
            $post = mt_rand(0,10);

            $topicUsername = $preAliasList[$pre] . ' ' . $postAliasList[$post];
            $userAlias->setUser($topic->getUser());
            $userAlias->setUsername($topicUsername);
            $this->em->persist($userAlias);
            $this->em->flush();
        }

        // or render a template
        // in the template, print things with {{ product.name }}
        return $this->render('default/single-view.html.twig', [
            'topic' => $topic,
            'linkForm' => $linkForm,
            'TopicUser' => $topicUsername,
        ]);
    }
}
