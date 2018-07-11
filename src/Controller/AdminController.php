<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\AdminLoginType;
use App\Form\PostType;
use App\Repository\PostRepository;
use Cocur\Slugify\Slugify;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends BaseController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        if ($this->authManager->isLoggedIn()) {
            return $this->redirectToRoute('listPost');
        }

        return $this->redirectToRoute('adminLogin');
    }

    /**
     * @Route("/admin/post/new", name="newPost")
     */
    public function newPost(Request $request)
    {
        if (!$this->authManager->isLoggedIn()) {
            return $this->redirectToLogin();
        }

        $form = $this->createForm(PostType::class, new Post());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Post $post */
            $post = $form->getData();
            $post->setAuthor($this->authManager->getUser())
                 ->setPublishedAt(new \DateTime());

            if (!$post->getUrl()) {
                $slugify = new Slugify();
                $post->setUrl($slugify->slugify($post->getTitle()));
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            $this->addFlash(
                'success',
                'The new blog post has been created successfully.'
            );

            return $this->redirectToRoute('editPost', ['id' => $post->getId()]);
        }

        return $this->render(
            'admin/newPost.html.twig',
            [
                'form' => $form->createView(),
                'user' => $this->authManager->getUser(),
            ]
        );
    }

    /**
     * @Route("/admin/post/edit/{id}", name="editPost")
     */
    public function editPost(
        Request $request,
        int $id,
        PostRepository $postRepository
    ) {
        if (!$this->authManager->isLoggedIn()) {
            return $this->redirectToLogin();
        }

        $post = $postRepository->find($id);

        if(!$post) {
            throw new NotFoundHttpException();
        }

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Post $post */
            $post = $form->getData();
            $post->setUpdatedAt(new \DateTime());

            if (!$post->getUrl()) {
                $slugify = new Slugify();
                $post->setUrl($slugify->slugify($post->getTitle()));
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            $this->addFlash(
                'success',
                'The blog post has been changed successfully.'
            );

            return $this->redirectToRoute(
                'editPost',
                [
                    'id' => $post->getId(),
                ]
            );
        }

        return $this->render(
            'admin/editPost.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @Route("/admin/post/list", name="listPost")
     */
    public function listPost(PostRepository $postRepository)
    {
        if (!$this->authManager->isLoggedIn()) {
            return $this->redirectToLogin();
        }

        return $this->render(
            'admin/listPost.html.twig',
            ['posts' => $postRepository->findAll()]
        );
    }

    /**
     * @Route("/admin/login", name="adminLogin")
     */
    public function login(Request $request): Response
    {
        if ($this->authManager->isLoggedIn()) {
            return $this->redirectToRoute('listPost');
        }

        $form = $this->createForm(AdminLoginType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->handleUserLogin($request);
        }

        return $this->render(
            'admin/login.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @Route("/admin/logout", name="adminLogout")
     */
    public function logout()
    {
        if (!$this->authManager->isLoggedIn()) {
            return $this->redirectToRoute('blogIndex');
        }

        $this->authManager->logout();

        return $this->redirectToLogin('You\'ve been successfully logged out');
    }

    private function handleUserLogin(Request $request): RedirectResponse
    {
        $formData = $request->get('admin_login');

        if (!$this->authManager->login(
            $formData['username'],
            $formData['password']
        )) {
            $this->addFlash('error', 'Wrong username or password');

            return $this->redirectToRoute('adminLogin');
        }

        return $this->redirectToRoute('listPost');
    }
}
