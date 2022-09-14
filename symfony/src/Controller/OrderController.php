<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Entity\Quantity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class OrderController extends AbstractController
{
    public function __construct()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $this->nowDate = new \DateTime();
        $min = 100000000;
        $max = 999999999;
        $sc = "SC";
        $this->orderCode =$sc.rand($min,$max);
    }

    /**
     * @Route("/orders", name="orders", methods={"GET"})
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
     * @Route("/orders", name="orders_create", methods={"POST"})
     */
    public function create(Request $request): Response
    {
        $order = new Orders();
        $param = json_decode($request->getContent(),true);

        $order->setOrderCode($this->orderCode);
        $order->setAddress($param['address']);
        $order->setCreatedAt($this->nowDate);
        $create = $this->getDoctrine()->getManager();
        $create->persist($order);
        $create->flush();

        return $this->json('Successfully');

    }
    /**
     * @Route("/shipping-date", name="shipping_date", methods={"POST"})
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
     * @Route("/orders/{id}", name="orders_update", methods={"PUT"})
     */
    public function update(Request $request, $id): Response{

        $data = $this->getDoctrine()->getRepository(Orders::class)->find($id);
        $param = json_decode($request->getContent(),true);
        if ($data->getShippingDate() === null)
        {
            $data->setAddress($param['address']);
            $data->setUpdatedAt($this->nowDate);
            $update = $this->getDoctrine()->getManager();
            $update->persist($data);
            $update->flush();

            return $this->json('Successfully');
        } else{
            return $this->json('Sorry create for you shipping date!');
        }

    }
    /**
     * @Route("/orders/{id}", name="orders_delete", methods={"DELETE"})
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
     * @Route("/orders-detail/{id}", name="orders_detail", methods={"GET"})
     */
    public function orderDetail($id): Response
    {
        $repository = $this->getDoctrine()->getRepository(Orders::class)->find($id);
        return $this->json($repository);
    }
}
