<?php

namespace App\Controller;

use App\Entity\Shop;
use App\Repository\ShopRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Annotations as OA;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/api/shops")
 */

class ShopController extends AbstractController
{
    /**
     * @Route("/", name="list_shops", methods={"GET"})
     * @OA\Get(
     *     path="/shops",
     *     security={"bearer"},
     *     @OA\Response(
     *          response="200",
     *          description="Liste des magasins",
     *          @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Shop")),
     *      )
     * )
     */
    public function listShops(Request $request, ShopRepository $shopRepository, SerializerInterface $serializer, PaginatorInterface $paginator)
    {
        $shops = $shopRepository->findAll();

        $pages = $paginator->paginate( $shops, $request->query->getInt('page', 1), 1);
        $data = $serializer->serialize($pages->getItems(), 'json', SerializationContext::create()->setGroups(array('list')));
        $response = new JsonResponse($data, 200, [], true);

        return $response;
    }

    /**
     * @Route("/{id}", name="show_shop", methods={"GET"})
     * @OA\Get(
     *     path="/shops/{id}",
     *     security={"bearer"},
     *     @OA\Parameter(
     *        name="id",
     *     in="path",
     *     description="Id du magasin",
     *     required=true,
     *     @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Magasin par son id",
     *          @OA\Items(ref="#/components/schemas/Shop")),
     *      )
     * )
     */
    public function showShop(Shop $shop, SerializerInterface $serializer)
    {
        $data = $serializer->serialize($shop, 'json', SerializationContext::create()->setGroups(array('detail')));
        $response = new JsonResponse($data, 200, [], true);

        return $response;
    }
}
