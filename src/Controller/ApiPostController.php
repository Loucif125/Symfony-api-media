<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;


class ApiPostController extends AbstractController
{
    /**
     * @Route("/api/post", name="api_post", methods={"GET"})
     */

    public function index(PostRepository $postRepository)
    {
        $posts = $postRepository->findAll();

        return $this->json($posts, 200, [], ['groups'=> 'post:read']);
    }

    /**
     * @Route("/api/store", name="api_store", methods={"POST"})
     */
    public function store(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager){

        $json = $request->getContent();

        try {
            $post= $serializer->deserialize($json, Post::class,'json');
            $post->setCreatedAt(new \DateTime());
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->json($post, 201, [], ["groups"=> "post:read"]);
        }
        catch (NotEncodableValueException $e){
            return $this->json(["status" => 400, "message" => $e->getMessage()], 400);

        }


    }
}
