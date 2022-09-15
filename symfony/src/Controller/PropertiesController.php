<?php

namespace App\Controller;

use App\Entity\Products;
use App\Entity\Properties;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PropertiesController extends AbstractController
{
    public function __construct()
    {
        $this->nowDate = new \DateTime();
    }

    /**
     * @Route("/api/properties", name="properties", methods={"GET"})
     */
    public function index(): JsonResponse
    {
        $propertiesRespository = $this->getDoctrine()->getRepository(Properties::class)->findAll();
        foreach ($propertiesRespository as $data){
            $res[] = [
                'id' => $data->getId(),
                'properties' => $data->getProperties(),
                'created_at' => $data->getCreatedAt(),
                'updated_at' => $data->getUpdatedAt(),
                'deleted_at' => $data->getDeletedAt(),
            ];
        }
        return $this->json($res);
    }

    /**
     * @Route("/api/properties", name="properties_create", methods={"POST"})
     */
    public function create(Request $request): Response
    {

        $properties = new Properties();
        $param = json_decode($request->getContent(),true);
            $properties->setProperties(json_encode($param['properties']));
            $properties->setProductId($param['product_id']);
            $properties->setCreatedAt($this->nowDate);
            $create = $this->getDoctrine()->getManager();
            $create->persist($properties);
            $create->flush();
            return $this->json('Successfully');
    }

    /**
     * @Route("/api/properties/{id}", name="properties_update", methods={"PUT"})
     */
    public function update(Request $request, $id): Response{

        $data = $this->getDoctrine()->getRepository(Properties::class)->find($id);
        $param = json_decode($request->getContent(),true);

        $data->setProperties(json_encode($param['properties']));
        $data->setUpdatedAt($this->nowDate);
        $update = $this->getDoctrine()->getManager();
        $update->persist($data);
        $update->flush();

        return $this->json('Successfully');
    }
    /**
     * @Route("/api/properties/{id}", name="properties_delete", methods={"DELETE"})
     */
    public function delete(Request $request, $id): Response
    {
        $data = $this->getDoctrine()->getRepository(Properties::class)->find($id);

        $data->setDeletedAt($this->nowDate);
        $update = $this->getDoctrine()->getManager();
        $update->persist($data);
        $update->flush();

        return $this->json('Successfully');
    }


}
