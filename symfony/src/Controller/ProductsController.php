<?php

namespace App\Controller;

use App\Entity\Products;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ProductsController extends AbstractController
{

    public function __construct()
    {
        $this->nowDate = new \DateTime();
    }

    /**
     * @Route("/products", name="app_products", methods={"GET"})
     */
    public function index(): JsonResponse
    {
        $productRespository = $this->getDoctrine()->getRepository(Products::class)->findAll();
        foreach ($productRespository as $data){
            $res[] = [
                'id' => $data->getId(),
                'product_name' => $data->getProductName(),
                'description' => $data->getDescription(),
                'price' => $data->getPrice(),
                'created_at' => $data->getCreatedAt(),
                'updated_at' => $data->getUpdatedAt(),
                'deleted_at' => $data->getDeletedAt(),
            ];
        }
        return $this->json($res);
    }

    /**
     * @Route("/products", name="products_create", methods={"POST"})
     */
    public function create(Request $request): Response
    {
        $product = new Products();
        $param = json_decode($request->getContent(),true);

        $product->setProductName($param['product_name']);
        $product->setDescription($param['description']);
        $product->setPrice($param['price']);
        $product->setCreatedAt($this->nowDate);
        $create = $this->getDoctrine()->getManager();
        $create->persist($product);
        $create->flush();

        return $this->json('Successfully');

    }
    /**
     * @Route("/products/{id}", name="products_update", methods={"PUT"})
     */
    public function update(Request $request, $id): Response{

        $data = $this->getDoctrine()->getRepository(Products::class)->find($id);
        $param = json_decode($request->getContent(),true);

        $data->setProductName($param['product_name']);
        $data->setDescription($param['description']);
        $data->setPrice($param['price']);
        $data->setUpdatedAt($this->nowDate);
        $update = $this->getDoctrine()->getManager();
        $update->persist($data);
        $update->flush();

        return $this->json('Successfully');
    }
    /**
     * @Route("/products/{id}", name="product_delete", methods={"DELETE"})
     */
    public function delete(Request $request, $id): Response
    {
        $data = $this->getDoctrine()->getRepository(Products::class)->find($id);

        $data->setDeletedAt($this->nowDate);
        $update = $this->getDoctrine()->getManager();
        $update->persist($data);
        $update->flush();

        return $this->json('Successfully');
    }
}
