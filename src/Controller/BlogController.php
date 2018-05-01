<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends BaseController
{
    private const PER_PAGE = 2;

    /**
     * @Route("/", name="blogIndex")
     */
    public function index(Request $request, PostRepository $postRepository)
    {
        $page = $request->get('page', 1);
        $count = $postRepository->count(['isPublic' => true]);

        return $this->render('blog/index.html.twig', [
            'posts' => $postRepository->findBy(
                ['isPublic' => true],
                ['updatedAt' => 'DESC'],
                self::PER_PAGE,
                ($page-1)*self::PER_PAGE
            ),
            'pages' => ceil($count / self::PER_PAGE),
            'currentPage' => $page,
        ]);
    }

    /**
     * @Route("/{url}", name="postDetail")
     */
    public function detail($url, PostRepository $postRepository, EntityManagerInterface $em)
    {
        $post = $postRepository->findOneBy(['url' => $url]);
        if(!$post) {
            throw new NotFoundHttpException('Blog post not found.');
        }

        $post->incrementView();

        $em->persist($post);
        $em->flush();

        return $this->render('blog/detail.html.twig',
            [
                'post' => $post,
            ]
        );
    }
}
