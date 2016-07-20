<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Form\ProductType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController extends FOSRestController
{
    /**
     * @ApiDoc(
     *   resource = true,
     *   description = "Return the overall product list",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     */
    public function getProductsAction()
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Product');
        $products = $repository->findAll();

        $view = $this->view($products, 200);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   resource = true,
     *   description = "Return an product identified by id",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the product is not found"
     *   }
     * )
     */
    public function getProductAction(Product $product)
    {
        $view = $this->view($product, 200);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   resource = true,
     *   description = "Creates a new product from the submitted data.",
     *   statusCodes = {
     *     201 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     */
    public function postProductsAction(Request $request)
    {
        $product = new Product();
        $product->setUser($this->getUser());

        return $this->processForm($product, $request);
    }

    /**
     * @ApiDoc(
     *   resource = true,
     *   description = "Updates a product from the submitted data by ID.",
     *   statusCodes = {
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors",
     *     404 = "Returned when the product is not found or not owned by the user"
     *   }
     * )
     */
    public function putProductAction(Product $product, Request $request)
    {
        if ($product->getUser()->getId() !== $this->getUser()->getId()) {
            throw new NotFoundHttpException('Product not found');
        }

        return $this->processForm($product, $request, 'PUT');
    }

    /**
     * Process data from request to Product
     *
     * @param Product $product
     * @param Request $request
     * @param string $method
     * @return Response|static
     */
    private function processForm(Product $product, Request $request, string $method = 'POST')
    {
        $statusCode = $product->getId() ? 204 : 201;

        $form = $this->createForm(ProductType::class, $product, ['method' => $method]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            $response = new Response();
            $response->setStatusCode($statusCode);

            if (201 === $statusCode) {
                $response->headers->set('Location',
                    $this->generateUrl(
                        'get_product',
                        ['product' => $product->getId()],
                        true
                    )
                );
            }

            return $response;
        }

        return View::create($form, 400);
    }

    /**
     * @ApiDoc(
     *   resource = true,
     *   description = "Delete an product identified by id",
     *   statusCodes = {
     *     204 = "Returned when successful",
     *     404 = "Returned when the product is not found or not owned by the user"
     *   }
     * )
     */
    public function deleteProductAction(Product $product)
    {
        if ($product->getUser()->getId() !== $this->getUser()->getId()) {
            throw new NotFoundHttpException('Product not found');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($product);
        $em->flush();

        $response = new Response();
        $response->setStatusCode(204);
        return $response;
    }
}
