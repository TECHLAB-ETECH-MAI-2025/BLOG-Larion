<?php
namespace App\Controller;

use App\Entity\Article;
use App\Entity\Commentaire;
use App\Form\ArticleType;
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
        // Création d'un nouvel article
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('app_home');
        }

        // Récupérer tous les articles
        $articles = $em->getRepository(Article::class)->findAll();

        // Récupérer tous les commentaires
        $commentaires = $em->getRepository(Commentaire::class)->findAll();

        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
            'articles' => $articles,
            'commentaires' => $commentaires,
        ]);
    }
}
