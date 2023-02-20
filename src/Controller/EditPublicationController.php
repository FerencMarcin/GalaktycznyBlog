<?php

namespace App\Controller;

use App\Entity\Publication;
use App\Form\UpdateContentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_USER")
 */
class EditPublicationController extends AbstractController
{
    #[Route('/edit/{location}/{id}', name: 'edit_publication')]
    public function index(int $id, string $location, Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $myPublication = $entityManager->getRepository(Publication::class)->find($id);

        $form = $this->createForm(UpdateContentType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this-> getUser()) {
                /** @var UploadedFile $pictureFileName */

                $pictureFileName = $form->get('filename')->getData();
                try {
                    if ($pictureFileName) {
                        $originalFileName = pathinfo($pictureFileName->getClientOriginalName(), PATHINFO_FILENAME);
                        $safeFileName = transliterator_transliterate('Any-latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFileName);
                        $newFileName = $safeFileName.'-'.uniqid().'.'.$pictureFileName->guessExtension();
                        $pictureFileName->move('images/PublicationImages', $newFileName);

                        $imageManager = new Filesystem();
                        $imageManager->remove('images/PublicationImages/'.$myPublication->getFilename());
                        $myPublication->setFilename($newFileName);
                    }
                    $myPublication->setTitle($form->get('title')->getData());
                    $myPublication->setContent($form->get('content')->getData());
                    $myPublication->setIsPublic($form->get('is_public')->getData());

                    $entityManager->persist($myPublication);
                    $entityManager->flush();

                    $this->addFlash('success', 'Pomyślnie edytowano wpis! :)');

                } catch (\Exception $e) {
                    $this->addFlash('error', 'Wystąpił błąd podczas edycji Twojego wpisu! :(');
                }
            }
        }


        return $this->render('edit_publication/index.html.twig', [
            'form' => $form->createView(),
            'location' => $location,
            'publication' => $myPublication
        ]);
    }
}
