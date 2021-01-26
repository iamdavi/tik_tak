<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\UserType;
use App\Form\SeguirUserType;
use App\Form\PublicacionHastagType;
use App\Entity\User;
use App\Entity\Publicacion;

class UserController extends AbstractController
{
    /**
     * Profile view of logged user
     * 
     * @Route("/user", name="user")
     */
    public function index(Request $request): Response
    {
        // Usuario logueado
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $publicaciones = $em->getRepository(Publicacion::class)->getPublicacionesByUser($user);
        $hastags_existentes = $em->getRepository(Publicacion::class)->getAllHastags();
        array_unshift($hastags_existentes, null);
        $filter_form = $this->createForm(PublicacionHastagType::class, $user, [
            'hastags' => $hastags_existentes
        ]);
        $filter_form->handleRequest($request);
        if ($filter_form->isSubmitted() && $filter_form->isValid()) {
            $hastag_seleccionado = $filter_form['hastag']->getData();
            $publicaciones = $em->getRepository(Publicacion::class)->getPublicacionesByHastag($hastag_seleccionado);
        }
        return $this->render('user/index.html.twig', [
            'user' => $user,
            'publicaciones' => $publicaciones,
            'filter_form' => $filter_form->createView()
        ]);
    }

    /**
     * @Route("/user/edit", name="user_edit")
     */
    public function edit(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('user');
        }
        return $this->render('user/edit.html.twig', [
            'form_edit' => $form->createView()
        ]);
    }

    /**
     * @Route("/user/list", name="user_list")
     */
    public function list(EntityManagerInterface $em): Response
    {
        $users = $em->getRepository(User::class)->getRestoUsuarios($this->getUser());

        return $this->render('user/list.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/user/{id}", name="user_show")
     */
    public function show(Request $request, EntityManagerInterface $em, User $user): Response
    {
        $publicaciones = $user->getPublicaciones();
        $logged_user = $this->getUser();
        $siguiendo = $logged_user->getMyFriends()->contains($user);
        $form = $this->createForm(SeguirUserType::class, $user, [
            'siguiendo' => $siguiendo
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $seguir = $form->get('seguirUsuario')->getData();
            if ($seguir) {
                $logged_user->addMyFriend($user); 
            } else {
                $logged_user->removeMyFriend($user); 
            }
            $em->flush();
            return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
        }
        return $this->render('user/show.html.twig', [
            'user' => $user,
            'publicaciones' => $publicaciones,
            'seguir_form' => $form->createView()
        ]);
    }

    /**
     * @Route("/test", name="user_test")
     */
    public function test(): Response
    {
        ob_start();
        phpinfo();
        $phpinfo = ob_get_clean();

        return new Response(
            '<html><body>'.$phpinfo.'</body></html>'
        );
    }
}
