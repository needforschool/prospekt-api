<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserLog;
use App\Repository\UserLogRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserLogController extends AbstractController
{
    #[Route('/api/userlogs', name: 'app_api_userlog', methods: ['GET'])]
    public function userLogs(UserLogRepository $userLogRepository): Response
    {
        $userLogs = $userLogRepository->findAll();
        $data = [];

        for($i = 0; $i < count($userLogs); $i++){
            $data[] = [
                'id' => $userLogs[$i]->getId(),
                'author_id' => $userLogs[$i]->getAuthorId()->getId(),
                'target_id' => $userLogs[$i]->getTargetId()->getId(),
                'type' => $userLogs[$i]->getType(),
                'content' => $userLogs[$i]->getContent(),
                'created_at' => $userLogs[$i]->getCreatedAt()
            ];
        }
        
        return $this->json($data, Response::HTTP_OK);   
    }

    #[Route('/api/add/userlog', name: 'app_api_add_userlog', methods: ['POST'])]
    public function addUserLog(Request $request, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $data = json_decode($request->getContent(), true);  
        $userLog = new UserLog();

        $userAuthor = $entityManager->getRepository(User::class)->find($data['author_id']);
        $userTarget = $entityManager->getRepository(User::class)->find($data['target_id']);
        
        $userLog->setAuthorId($userAuthor);
        $userLog->setTargetId($userTarget);
        
        $userLog->setType($data['type']);
        $userLog->setContent($data['content']);
        $userLog->setCreatedAt($data['created_at']);
        
        $entityManager->persist($userAuthor);
        $entityManager->persist($userTarget);
        $entityManager->persist($userLog);
        $entityManager->flush();
        //return new JsonResponse('success');
        return $this->json($userLog, Response::HTTP_OK);
    }
}
