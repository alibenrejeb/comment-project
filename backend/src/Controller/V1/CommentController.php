<?php

namespace App\Controller\V1;

use App\Entity\Comment;
use App\Entity\CommentReply;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

## Comment's controller
#[Route(
        "/{version}",
        defaults: ["_format" => "json", "version" => "v1"],
        requirements: ["version" => "v1"]
    )
]
class CommentController extends AbstractController
{
    #[
        Route(
            '/comments/{pageId}',
            methods: ['GET'],
            name: 'get_comments',
            requirements: ["pageId" => "\d+"],
            defaults: ["pageId" => 0]
        )
    ]
    public function getComments(Request $request, CommentRepository $commentRepository, ?int $pageId): Response
    {
        $query = $request->query->all();
        $criterias = $pageId > 0 ? ['pageId' => $pageId] : [];
        return $this->json($commentRepository->findBy($criterias, $query['sort'], $query['limit'] ?? null), 200, [], ['groups' => ['get_comment']]);
    }

    #[
        Route(
            '/comments',
            methods: ['POST'],
            name: 'create_comment'
        )
    ]
    #[Security("is_granted('ROLE_FULL_USER')")]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): Response {
        $data = json_decode($request->getContent(), true);
        $comment = $serializer->denormalize($data, Comment::class);

        $errors = $validator->validate($comment, null, ['create_comment']);
        if (count($errors) > 0) {
            throw new BadRequestHttpException((string) $errors);
        }

        $comment->setAuthor($this->getUser());
        $em->persist($comment);
        $em->flush();
        return $this->json($comment, Response::HTTP_CREATED, [], ['groups' => ['get_comment']]);
    }

    #[
        Route(
            '/comments/{id}',
            methods: ['POST'],
            name: 'create_comment_reply',
            requirements: ["id" => "\d+"]
        )
    ]
    #[Security("is_granted('ROLE_FULL_USER')")]
    public function reply(
        Request $request,
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        Comment $comment
    ): Response {
        $data = json_decode($request->getContent(), true);
        $commentReply = $serializer->denormalize($data, CommentReply::class);
        $commentReply
            ->setAuthor($this->getUser())
            ->setComment($comment);
        $em->persist($commentReply);
        $em->flush();
        return $this->json($commentReply, Response::HTTP_CREATED, [], ['groups' => ['get_comment_reply']]);
    }

    #[
        Route(
            '/comments/{id}',
            methods: ['PATCH'],
            name: 'update_comment',
            requirements: ["id" => "\d+"]
        )
    ]
    #[Security("is_granted('ROLE_FULL_USER')")]
    public function update(
        Request $request,
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        Comment $comment
    ) : Response {
        if (!$this->isGranted('ROLE_SUPER_ADMIN') && $comment->getAuthor()->getId() !== $this->getUser()->getId()) {
            throw new AccessDeniedException('User is not permitted to do this operation.');
        }

        $serializer->deserialize(
            $request->getContent(),
            Comment::class,
            'json',
            ['object_to_populate' => $comment]
        );
        $em->persist($comment);
        $em->flush();

        return $this->json($comment, Response::HTTP_CREATED, [], ['groups' => ['get_comment']]);
    }
}
