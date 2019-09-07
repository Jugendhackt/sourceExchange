<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Topic;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use App\Entity\Link;
use App\Entity\TopicUser;
use App\Repository\TopicUserRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType as SymfonyTextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class TopicController extends AbstractController
{
    public function __construct(EntityManagerInterface $em, TopicUserRepository $topicUsers)
    {
        $this->em = $em;
        $this->topicUsers = $topicUsers;
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
        $flushNeeded = false;

        $topic = $this->getDoctrine()
            ->getRepository(Topic::class)
            ->find($id);

        if (!$topic) {
            throw $this->createNotFoundException(
                'No topic found for id '.$id
            );
        }

        $link = new Link();
        $link->setTopic($topic);
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

        $topicUser = $this->topicUsers->findOneBy([
            'user' => $topic->getUser()->getId(),
            'topic_id' => $topic->getId()
        ]);

        if ($topicUser === null)
        {
            // Workaround for legacy topics where no username was generated on topic creation

            $topicUser = new TopicUser();
            $topicUser->setTopicId($topic);
            $topicUser->setUser($topic->getUser());
            $this->em->persist($topicUser);
            $this->em->flush();
        }

        $userTopicUsernameMap = [];
        $userTopicUsernameMap[$topic->getUser()->getId()] = $topicUser;

        foreach ($topic->getLinks() as $link)
        {
            $linkUser = $link->getUser();
            if (isset($userTopicUsernameMap[$linkUser->getId()]))
            {
                continue;
            }

            $topicUser = $this->topicUsers->findOneBy([
                'user' => $linkUser->getId(),
                'topic_id' => $topic->getId(),
            ]);

            if ($topicUser instanceof TopicUser)
            {
                $userTopicUsernameMap[$linkUser->getId()] = $topicUser;
                continue;
            }

            $topicUser = new TopicUser();
            $topicUser->setTopicId($topic);
            $topicUser->setUser($linkUser);
            $this->em->persist($topicUser);
            $flushNeeded = true;

            $userTopicUsernameMap[$linkUser->getId()] = $topicUser;
        }

        // Do not flush for every single new TopicUser
        if ($flushNeeded)
        {
            $this->em->flush();
        }

        return $this->render('default/single-view.html.twig', [
            'topic' => $topic,
            'linkForm' => $linkForm,
            'topicUserMap' => $userTopicUsernameMap,
        ]);
    }
}
