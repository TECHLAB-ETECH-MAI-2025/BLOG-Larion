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
        // Création formulaire article
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

        // Préparer un tableau pour stocker un formulaire commentaire par article
        $commentairesForms = [];

        foreach ($articles as $articleEntity) {
            $commentaire = new Commentaire();
            $commentaire->setArticle($articleEntity);

            $commentaireForm = $this->createForm(CommentaireType::class, $commentaire);
            $commentaireForm->handleRequest($request);

            // Ici on vérifie si ce formulaire est soumis ET valide
            if ($commentaireForm->isSubmitted() && $commentaireForm->isValid()) {
                $em->persist($commentaire);
                $em->flush();

                return $this->redirectToRoute('app_home');
            }

            $commentairesForms[$articleEntity->getId()] = $commentaireForm->createView();
        }

        return $this->render('home/index.html.twig', [
            'articleForm' => $articleForm->createView(),
            'articles' => $articles,
            'commentairesForms' => $commentairesForms,
        ]);
    }
}
