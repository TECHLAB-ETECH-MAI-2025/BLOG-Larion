<?php

namespace App\Controller\Api;

use App\Entity\Message;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/api/chat', name: 'api_chat_')]
class ChatController extends AbstractController
{
    #[Route('/messages', name: 'messages', methods: ['GET'])]
    public function messages(MessageRepository $repo): JsonResponse
    {
        $messages = $repo->findBy([], ['createdAt' => 'ASC']);

        $data = array_map(function ($m) {
            return [
                'id' => $m->getId(),
                'user' => $m->getUser()->getUsername(),
                'content' => $m->getContent(),
                'date' => $m->getCreatedAt()->format('H:i'),
            ];
        }, $messages);

        return new JsonResponse($data);
    }

    #[Route('/send', name: 'send', methods: ['POST'])]
    public function send(
        Request $request,
        EntityManagerInterface $em,
        Security $security
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $content = trim($data['content'] ?? '');
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

        // On ne publie plus sur Mercure

        return new JsonResponse(['status' => 'ok']);
    }
}
