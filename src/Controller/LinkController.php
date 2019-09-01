<?php

namespace App\Controller;

use App\Entity\Link;
use App\Entity\Topic;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class LinkController extends AbstractController
{
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/link/new/{topic}", name="link")
     */
    public function index(Topic $topic, Request $request)
    {
        $form = $this->createFormBuilder(new Link())
            ->add('title', TextType::class, [
                'label' => 'Titel',
                'attr' => [
                    'placeholder' => 'Ein Sack Reis fiel um',
                ]
            ])
            ->add('href', UrlType::class, [
                'label' => 'Link',
                'attr' => [
                    'placeholder' => 'https://tagesschau.de/...',
                ]
            ])
            ->add('description', TextType::class, [
                'label' => 'Beschreibung / Anmerkung',
                'attr' => [
                    'placeholder' => 'Stelle xy ist Fachlich unsauber',
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Quelle angeben',
                'attr' => [
                    'class' => 'btn-success btn-block'
                    ]
                ])

            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $link = $form->getNormData();
            $link->setTopic($topic);
            $this->em->persist($link);
            $this->em->flush();
        }

        $form = $form->createView();

        return $this->render('link/new.html.twig', [
            'form' => $form,
        ]);
    }
}
