<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProductController extends AbstractController
{

	/**
     * @Route("/product/new")
    */
    public function new(Request $request)
    {

    	
        // creates a task and gives it some dummy data for this example
        $product = new Product();
        $product->setCode('K123');
        $product->setName('Keyboard');
        $product->setPrice(1999);
        $product->setDescription('Ergonomic and stylish!');

        $form = $this->createFormBuilder($product)
            ->add('code', TextType::class)
            ->add('name', TextType::class)
            ->add('price', IntegerType::class)
            ->add('description', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Create Product'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $task = $form->getData();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('app_lucky_number');
        }

        return $this->render('default/new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/product", name="product")
     */
    public function index()
    {
        // you can fetch the EntityManager via $this->getDoctrine()
        // or you can add an argument to your action: index(EntityManagerInterface $entityManager)
        $entityManager = $this->getDoctrine()->getManager();

        $product = new Product();
        $product->setCode('K123');
        $product->setName('Keyboard');
        $product->setPrice(1999);
        $product->setDescription('Ergonomic and stylish!');

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($product);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new product with id '.$product->getId());
    }

    /**
	 * @Route("/product/{id}", name="product_show")
	 */
	public function show($id)
	{
	    $product = $this->getDoctrine()
	        ->getRepository(Product::class)
	        ->find($id);

	    if (!$product) {
	        throw $this->createNotFoundException(
	            'No product found for id '.$id
	        );
	    }

	    return new Response('Check out this great product: '.$product->getName());

	    // or render a template
	    // in the template, print things with {{ product.name }}
	    // return $this->render('product/show.html.twig', ['product' => $product]);
	}
}
