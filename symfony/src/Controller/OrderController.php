<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Entity\ProductOrderQunatity;
use App\Entity\Products;
use App\Entity\Quantity;
use App\Entity\User;
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
        $user = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $orderData = $this->getDoctrine()->getRepository(Orders::class)->findBy(['user_id'=>$user]);
        foreach ($orderData as $data){
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
        $user = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $order = new Orders();
        $quantity = new Quantity();
        $pOq = new ProductOrderQunatity();
        $param = json_decode($request->getContent(),true);

        $order->setOrderCode($this->orderCode);
        $order->setAddress($param['address']);
        $order->setUserId($user);
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
     * @Route("/api/shipping-date/{id}", name="shipping_date", methods={"PUT"})
     */
    public function shipping_date($id): Response
    {
        $orderData = $this->getDoctrine()->getRepository(Orders::class)->find($id);
            $orderData->setShippingDate($this->nowDate);
            $shippingCreate = $this->getDoctrine()->getManager();
            $shippingCreate->persist($orderData);
            $shippingCreate->flush();

        return $this->json('Successfully');

    }
    /**
     * @Route("/api/orders/{id}", name="orders_update", methods={"PUT"})
     */
    public function update(Request $request, $id): Response{

        $user = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $productOrderQuantityData = $this->getDoctrine()->getRepository(ProductOrderQunatity::class)->find($id);
        $orderData = $this->getDoctrine()->getRepository(Orders::class)->find($productOrderQuantityData->getOrderId());
        $quantityData = $this->getDoctrine()->getRepository(Quantity::class)->find($productOrderQuantityData->getQuantityId());
        $param = json_decode($request->getContent(),true);
        if ($orderData->getUserId() === $user) {
            if ($orderData->getId() && $orderData->getShippingDate() === null) {
                $orderData->setAddress($param['address']);
                $orderData->setUpdatedAt($this->nowDate);
                $update = $this->getDoctrine()->getManager();
                $update->persist($orderData);
                $update->flush();
                $orderId = $orderData->getId();

                if ($quantityData->getId()) {
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
            } else {
                return $this->json('Sorry create for you shipping date!');
            }
        } else {
            return $this->json('Sorry order not found!');
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
    public function show($id): Response
    {
        $user = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $productOrderQuantityData = $this->getDoctrine()->getRepository(ProductOrderQunatity::class)->find($id);
        $orderData = $this->getDoctrine()->getRepository(Orders::class)->find($productOrderQuantityData->getOrderId());
        $quantityData = $this->getDoctrine()->getRepository(Quantity::class)->find($productOrderQuantityData->getQuantityId());
        $productData = $this->getDoctrine()->getRepository(Products::class)->find($productOrderQuantityData->getProductId());
        $userData = $this->getDoctrine()->getRepository(User::class)->find($productOrderQuantityData->getUserId());
        if ($user === $productOrderQuantityData->getUserId()){
            $data=[
                'Order Code'   => $orderData->getOrderCode(),
                'Product Name' => $productData->getProductName(),
                'Quantity' =>  $quantityData->getQuantity(),
                'Address' =>  $orderData->getAddress(),
                'Shipping Date' =>  $orderData->getShippingDate(),
                'User' =>  $userData->getEmail(),
            ];
            return $this->json($data);
        } else {
            return new Response("Order information not found!");
        }


    }
}
