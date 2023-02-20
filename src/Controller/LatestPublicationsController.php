<?php

namespace App\Controller;

use App\Entity\Publication;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LatestPublicationsController extends AbstractController
{
    #[Route('/latest', name: 'latest_publications')]
    public function index(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
//        $latestPublicationsPublic = $entityManager->getRepository(Publication::class)->findBy(['is_public' => true]);
//        $latestPublicationsPublic = array_reverse($latestPublicationsPublic);
        $latestPublicationsPublic = $entityManager->getRepository(Publication::class)->findAllPublicPublications();

        return $this->render('latest_publications/index.html.twig', [
            'latestPublicationsPublic' => $latestPublicationsPublic,
        ]);
    }
}
