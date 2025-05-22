<?php

namespace App\Controller\Api;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\ArticleLike;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Repository\ArticleLikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ArticleController extends AbstractController
{
    #[Route('/articles', name: 'api_articles', methods: ['POST'])]
    public function list(Request $request, ArticleRepository $articleRepository): JsonResponse
    {
        $draw = $request->request->getInt('draw');
        $start = $request->request->getInt('start');
        $length = $request->request->getInt('length');
        $search = $request->request->all('search')['value'] ?? null;
        $orders = $request->request->all('order') ?? [];

        $columns = [
            0 => 'a.id',
            1 => 'a.titre',
            2 => 'categories',
            3 => 'commentsCount',
            4 => 'likesCount',
            5 => 'a.dateCreation',
        ];

        $orderColumn = $columns[$orders[0]['column'] ?? 0] ?? 'a.id';
        $orderDir = $orders[0]['dir'] ?? 'desc';

        $results = $articleRepository->findForDataTable($start, $length, $search, $orderColumn, $orderDir);

        $data = [];
        foreach ($results['data'] as $article) {
            $categoryNames = array_map(fn($category) => $category->getNom(), $article->getCategories()->toArray());

            $data[] = [
                'id' => $article->getId(),
                'title' => sprintf(
                    '<a href="%s">%s</a>',
                    $this->generateUrl('app_article_show', ['id' => $article->getId()]),
                    htmlspecialchars($article->getTitre())
                ),
                'categories' => implode(', ', $categoryNames),
                'commentsCount' => $article->getCommentaires()->count(),
                'likesCount' => $article->getArticleLikes()->count(),
                'createdAt' => $article->getDateCreation()?->format('d/m/Y H:i') ?? '',
                'actions' => $this->renderView('article/_actions.html.twig', [
                    'article' => $article,
                ]),
            ];
        }

        return new JsonResponse([
            'draw' => $draw,
            'recordsTotal' => $results['totalCount'],
            'recordsFiltered' => $results['filteredCount'],
            'data' => $data,
        ]);
    }

    #[Route('/articles/search', name: 'api_articles_search', methods: ['GET'])]
    public function search(Request $request, ArticleRepository $articleRepository): JsonResponse
    {
        $query = $request->query->get('q', '');

        if (strlen($query) < 2) {
            return new JsonResponse(['results' => []]);
        }

        $articles = $articleRepository->searchByTitle($query, 10);

        $results = [];
        foreach ($articles as $article) {
            $categoryNames = array_map(fn($category) => $category->getNom(), $article->getCategories()->toArray());

            $results[] = [
                'id' => $article->getId(),
                'title' => $article->getTitre(),
                'categories' => $categoryNames,
            ];
        }

        return new JsonResponse(['results' => $results]);
    }

    #[Route('/article/{id}/comment', name: 'api_article_comment', methods: ['POST'])]
    public function addComment(Article $article, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $comment = new Comment();
        $comment->setArticle($article);
        $comment->setCreatedAt(new \DateTimeImmutable());

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($comment);
            $em->flush();

            return new JsonResponse([
                'success' => true,
                'commentHtml' => $this->renderView('commentaire/_comment.html.twig', [
                    'comment' => $comment,
                ]),
                'commentsCount' => $article->getCommentaires()->count(),
            ]);
        }

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }

        return new JsonResponse([
            'success' => false,
            'error' => count($errors) > 0 ? $errors[0] : 'Formulaire invalide',
        ], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/article/{id}/like', name: 'api_article_like', methods: ['POST'])]
    public function likeArticle(
        Article $article,
        Request $request,
        EntityManagerInterface $em,
        ArticleLikeRepository $likeRepository
    ): JsonResponse {
        $ipAddress = $request->getClientIp();

        $existingLike = $likeRepository->findOneBy([
            'article' => $article,
            'ipAddress' => $ipAddress,
        ]);

        if ($existingLike) {
            $em->remove($existingLike);
            $em->flush();

            return new JsonResponse([
                'success' => true,
                'liked' => false,
                'likesCount' => $article->getArticleLikes()->count(),
            ]);
        }

        $like = new ArticleLike();
        $like->setArticle($article);
        $like->setIpAddress($ipAddress);
        $like->setCreatedAt(new \DateTimeImmutable());

        $em->persist($like);
        $em->flush();

        return new JsonResponse([
            'success' => true,
            'liked' => true,
            'likesCount' => $article->getArticleLikes()->count(),
        ]);
    }
}
