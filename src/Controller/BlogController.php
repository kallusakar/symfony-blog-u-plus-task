<?php

namespace App\Controller;

use App\Component\Paginator;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends BaseController
{
    /**
     * @Route("/", name="blogIndex")
     */
    public function index(Request $request, PostRepository $postRepository)
    {
        $paginator = new Paginator($request, $postRepository->count(['isPublic' => true]));


        return $this->render('blog/index.html.twig', [
            'paginator' => $paginator,
            'posts' => $postRepository->findBy(
                ['isPublic' => true],
                ['updatedAt' => 'DESC'],
                Paginator::PER_PAGE,
                $paginator->getOffset()
            )
        ]);
    }

    /**
     * @Route("/{url}", name="postDetail")
     */
    public function detail(string $url, PostRepository $postRepository, EntityManagerInterface $em)
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
