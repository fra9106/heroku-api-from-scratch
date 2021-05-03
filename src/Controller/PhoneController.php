<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Repository\PhoneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use OpenApi\Annotations as OA;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Knp\Component\Pager\PaginatorInterface;


/**
 * @Route("/api/phones")
 */

class PhoneController extends AbstractController
{
    /**
     * @Route("/{page<\d+>?1}", name="list_phone", methods={"GET"}, priority= -1)
     * @OA\Get(
     *     path="/phones",
     *     @OA\Parameter(
     *        name="page",
     *     in="query",
     *     description="Liste des produits (5 par page)",
     *     required=false,
     *     @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Liste des produits",
     *          @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Phone")),
     *      )
     * )
     */
    public function listPhone(Request $request, PhoneRepository $phoneRepository, SerializerInterface $serializer, PaginatorInterface $paginator)
    {
        $phones = $phoneRepository->findAll();

        $pages = $paginator->paginate( $phones, $request->query->getInt('page', 1), 5);
        $data = $serializer->serialize($pages->getItems(), 'json', SerializationContext::create()->setGroups(array('list')));

        $response = new JsonResponse($data, 200, [], true);
        return $response;
    }

    /**
     * @Route("/{id}", name="show_phone", methods={"GET"})
     * @OA\Get(
     *     path="/phones/{id}",
     *     security={"bearer"},
     *     @OA\Parameter(
     *        name="id",
     *     in="path",
     *     description="Id du produit",
     *     required=true,
     *     @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Affichage d'un produit par son id",
     *          @OA\JsonContent(ref="#/components/schemas/Phone"),
     *      )
     * )
     */
    public function showPhone(Phone $phone, SerializerInterface $serializer)
    {
        $data = $serializer->serialize($phone, 'json',SerializationContext::create()->setGroups(array('detail')) );
        $response = new JsonResponse($data, 200, [], true);

        return $response;
    }

    /**
     * @Route("/addphone", name="add_phone", methods={"POST"})
     * @IsGranted("ROLE_ADMIN", statusCode=403, message="Vous n'avez pas les droits administrateur pour ajouter un produit !")
     * @OA\Post(
     *     path="/phones",
     *     security={"bearer"},
     *     @OA\Response(
     *          response="201",
     *          description="Création d'un produit",
     *          @OA\JsonContent(ref="#/components/schemas/Phone"),
     *     )
     * )
     */
    public function addPhone(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $json = $request->getContent();
        try {
            $newPhone = $serializer->deserialize($json, Phone::class, 'json');

            $error = $validator->validate($newPhone);

            if (count($error) > 0) {
                return  $this->json($error, 400);
            }

            $entityManager->persist($newPhone);
            $entityManager->flush();

            $newUser = [
                'status' => 201,
                'message' => 'Le nouveau produit a bien été ajouté !'
            ];

            return $this->json($newUser);

        } catch (NotEncodableValueException $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * @Route("/{id}", name="update_phone", methods={"PUT"})
     * @IsGranted("ROLE_ADMIN", statusCode=403, message="Vous n'avez pas les droits administrateur pour modifier ce produit !")
     * @OA\Put(
     *     path="/phones/{id}",
     *     security={"bearer"},
     *     @OA\Parameter(
     *        name="id",
     *     in="path",
     *     description="Modifie un produit",
     *     required=true,
     *     @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Le phone",
     *          @OA\JsonContent(ref="#/components/schemas/Phone"),
     *      )
     * )
     */
    public function updatePhone(Phone $phone, Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $updatePhone = $entityManager->getRepository(Phone::class)->find($phone->getId());
        $data = json_decode($request->getContent());
        foreach ($data as $key => $value) {
            if ($key && !empty($value)) {
                $name = ucfirst($key);
                $set = 'set' . $name;
                $updatePhone->$set($value);
            }
        }
        $errors = $validator->validate($updatePhone);
        if (count($errors)) {
            $errors = $serializer->serialize($errors, 'json');
            return new Response($errors, 500, [
                'Content-Type' => 'application/json',

            ]);
        }
        $entityManager->flush();
        $data = [
            'status' => 200,
            'message' => 'Produit modifié !'
        ];
        return new JsonResponse($data);
    }

    /**
     * @Route("/{id}", name="delete_phone", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN", statusCode=403, message="Vous n'avez pas les droits administrateur pour supprimer ce produit !")
     * @OA\Delete (
     *     path="/phones",
     *     security={"bearer"},
     *     @OA\Response(
     *          response="204",
     *          description="Suppression d'un produit",
     *          @OA\JsonContent(ref="#/components/schemas/Phone"),
     *     )
     * )
     */
    public function deletePhone(Phone $phone, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($phone);
        $entityManager->flush();
        return new Response(null, 204);
    }
}
