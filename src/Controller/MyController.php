<?php

namespace App\Controller;

use App\Entity\Publication;
use mysql_xdevapi\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_USER")
 */
class MyController extends AbstractController
{
    #[Route('/my/publications', name: 'my_publications')]
    public function index(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $myPublications = $entityManager->getRepository(Publication::class)->findBy(['user' => $this->getUser()]);
        $myPublications = array_reverse($myPublications);

        return $this->render('my/index.html.twig', [
            'myPublications' => $myPublications
        ]);
    }

    #[Route('/my/publications/set_private/{id}', name: 'my_publications_set_as_private')]
    public function myPublicationSetAsPrivate(int $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $myPublication = $entityManager->getRepository(Publication::class)->find($id);


        if($this->getUser() == $myPublication->getUser())
        {
            try {
                $myPublication->setIsPublic(0);
                $entityManager->persist($myPublication);
                $entityManager->flush();
                $this->addFlash('success', 'Wpis został ustawiony jako prywatny');
            } catch (Exception $e) {
                $this->addFlash('error', 'Wystąpił błąd podczas ustawiania wpisu na prywatny');
            }
        } else {
            $this->addFlash('error', 'Nie jesteś właścicielem tego wpisu');
        }

        return $this->redirectToRoute('my_publications');
    }

    #[Route('/my/publications/set_public/{id}', name: 'my_publications_set_as_public')]
    public function myPublicationSetAsPublic(int $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $myPublication = $entityManager->getRepository(Publication::class)->find($id);

//        $this->denyAccessUnlessGranted('ROLE_USER');
        if($this->getUser() == $myPublication->getUser())
        {
            try {
                $myPublication->setIsPublic(1);
                $entityManager->persist($myPublication);
                $entityManager->flush();
                $this->addFlash('success', 'Wpis został ustawiony jako publiczny');
            } catch (Exception $e) {
                $this->addFlash('error', 'Wystąpił błąd podczas ustawiania wpisu na publiczny');
            }
        } else {
            $this->addFlash('error', 'Nie jesteś właścicielem tego wpisu');
        }

        return $this->redirectToRoute('my_publications');
    }


    #[Route('/my/publications/remove/{id}', name: 'my_publications_remove')]
    public function myPublicationRemove(int $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $myPublication = $entityManager->getRepository(Publication::class)->find($id);

//        $this->denyAccessUnlessGranted('ROLE_USER');
        if($this->getUser() == $myPublication->getUser())
        {
            $imageManager = new Filesystem();
            $imageManager->remove('images/PublicationImages/'.$myPublication->getFilename());
            if($imageManager->exists('images/PublicationImages/'.$myPublication->getFilename())) {
                $this->addFlash('error', 'Nie udało się usunąć wpisu!');
            } else {
                $entityManager->remove($myPublication);
                $entityManager->flush();
                $this->addFlash('success', 'Usunięto wpis!');
            }
        } else {
            $this->addFlash('error', 'Nie możesz usunąć publikacji, której nie jesteś właścicielem');
        }

        return $this->redirectToRoute('my_publications');
    }
}
