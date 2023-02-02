<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/api/users', name: 'app_api_users', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {

        $users = [];
        foreach ($userRepository->findAll() as $user) {
            $users[] = $user->getInfos();
        }
        return $this->json($users, Response::HTTP_OK);

    }

    #[Route('/api/user/{id}', name: 'app_api_user_id', methods: ['GET'])]
    public function userById(UserRepository $userRepository, $id): Response
    {
        if (!$userRepository->find($id)) {
            throw $this->createNotFoundException(
                'No user found with id '.$id
            );
        }
        $user = $userRepository->find($id)->getInfos();
        return $this->json($user, Response::HTTP_OK);

    }

    #[Route('/api/add/user', name: 'app_api_add_user', methods: ['POST'])]
    public function addUser(Request $request, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $data = json_decode($request->getContent(), true);
        $user = new User();
        
        $user->setType($data['type']);
        $user->setEmail($data['email']);
        $user->setTel($data['tel']);
        $user->setName($data['name']);
        $user->setPassword($data['password']);
        $user->setToken($data['token']);
        $user->setSiret($data['siret']);
        $user->setVat($data['vat']);
        $user->setUpdatedAt	($data['updated_at']);
        $user->setCreatedAt($data['created_at']);

        $entityManager->persist($user);
        $entityManager->flush();
        //return new JsonResponse('success');
        return $this->json($user, Response::HTTP_OK);

    }
}
