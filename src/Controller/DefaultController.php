<?php

    namespace App\Controller;

    use App\Entity\Product;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;

    class DefaultController extends Controller {
        private $TempDir = 'default/';

        /**
         * @Route("/", name="homepage")
         */
        public function index(){
            $arr = $this->getDoctrine()->getRepository(Product::class)->findAll();
            return $this->render($this->TempDir.'index.html.twig',array('product' => $arr));
        }

        /**
         * @Route("/getJson", name="products_json")
         */
        public function getJson(){
            $arr = $this->getDoctrine()->getRepository(Product::class)->findAll();
            $tmp = array();
            foreach ($arr as $a):
                $tmp[] = array(
                    'id' => $a->getId(),
                    'name' => $a->getName(),
                    'price' => $a->getPrice(),
                    'quantity' => $a->getQuantity(),
                    'description' => $a->getDescription(),
                    'discount' => $a->getDescription(),
                    'category' => $a->getCategory()->getName(),
                    'category_id' => $a->getCategory()->getId(),
                    'brand' => $a->getBrand()->getName(),
                    'brand_id' => $a->getBrand()->getId()
                );
            endforeach;
            return $this->json($tmp);
            //return $this->render($this->TempDir.'index.html.twig',array('product' => $arr));
        }

    }