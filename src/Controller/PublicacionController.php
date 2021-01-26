<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Publicacion;
use App\Entity\Likes;
use App\Form\PublicacionType;
use App\Form\LikeType;

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
            $descripcion = $edit_form->getData()->getDescription();
            preg_match_all('/#(\w+)/', $descripcion, $matches);
            foreach ($matches[1] as $match) {
                $keywords[] = $match;
            }
            $publicacion->setHastags($keywords);
            $em->persist($publicacion);
            $em->flush();
            return $this->redirectToRoute('user');
        }
        return $this->render('publicacion/edit.html.twig', [
            'edit_form' => $edit_form->createView()
        ]);
    }

    /**
     * @Route("/publicacion/show/{id}", name="publicacion_show")
     */
    public function show(Request $request, Publicacion $publicacion): Response
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $like_user = $em->getRepository(Likes::class)->getLikeByUserAndPublicacion($user, $publicacion);
        if (is_null($like_user)) {
            $like_user = new Likes();
            $like_user->setUser($user);
            $like_user->setPublicacion($publicacion);
        }
        $like_form = $this->createForm(LikeType::class, $like_user);
        $like_form->handleRequest($request);
        if ($like_form->isSubmitted() && $like_form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($like_user);
            $em->flush();
            return $this->redirectToRoute('publicacion_show', ['id' => $publicacion->getId()]);
        }

        $numero_likes = $publicacion->getLikes()->filter(function ($item) { return $item->getLiked(); })->count();
        return $this->render('publicacion/show.html.twig', [
            'publicacion' => $publicacion, 
            'numero_likes' => $numero_likes,
            'like_form' => $like_form->createView()
        ]);
    }
}
