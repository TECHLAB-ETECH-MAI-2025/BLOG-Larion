<?php
namespace App\Controller;

use App\Entity\Article;
use App\Entity\Commentaire;
use App\Form\ArticleType;
use App\Form\CommentaireType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        // Formulaire de création d'article
        $article = new Article();
        $articleForm = $this->createForm(ArticleType::class, $article);
        $articleForm->handleRequest($request);

        if ($articleForm->isSubmitted() && $articleForm->isValid()) {
            $em->persist($article);
            $em->flush();
            return $this->redirectToRoute('app_home');
        }

        // Récupérer tous les articles
        $articles = $em->getRepository(Article::class)->findAll();

        // Créer les formulaires de commentaires pour chaque article
        $commentairesForms = [];

        foreach ($articles as $articleEntity) {
            $commentaire = new Commentaire();
            $commentaire->setArticle($articleEntity);

            $form = $this->createForm(CommentaireType::class, $commentaire, [
                'action' => $this->generateUrl('app_home', ['comment_article_id' => $articleEntity->getId()]),
                'method' => 'POST',
            ]);

            $commentairesForms[$articleEntity->getId()] = $form->handleRequest($request)->createView();
        }

        // Vérifier si un commentaire a été soumis
        $commentArticleId = $request->query->get('comment_article_id');
        if ($commentArticleId) {
            $articleCommented = $em->getRepository(Article::class)->find($commentArticleId);
            if ($articleCommented) {
                $commentaire = new Commentaire();
                $commentaire->setArticle($articleCommented);

                $form = $this->createForm(CommentaireType::class, $commentaire);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $em->persist($commentaire);
                    $em->flush();
                    return $this->redirectToRoute('app_home');
                }
            }
        }

        return $this->render('home/index.html.twig', [
            'articleForm' => $articleForm->createView(),
            'articles' => $articles,
            'commentairesForms' => $commentairesForms,
        ]);
    }
}
