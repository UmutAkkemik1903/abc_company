<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Entity\ProductOrderQunatity;
use App\Entity\Products;
use App\Entity\Quantity;
use App\Repository\OrdersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;


class OrderController extends AbstractController
{
    public $nowDate;
    public $orderCode;
    public function __construct($args = 'now')
    {
        $this->nowDate = new \DateTimeImmutable($args);
        $min = 100000000;
        $max = 999999999;
        $sc = "OC";
        $this->orderCode =$sc.rand($min,$max);
    }

    /**
     * @Route("/api/orders", name="orders", methods={"GET"})
     */
    public function index(): JsonResponse
    {
        $orderRespository = $this->getDoctrine()->getRepository(Orders::class)->findAll();
        foreach ($orderRespository as $data){
            $res[] = [
                'id' => $data->getId(),
                'order_code' => $data->getOrderCode(),
                'address' => $data->getAddress(),
                'created_at' => $data->getCreatedAt(),
                'updated_at' => $data->getUpdatedAt(),
                'deleted_at' => $data->getDeletedAt(),
            ];
        }
        return $this->json($res);
    }

    /**
     * @Route("/api/orders", name="orders_create", methods={"POST"})
     */
    public function create(Request $request): Response
    {
        $order = new Orders();
        $quantity = new Quantity();
        $pOq = new ProductOrderQunatity();
        $param = json_decode($request->getContent(),true);

        $order->setOrderCode($this->orderCode);
        $order->setAddress($param['address']);
        $order->setCreatedAt($this->nowDate);
        $create = $this->getDoctrine()->getManager();
        $create->persist($order);
        $create->flush();
        $id = $order->getId();

        $quantity->setQuantity($param['quantity']);
        $quantity->setCreatedAt($this->nowDate);
        $createQ = $this->getDoctrine()->getManager();
        $createQ->persist($quantity);
        $createQ->flush();
        $quantityId = $quantity->getId();

        $user = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $pOq->setProductId($param['product_id']);
        $pOq->setOrderId($id);
        $pOq->setQuantityId($quantityId);
        $pOq->setUserId($user);
        $createOq = $this->getDoctrine()->getManager();
        $createOq->persist($pOq);
        $createOq->flush();

        return $this->json('Successfully');

    }

    /**
     * @Route("/api/shipping-date", name="shipping_date", methods={"POST"})
     */
    public function shipping_date(Request $request): Response
    {
        $order = new Orders();
        $param = json_decode($request->getContent(),true);

        $order->setShippingDate($param['shipping_date']);
        $create = $this->getDoctrine()->getManager();
        $create->persist($order);
        $create->flush();

        return $this->json('Successfully');

    }
    /**
     * @Route("/api/orders/{id}", name="orders_update", methods={"PUT"})
     */
    public function update(Request $request, $id): Response{

        $productOrderQuantityData = $this->getDoctrine()->getRepository(ProductOrderQunatity::class)->find($id);
        $orderData = $this->getDoctrine()->getRepository(Orders::class)->find($productOrderQuantityData->getOrderId());
        $quantityData = $this->getDoctrine()->getRepository(Quantity::class)->find($productOrderQuantityData->getQuantityId());
        $param = json_decode($request->getContent(),true);
        if ($orderData->getId() && $orderData->getShippingDate() === null)
        {
                $orderData->setAddress($param['address']);
                $orderData->setUpdatedAt($this->nowDate);
                $update = $this->getDoctrine()->getManager();
                $update->persist($orderData);
                $update->flush();
                $orderId = $orderData->getId();

            if ($quantityData->getId())
            {
                $quantityData->setQuantity($param['quantity']);
                $quantityData->setUpdatedAt($this->nowDate);
                $updateQ = $this->getDoctrine()->getManager();
                $updateQ->persist($quantityData);
                $updateQ->flush();
                $quantityId = $quantityData->getId();
            }
                $productOrderQuantityData->setProductId($param['product_id']);
                $productOrderQuantityData->setOrderId($orderId);
                $productOrderQuantityData->setQuantityId($quantityId);
                $updateOq = $this->getDoctrine()->getManager();
                $updateOq->persist($productOrderQuantityData);
                $updateOq->flush();

            return $this->json('Successfully');
        } else{
            return $this->json('Sorry create for you shipping date!');
        }

    }
    /**
     * @Route("/api/orders/{id}", name="orders_delete", methods={"DELETE"})
     */
    public function delete(Request $request, $id): Response
    {
        $data = $this->getDoctrine()->getRepository(Orders::class)->find($id);

        $data->setDeletedAt($this->nowDate);
        $update = $this->getDoctrine()->getManager();
        $update->persist($data);
        $update->flush();

        return $this->json('Successfully');
    }

    /**
     * @Route("/api/orders-detail/{id}", name="orders_detail", methods={"GET"})
     */
    public function show(int $id, OrdersRepository $ordersRepository): Response
    {
        $orders = $ordersRepository
            ->find($id);

        return $this->json($orders);
    }
}
