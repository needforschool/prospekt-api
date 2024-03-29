<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\User;
use App\Entity\UserLog;
use App\Repository\UserLogRepository;
use Doctrine\Persistence\ManagerRegistry;
use mysql_xdevapi\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserLogController extends AbstractController
{
    protected $entityManager;

    public function __construct(ManagerRegistry $doctrine){
        $this->entityManager = $doctrine->getManager();
    }



    #[Route('/usersLog', name: 'app_users_log', methods: ['GET'])]
    public function index(UserLogRepository $userLogRepository): Response
    {

        $usersLog = $userLogRepository->findAll();

        for($i = 0; $i < count($usersLog); $i++){
            $data[] = [
                'id'            => $usersLog[$i]->getId(),
                'author_id'     => $usersLog[$i]->getAuthorId(),
                'target_id'     => $usersLog[$i]->getTargetId(),
                'type'          => $usersLog[$i]->getType(),
                'content'       => $usersLog[$i]->getContent(),
                'created_at'    => $usersLog[$i]->getCreatedAt()
            ];
        }
        return $this->json($data, Response::HTTP_OK);

    }
    #[Route('/UserLogCreate', name: 'app_user_log_create', methods: ['POST'])]
    public function createLog(Request $request,ManagerRegistry $doctrine): Response {

        $data = json_decode($request->getContent(), true);

        $target = $doctrine->getRepository(User::class)->find($data['idTarget']);
        if($target === null){
            return $this->json(['message' => 'target introuvable'], Response::HTTP_BAD_REQUEST);
        }

        $author = $doctrine->getRepository(User::class)->find($data['authorId']);
        if($author === null ){
            return $this->json(['message' => 'author introuvable'], Response::HTTP_BAD_REQUEST);
        }

        $userLog = new userLog();
        $userLog->setAuthorId($author)
                ->setTargetId($target)
                ->setType($data["type"])
                ->setContent($data['content'])
                ->setCreatedAt(new \DateTimeImmutable());
        try{
            $this->entityManager->persist($userLog);
            $this->entityManager->flush();
        }catch(\Exception $e){
            return $this->json($e->getMessage(), 500);
        }

        return $this->json(['message' => 'success'], Response::HTTP_OK);
    }

    // recupere les logs d'un client grâce à son id
    #[Route('/users/{id}/logs', name: 'app_user_log_get', methods: ['GET'])]
    public function getLog($id,ManagerRegistry $doctrine): Response {

        $userLog  = $doctrine->getRepository(UserLog::class)->getLogByTargetId($id);

        if(empty($userLog)){
            return $this->json(["message" => "Pas de log"], Response::HTTP_BAD_REQUEST);
        }

        return $this->json($userLog, Response::HTTP_OK);
    }
}
