<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Publicacion;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="default")
     */
    public function index(EntityManagerInterface $em): Response
    {
        $publicaciones_publicas = $em->getRepository(Publicacion::class)->getPublicacionesPublicas();
        return $this->render('default/index.html.twig', [
            'publicaciones' => $publicaciones_publicas
        ]);
    }
}
