<?php

namespace App\Controller;

use App\Entity\Link;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Report;
use App\Entity\Topic;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class ReportController extends AbstractController
{
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/report/new/topic/{topic}")
     */
    public function newReportFromTopic(Topic $topic)
    {
        $report = new Report();
        $report->setUser($this->getUser());
        $report->setTopic($topic);
        $this->em->persist($report);
        $this->em->flush();

        return $this->redirectToRoute('topic_show', [
            'topic' => $topic
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/report/new/link/{link}")
     */
    public function newReportFromLink(Link $link)
    {
        $report = new Report();
        $report->setUser($this->getUser());
        $report->setLink($link);
        $this->em->persist($report);
        $this->em->flush();

        return $this->redirectToRoute('topic_show', [
            'topic' => $link->getTopic(),
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
}
