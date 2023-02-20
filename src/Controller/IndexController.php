<?php

namespace App\Controller;

use App\Entity\Publication;
use App\Form\UploadContentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(UploadContentType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            if ($this-> getUser()) {
                /** @var UploadedFile $pictureFileName */
                $pictureFileName = $form->get('filename')->getData();
                if ($pictureFileName) {
                    try {
                        $originalFileName = pathinfo($pictureFileName->getClientOriginalName(), PATHINFO_FILENAME);
                        $safeFileName = transliterator_transliterate('Any-latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFileName);
                        $newFileName = $safeFileName.'-'.uniqid().'.'.$pictureFileName->guessExtension();
                        $pictureFileName->move('images/PublicationImages', $newFileName);

                        $entityPublication = new Publication();
                        $entityPublication->setFilename($newFileName);
                        $entityPublication->setTitle($form->get('title')->getData());
                        $entityPublication->setContent($form->get('content')->getData());
                        $entityPublication->setIsPublic($form->get('is_public')->getData());
                        $entityPublication->setUploadedAt(new \DateTime());
                        $entityPublication->setUser($this->getUser());

                        $entityManager->persist($entityPublication);
                        $entityManager->flush();

                        $this->addFlash('success', 'Dodano wpis! :)');
                    } catch (\Exception $e) {
                        $this->addFlash('error', 'Wystąpił błąd podczas dodawania Twojego wpisu! :(');
                    }
                }
            }
        }

        return $this->render('index/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
