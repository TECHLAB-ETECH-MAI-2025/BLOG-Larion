<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\ArticleLike;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Repository\ArticleLikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
#[Route('/article')]
class ArticleController extends AbstractController
{
    #[Route('/', name: 'app_article_index', methods: ['GET'])]
    public function index(Request $request, ArticleRepository $articleRepository, PaginatorInterface $paginator): Response
    {
        $query = $articleRepository->createQueryBuilder('a')->getQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('article/index.html.twig', [
            'articles' => $pagination,
        ]);
    }

    #[Route('/new', name: 'app_article_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('article/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_article_show', methods: ['GET'])]
    public function show(Article $article, Request $request, ArticleLikeRepository $likeRepository): Response
    {
        $ipAddress = $request->getClientIp();

        $isLiked = $likeRepository->findOneBy([
            'article' => $article,
            'ipAddress' => $ipAddress,
        ]) !== null;

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'is_liked' => $isLiked,
        ]);
    }

    #[Route('/article/{id}/like', name: 'app_article_like', methods: ['POST'])]
public function like(Article $article, Request $request, EntityManagerInterface $em, ArticleLikeRepository $likeRepo): JsonResponse
{
    $user = $this->getUser();
    if (!$user) {
        return new JsonResponse(['status' => 'error', 'message' => 'Connectez-vous d\'abord.'], 403);
    }

    $data = json_decode($request->getContent(), true);
    $token = $data['_token'] ?? '';

    if (!$this->isCsrfTokenValid('like' . $article->getId(), $token)) {
        return new JsonResponse(['status' => 'error', 'message' => 'Token CSRF invalide.'], 400);
    }

    $existingLike = $likeRepo->findOneBy(['article' => $article, 'user' => $user]);

    if ($existingLike) {
        $em->remove($existingLike);
    } else {
        $like = new ArticleLike();
        $like->setArticle($article);
        $like->setUser($user);
        $em->persist($like);
    }

    $em->flush();

    return new JsonResponse([
        'status' => 'success',
        'likes' => $likeRepo->count(['article' => $article])
    ]);
}

    #[Route('/{id}/edit', name: 'app_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_article_delete', methods: ['POST'])]
    public function delete(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
    }
}
