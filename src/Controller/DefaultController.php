<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Publicacion;
use App\Form\PublicacionHastagType;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="default")
     */
    public function index(EntityManagerInterface $em, Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $publicaciones = $em->getRepository(Publicacion::class)->findBy([], ['created' => 'desc']);
        $hastags_existentes = $em->getRepository(Publicacion::class)->getAllHastags();
        array_unshift($hastags_existentes, null);
        $filter_form = $this->createForm(PublicacionHastagType::class, null, [
            'hastags' => $hastags_existentes
        ]);
        $filter_form->handleRequest($request);
        if ($filter_form->isSubmitted() && $filter_form->isValid()) {
            $hastag_seleccionado = $filter_form['hastag']->getData();
            $publicaciones = $em->getRepository(Publicacion::class)->getPublicacionesByHastag($hastag_seleccionado);
        }
        return $this->render('default/index.html.twig', [
            'publicaciones' => $publicaciones,
            'filter_form' => $filter_form->createView()
        ]);
    }
}
