<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Annotations as OA;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Knp\Component\Pager\PaginatorInterface;

/**
* @Route("/api/users")
*/

class UserController extends AbstractController
{
    /**
    * @Route("/{page<\d+>?1}", name="list_users", methods={"GET"}, priority= -1)
    * @OA\Get(
    *     path="/users",
    *     security={"bearer"},
    *     @OA\Parameter(
    *        name="page",
    *     in="query",
    *     description="Liste des utilisateurs (5 par page)",
    *     required=false,
    *     @OA\Schema(type="integer")
    *      ),
    *      @OA\Response(
    *          response="200",
    *          description="Liste des utilisateurs",
    *          @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User")),
    *      )
    * )
    */
    public function listUser(Request $request, UserRepository $userRepository, SerializerInterface $serializer, PaginatorInterface $paginator)
    {

        $users = $userRepository->findAll();
        
        $pages = $paginator->paginate( $users, $request->query->getInt('page', 1), 5);
        $data = $serializer->serialize($pages->getItems(), 'json', SerializationContext::create()->setGroups(array('list')));
        $response = new JsonResponse($data, 200, [], true);
        
        return $response;
    }
    
    /**
    * @Route("/{id}", name="show_user", methods={"GET"})
    * @OA\Get(
    *     path="/users/{id}",
    *     security={"bearer"},
    *     @OA\Parameter(
    *        name="id",
    *     in="path",
    *     description="affichage d'un utilisateur par son id",
    *     required=true,
    *     @OA\Schema(type="integer")
    *      ),
    *      @OA\Response(
    *          response="200",
    *          description="Affiche un utilisateur par son id",
    *          @OA\JsonContent(ref="#/components/schemas/User"),
    *      )
    * )
    */
    public function showUser(User $user, UserRepository $userRepository, SerializerInterface $serializer)
    {
        $this->denyAccessUnlessGranted('GET_USER', $user, "Vous n'êtes pas autorisé à consulter la fiche de cet utilisateur");
        $user = $userRepository->find($user->getId());
        $data = $serializer->serialize($user, 'json', SerializationContext::create()->setGroups(array('detail')));
        $response = new JsonResponse($data, 200, [], true);
        
        return $response;
    }
    
    /**
    * @Route("/add", name="add_user", methods={"POST"})
    * @OA\Post(
    *     path="/users",
    *     security={"bearer"},
    *     @OA\Response(
    *          response="201",
    *          description="Création d'un utilisateur",
    *          @OA\JsonContent(ref="#/components/schemas/User"),
    *     )
    * )
    */
    public function addUser(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $json = $request->getContent();
        $userConnected = $this->getUser();
        try {
            $newUser = $serializer->deserialize($json, User::class, 'json');
            $newUser->setCreatedAt(new DateTime());
            $newUser->setShop($userConnected);
            
            $error = $validator->validate($newUser);
            
            if (count($error) > 0) {
                return  $this->json($error, 400);
            }
            
            $entityManager->persist($newUser);
            $entityManager->flush();
            
            $newUser = [
                'status' => 201,
                'message' => 'Le nouvel utilisateur a bien été ajouté !'
            ];
            
            return $this->json($newUser);
                
        } catch (NotEncodableValueException $e ) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage() 
                ], 400);
            }
    }
        
    /**
     * @Route("/{id}", name="delete_user", methods={"DELETE"})
     * @OA\Delete (
     *     path="/users",
     *     security={"bearer"},
     *     @OA\Response(
     *          response="204",
     *          description="Suppression d'un utilisateur",
     *          @OA\JsonContent(ref="#/components/schemas/Phone"),
     *     )
     * )
    */
    public function deleteUser(User $user, EntityManagerInterface $entityManager)
    {
        $this->denyAccessUnlessGranted('CAN_DELETE', $user, "Vous n'êtes pas autorisé à supprimer cet utilisateur");
        $entityManager->remove($user);
        $entityManager->flush();
        return new Response(null, 204);
    }
}
    