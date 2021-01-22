<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Publicacion;
use App\Form\PublicacionType;

class PublicacionController extends AbstractController
{
    /**
     * @Route("/publicacion", name="publicacion")
     */
    public function index(): Response
    {
        return $this->render('publicacion/index.html.twig', [
            'controller_name' => 'PublicacionController',
        ]);
    }

    /**
     * @Route("/publicacion/new", name="publicacion_new")
     */
    public function new(Request $request): Response
    {
        $publicacion = new Publicacion();
        $form = $this->createForm(PublicacionType::class, $publicacion);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            // Stablish the creator of Publicacion object (logged user)
            $publicacion->setUser($this->getUser());
            $em->persist($publicacion);
            $em->flush();
            return $this->redirectToRoute('user');
        }
        return $this->render('publicacion/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/publicacion/edit/{id}", name="publicacion_edit")
     */
    public function edit(Request $request, Publicacion $publicacion): Response
    {
        if ($publicacion->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('user');
        }
        $edit_form = $this->createForm(PublicacionType::class, $publicacion);
        $edit_form->handleRequest($request);
        if ($edit_form->isSubmitted() && $edit_form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($publicacion);
            $em->flush();
            return $this->redirectToRoute('user');
        }
        return $this->render('publicacion/edit.html.twig', [
            'edit_form' => $edit_form->createView()
        ]);
    }
}
