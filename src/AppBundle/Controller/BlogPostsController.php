<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BlogPost;
use AppBundle\Entity\Repository\BlogPostRepository;
use AppBundle\Form\Type\BlogPostType;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BlogPostsController
 * @package AppBundle\Controller
 *
 * @RouteResource("post")
 */
class BlogPostsController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Gets an individual Blog Post
     *
     * @param int $id
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @Operation(
     *     tags={""},
     *     summary="Gets an individual Blog Post",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Return when not found"
     *     )
     * )
     *
     */
    public function getAction(int $id)
    {
        $blogPost = $this->getBlogPostRepository()->createFindOneByIdQuery($id)->getSingleResult();

        if ($blogPost === null) {
            return new View(null, Response::HTTP_NOT_FOUND);
        }

        return $blogPost;
    }

    /**
     * @return BlogPostRepository
     */
    private function getBlogPostRepository()
    {
        return $this->get('crv.doctrine_entity_repository.blog_post');
    }

    /**
     * Gets a collection of BlogPosts
     *
     * @return array
     *
     * @Operation(
     *     tags={""},
     *     summary="Gets a collection of BlogPosts",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Return when not found"
     *     )
     * )
     *
     */
    public function cgetAction()
    {
        return $this->getBlogPostRepository()->createFindAllQuery()->getResult();
    }

    /**
     * @param Request $request
     * @return View|\Symfony\Component\Form\Form
     *
     * @Operation(
     *     tags={""},
     *     summary="Create a new post",
     *     @SWG\Parameter(
     *         name="blog_post",
     *         description="json post request object",
     *         required=true,
     *         in="body",
     *         @SWG\Schema(ref=@Model(type=AppBundle\Form\Type\BlogPostType::class))
     *     ),
     *     @SWG\Response(
     *         response="201",
     *         description="Returned when a new BlogPost has been successful created"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Return when not found"
     *     )
     * )
     *
     */
    public function postAction(Request $request)
    {
        $form = $this->createForm(BlogPostType::class, null, [
            'csrf_protection' => false,
        ]);

        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $form;
        }

        /**
         * @var $blogPost BlogPost
         */
        $blogPost = $form->getData();

        $em = $this->getDoctrine()->getManager();
        $em->persist($blogPost);
        $em->flush();

        $routeOptions = [
            'id' => $blogPost->getId(),
            '_format' => $request->get('_format'),
        ];

        return $this->routeRedirectView('get_post', $routeOptions, Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @param int     $id
     * @return View|\Symfony\Component\Form\Form
     *
     * @Operation(
     *     tags={""},
     *     summary="",
     *     @SWG\Parameter(
     *         name="blog_post",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="object (BlogPostType)")
     *     ),
     *     @SWG\Response(
     *         response="204",
     *         description="Returned when an existing BlogPost has been successful updated"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Return when errors"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Return when not found"
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Returned on any other error"
     *     )
     * )
     *
     */
    public function putAction(Request $request, int $id)
    {
        /**
         * @var $blogPost BlogPost
         */
        $blogPost = $this->getBlogPostRepository()->find($id);

        if ($blogPost === null) {
            return new View(null, Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(BlogPostType::class, $blogPost, [
            'csrf_protection' => false,
        ]);

        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $form;
        }

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        $routeOptions = [
            'id' => $blogPost->getId(),
            '_format' => $request->get('_format'),
        ];

        return $this->routeRedirectView('get_post', $routeOptions, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param Request $request
     * @param int     $id
     * @return View|\Symfony\Component\Form\Form
     *
     * @Operation(
     *     tags={""},
     *     summary="",
     *     @SWG\Parameter(
     *         name="blog_post",
     *         in="body",
     *         description="",
     *         required=false,
     *         @SWG\Schema(type="object (BlogPostType)")
     *     ),
     *     @SWG\Response(
     *         response="204",
     *         description="Returned when an existing BlogPost has been successful updated"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Return when errors"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Return when not found"
     *     )
     * )
     *
     */
    public function patchAction(Request $request, int $id)
    {
        /**
         * @var $blogPost BlogPost
         */
        $blogPost = $this->getBlogPostRepository()->find($id);

        if ($blogPost === null) {
            return new View(null, Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(BlogPostType::class, $blogPost, [
            'csrf_protection' => false,
        ]);

        $form->submit($request->request->all(), false);

        if (!$form->isValid()) {
            return $form;
        }

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        $routeOptions = [
            'id' => $blogPost->getId(),
            '_format' => $request->get('_format'),
        ];

        return $this->routeRedirectView('get_post', $routeOptions, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param int $id
     * @return View
     *
     * @Operation(
     *     tags={""},
     *     summary="",
     *     @SWG\Response(
     *         response="204",
     *         description="Returned when an existing BlogPost has been successful deleted"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Return when not found"
     *     )
     * )
     *
     */
    public function deleteAction(int $id)
    {
        /**
         * @var $blogPost BlogPost
         */
        $blogPost = $this->getBlogPostRepository()->find($id);

        if ($blogPost === null) {
            return new View(null, Response::HTTP_NOT_FOUND);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($blogPost);
        $em->flush();

        return new View(null, Response::HTTP_NO_CONTENT);
    }
}
