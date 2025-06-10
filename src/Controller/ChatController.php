<?php

namespace App\Controller;

use App\Entity\Message;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;


class ChatController extends AbstractController
{
    #[Route('/chat', name: 'chat_index')]
    public function index(MessageRepository $repo): Response
    {
        // Récupérer tous les messages triés par date (du plus ancien au plus récent)
        $messages = $repo->findBy([], ['createdAt' => 'ASC']);

        return $this->render('chat/index.html.twig', [
            'messages' => $messages,
        ]);
    }

    #[Route('/chat/send', name: 'chat_send', methods: ['POST'])]
    public function send(Request $request, EntityManagerInterface $em, Security $security): JsonResponse
    {
        $content = trim($request->request->get('content'));
        $user = $security->getUser();

        if (!$user) {
            return new JsonResponse(['status' => 'error', 'message' => 'Non authentifié'], 401);
        }

        if (empty($content)) {
            return new JsonResponse(['status' => 'error', 'message' => 'Message vide'], 400);
        }

        $message = new Message();
        $message->setContent($content);
        $message->setUser($user);
        $message->setCreatedAt(new \DateTimeImmutable());

        $em->persist($message);
        $em->flush();

        return new JsonResponse(['status' => 'ok']);
    }

    #[Route('/chat/messages', name: 'chat_messages', methods: ['GET'])]
    public function messages(MessageRepository $repo): JsonResponse
    {
        $messages = $repo->findBy([], ['createdAt' => 'ASC']);

        $data = array_map(function ($m) {
            return [
                'user' => $m->getUser()->getUsername(),
                'content' => $m->getContent(),
                'date' => $m->getCreatedAt()->format('H:i'),
            ];
        }, $messages);

        return new JsonResponse($data);
    }
}
