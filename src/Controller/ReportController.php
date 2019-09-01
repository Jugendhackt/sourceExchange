<?php

namespace App\Controller;

use App\Entity\Link;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Report;
use App\Entity\Topic;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class ReportController extends AbstractController
{
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/report/new/topic/{topic}", name="report_topic")
     */
    public function newReportFromTopic(Topic $topic, Request $request)
    {
        $form = $this->_generateReportForm($topic);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $report = $form->getNormData();
            $report->setUser($this->getUser());
            $report->setTopic($topic);
            $this->em->persist($report);
            $this->em->flush();

            $this->addFlash('success', 'Meldung wurde erfolgreich erstellt');
            return $this->redirectToRoute('topic_show', [
                'id' => $topic->getId()
            ]);
        }

        $form = $form->createView();

        return $this->render('report/new.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/report/new/link/{link}", name="report_link")
     */
    public function newReportFromLink(Link $link, Request $request)
    {
        $topic = $link->getTopic();

        $form = $this->_generateReportForm($topic, $link);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $report = $form->getNormData();
            $report->setUser($this->getUser());
            $report->setTopic($topic);
            $report->setLink($link);
            $this->em->persist($report);
            $this->em->flush();

            $this->addFlash('success', 'Meldung wurde erfolgreich erstellt');
            return $this->redirectToRoute('topic_show', [
                'id' => $topic->getId()
            ]);
        }

        $form = $form->createView();

        return $this->render('report/new.html.twig', [
            'form' => $form,
        ]);
    }


    /**
     * @Route("/report/", name="report")
     * @IsGranted("ROLE_MODERATOR")
     */
    public function index()
    {
        return $this->render('report/index.html.twig', [
            'controller_name' => 'ReportController',
        ]);
    }

    /**
     * @Route("/report/view/{report}", name="report_view")
     * @IsGranted("ROLE_MODERATOR")
     */
    public function detail(Report $report)
    {
        return $this->render('report/view.html.twig', [
            'report' => $report,
        ]);
    }

    /**
     * @IsGranted("ROLE_MODERATOR")
     * @Route("/report/decide/{report}", name="report_decide")
     */
    public function decide(Report $report, Request $request)
    {
        $approve = (bool) $request->query->get('approve');

        if (!$approve)
        {
            $this->em->remove($report);
            $this->em->flush();
            $this->addFlash('success', 'Beschwerde wurde erfolgreich gelöscht');
            return $this->redirectToRoute('index');
        }

        $form = $this->createFormBuilder([])
            ->add('confirm', CheckboxType::class, [
                'required' => false,
                'label' => 'Gemeldeten Inhalt wirklich löschen',
                'help' => 'Damit wird der gemeldete Topic bzw. Link undiwederbringlich gelöscht',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Absenden',
                'attr' => [
                    'class' => 'btn btn-block btn-danger'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $formData = $form->getNormData();
            if ($formData['confirm'])
            {
                if ($report->getLink() !== null)
                {
                    $this->em->remove($report->getLink());
                }
                else
                {
                    foreach ($report->getTopic()->getLinks() as $link)
                    {
                        $this->em->remove($link);
                    }
                    $this->em->remove($report->getTopic());
                    $this->em->flush();
                }

                $this->addFlash('success', 'Beschwerde wurde erfolgreich gelöscht');
            }
            else
            {
                $this->addFlash('warning', 'Die Beschwerde wurde nicht gelöscht');
                return $this->redirectToRoute('report_view', [
                    'report' => $report->getId(),
                ]);
            }

            return $this->redirectToRoute('index');
        }

        $form = $form->createView();

        return $this->render('report/decide.html.twig', [
            'form' => $form,
        ]);
    }


    private function _generateReportForm(?Topic $topic = null, ?Link $link = null)
    {
        $report = new Report();
        $form = $this->createFormBuilder($report)
            ->add('description', TextareaType::class, [
                'label' => 'Beschreibung'
            ]);

        if (!is_null($topic))
        {
            $form->add('topicName', TextType::class, [
                'mapped' => false,
                'disabled' => 'true',
                'label' => 'Name des Themas',
                'data' => $topic->getTitle()
            ]);
        }

        if (!is_null($link))
        {
            $form->add('linkUrl', TextType::class, [
                'mapped' => false,
                'disabled' => 'true',
                'label' => 'Betroffener Link',
                'data' => $link->getHref()
            ]);
        }

        $form->add('submit', SubmitType::class, [
            'label' => 'Anbsenden',
            'attr' => [
                'class' => 'btn-outline-warning btn-block'
            ]]);

        return $form->getForm();
    }
}
