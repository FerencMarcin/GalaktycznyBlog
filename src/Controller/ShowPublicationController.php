<?php

namespace App\Controller;

use App\Entity\Publication;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShowPublicationController extends AbstractController
{
    #[Route('/show/publication/{location}/{id}', name: 'show_publication')]
    public function index(int $id, string $location): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $myPublication = $entityManager->getRepository(Publication::class)->find($id);

        return $this->render('show_publication/index.html.twig', [
            'publication' => $myPublication,
            'location' => $location
        ]);
    }
}
