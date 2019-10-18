<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Entity\Brand;
use App\Entity\Images;
use App\Entity\Setting;
use App\Form\UrunType;
use function Sodium\add;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


/**
 * Controller used to manage blog contents in the backend.
 *
 * Please note that the application backend is developed manually for learning
 * purposes. However, in your real Symfony application you should use any of the
 * existing bundles that let you generate ready-to-use backends without effort.
 *
 * See http://knpbundles.com/keyword/admin
 *
 * @Route("/etic")
 *
 */
class EticController extends Controller {

    private $TempDir = 'etic/';

    /**
     * @Route("/", name="etic_index")
     */
    public function index(){
        return $this->redirectToRoute('etic_anasayfa', array(), 301);
    }

    /**
     * @Route("/anasayfa", name="etic_anasayfa")
     */
    public function anasayfa(){
        $data = array();
        $product = $this->getDoctrine()->getRepository(Product::class)->findAll();
        $data[] = array('name' => 'Ürün','count' => count($product),'icon' => 'icon-people text-info');

        $category = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $data[] = array('name' => 'Kategori','count' => count($category),'icon' => 'ti-home text-primary');

        $brand = $this->getDoctrine()->getRepository(Brand::class)->findAll();
        $data[] = array('name' => 'Marka','count' => count($brand),'icon' => 'icon-wallet text-danger');

        return $this->render($this->TempDir.'index.html.twig',array('data' => $data));
    }

    /**
     * @Route("/urunekle", name="etic_urun_ekle")
     */
    public function urunEkle(Request $request){
        $product = new Product();

        $form = $this->createForm(UrunType::class,$product);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $file = $request->files->get('urun')['image'];
                $uploads_dir = $this->getParameter('uploads_dir');
                $filename = $product->getName().'_'.md5(uniqid()) . '.'.$file->guessExtension();
                $file->move($uploads_dir, $filename);
                $em = $this->getDoctrine()->getManager();
                $img = new Images();
                $img->setProduct($product);
                $img->setName($filename);
                $em->persist($img);
                $em->flush();

                $product = $form->getData();
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($product);
                $entityManager->flush();
                return $this->redirectToRoute('etic_urun_listesi');
            }

        return $this->render($this->TempDir.'productEkle.html.twig', array('form' => $form->createView(),'title' => 'Ürün Ekle'));
    }

    /**
     * @Route("/urunduzenle/{id}", requirements={"id": "\d+"}, name="etic_urun_duzenle")
     */
    public function urunDuzenle(Request $request,$id){
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        $uploads_dir = $this->getParameter('uploads_dir');
        $image_x = $product->getImages();
        if(count($image_x) > 0) $image = $image_x[0]->getName();

        $form = $this->createForm(UrunType::class,$product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $request->files->get('urun')['image'];

            if((is_array($file) or is_object($file))):
                if(!empty($image) and file_exists($uploads_dir.'/'.$image)) unlink($uploads_dir.'/'.$image);
                $filename = $product->getName().'_'.md5(uniqid()) . '.'.$file->guessExtension();
                $file->move($uploads_dir, $filename);
                $em = $this->getDoctrine()->getManager();
                if(count($image_x) > 0) $img = $this->getDoctrine()->getRepository(Images::class)->find($image_x[0]->getId());
                else $img = new Images();
                $img->setProduct($product);
                $img->setName($filename);
                $em->persist($img);
                $em->flush();
            endif;

            $product = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();
            return $this->redirectToRoute('etic_urun_listesi');
        }

        $r_data = array('form' => $form->createView(),'title' => 'Ürün Düzenle');
        if(isset($image)):
            $r_data['image'] = '../uploads/'.$image;
        endif;
        return $this->render($this->TempDir.'productEkle.html.twig', $r_data);
    }


    /**
     * @Route("/urunsil/{id}", requirements={"id": "\d+"}, name="etic_urun_sil")
     */
    public function urunSil(Request $request,$id){
        $em = $this->getDoctrine()->getManager();
        $pro = $em->getRepository(Product::class)->find($id);
        $em->remove($pro);
        $em->flush();
        return $this->redirectToRoute('etic_urun_listesi');
    }
    /**
     * @Route("/urunlistesi", name="etic_urun_listesi")
     */
    public function urunListesi(){
        $products = $this->getDoctrine()->getRepository(Product::class )->findAll();
        return $this->render($this->TempDir.'productsList.html.twig', array('products' => $products,'title' => 'Ürün Listesi'));
    }

    /**
     * @Route("/urunlistesi/{type}/{id}", requirements={"id": "\d+"}, name="etic_kategori_urun_listesi")
     */
    public function kategoriUrunListesi($type,$id){
        if($type == 'marka') $obj = Brand::class;
        else $obj = Category::class;
        $category = $this->getDoctrine()->getRepository($obj)->find($id);
        $products = $category->getProducts();
        return $this->render($this->TempDir.'productsList.html.twig', array('products' => $products,'title' => $category->getName()));
    }

    /**
     * @Route("/urun/{id}", requirements={"id": "\d+"}, name="etic_urun_detay")
     */
    public function urunDetay($id){
        $product = $this->getDoctrine()->getRepository(Product::class )->find($id);
        return $this->render($this->TempDir.'productDetay.html.twig', array('product' => $product,'title' => $product->getName()));
    }

    /**
     * @Route("/kategoriekle", name="etic_kategori_ekle")
     */
    public function kategoriEkle(Request $request){
        $category = new Category();

        $form = $this->createFormBuilder($category)
            ->add('name',TextType::class, array('label' => 'Adı', 'attr' => array('class' => 'form-control') ))
            ->add('Kaydet',SubmitType::class, array('label' => 'Kategori Oluştur', 'attr' => array('class' => 'btn btn-outline-success mt-3')))
            ->getForm();


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();
            return $this->redirectToRoute('etic_kategori_listesi');
        }

        return $this->render($this->TempDir.'template/form.html.twig', array('form' => $form->createView(),'title' => 'Kategori Ekle'));
    }

    /**
     * @Route("/kategoriduzenle/{id}", requirements={"id": "\d+"}, name="etic_kategori_duzenle")
     */
    public function kategoriDuzenle(Request $request,$id){
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);

        $form = $this->createFormBuilder($category)
            ->add('name',TextType::class, array('label' => 'Adı', 'attr' => array('class' => 'form-control') ))
            ->add('Kaydet',SubmitType::class, array('label' => 'Kategori Oluştur', 'attr' => array('class' => 'btn btn-outline-success mt-3')))
            ->getForm();


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();
            return $this->redirectToRoute('etic_kategori_listesi');
        }

        return $this->render($this->TempDir.'template/form.html.twig', array('form' => $form->createView(),'title' => 'Kategori Düzenle'));
    }


    /**
     * @Route("/kategorisil/{id}", requirements={"id": "\d+"}, name="etic_kategori_sil")
     */
    public function kategoriSil(Request $request,$id){
        $em = $this->getDoctrine()->getManager();
        $cat = $em->getRepository(Category::class)->find($id);
        $em->remove($cat);
        $em->flush();
        return $this->redirectToRoute('etic_kategori_listesi');
    }

    /**
     * @Route("/kategorilistesi", name="etic_kategori_listesi")
     */
    public function kategoriListesi(){
        $cats = $this->getDoctrine()->getRepository(Category::class )->findAll();
        return $this->render($this->TempDir.'categoryList.html.twig', array('cats' => $cats,'title' => 'Kategori Listesi'));
    }

    /* helüs */
    /**
     * @Route("/markaekle", name="etic_marka_ekle")
     */
    public function markaEkle(Request $request){
        $brand = new Brand();

        $form = $this->createFormBuilder($brand)
            ->add('name',TextType::class, array('label' => 'Adı', 'attr' => array('class' => 'form-control') ))
            ->add('Kaydet',SubmitType::class, array('label' => 'Marka Ekle', 'attr' => array('class' => 'btn btn-outline-success mt-3')))
            ->getForm();


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brand = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($brand);
            $entityManager->flush();
            return $this->redirectToRoute('etic_marka_listesi');
        }

        return $this->render($this->TempDir.'template/form.html.twig', array('form' => $form->createView(),'title' => 'Marka Ekle'));
    }

    /**
     * @Route("/markaduzenle/{id}", requirements={"id": "\d+"}, name="etic_marka_duzenle")
     */
    public function markaDuzenle(Request $request,$id){
        $brand = $this->getDoctrine()->getRepository(Brand::class)->find($id);

        $form = $this->createFormBuilder($brand)
            ->add('name',TextType::class, array('label' => 'Adı', 'attr' => array('class' => 'form-control') ))
            ->add('Kaydet',SubmitType::class, array('label' => 'Marka Düzenle', 'attr' => array('class' => 'btn btn-outline-success mt-3')))
            ->getForm();


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brand = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($brand);
            $entityManager->flush();
            return $this->redirectToRoute('etic_marka_listesi');
        }

        return $this->render($this->TempDir.'template/form.html.twig', array('form' => $form->createView(),'title' => 'Marka Düzenle'));
    }


    /**
     * @Route("/markasil/{id}", requirements={"id": "\d+"}, name="etic_marka_sil")
     */
    public function markaSil(Request $request,$id){
        $em = $this->getDoctrine()->getManager();
        $brand = $em->getRepository(Brand::class)->find($id);
        $em->remove($brand);
        $em->flush();
        return $this->redirectToRoute('etic_marka_listesi');
    }

    /**
     * @Route("/markalistesi", name="etic_marka_listesi")
     */
    public function markaListesi(){
        $brand  = $this->getDoctrine()->getRepository(Brand::class )->findAll();
        return $this->render($this->TempDir.'markaList.html.twig', array('cats' => $brand,'title' => 'Marka Listesi'));
    }


    /**
     * @Route("/ayarlar", name="etic_ayarlar")
     */
    public function ayarDuzenle(Request $request){
        $brand = $this->getDoctrine()->getRepository(Setting::class)->find(1);

        $form = $this->createFormBuilder($brand)
            ->add('company_name',TextType::class, array('label' => 'Ünvan', 'attr' => array('class' => 'form-control mb-4') ))
            ->add('address',TextareaType::class, array('required' => false,'label' => 'Adres', 'attr' => array('class' => 'form-control mb-4') ))
            ->add('phone',TextType::class, array('required' => false,'label' => 'Telefon', 'attr' => array('class' => 'form-control mb-4','data-mask' => '0 (999) 999 99 99') ))
            ->add('gsm',TextType::class, array('required' => false,'label' => 'GSM', 'attr' => array('class' => 'form-control mb-4','data-mask' => '0 (999) 999 99 99') ))
            ->add('fax',TextType::class, array('required' => false,'label' => 'Fax', 'attr' => array('class' => 'form-control mb-4','data-mask' => '0 (999) 999 99 99') ))
            ->add('explanation',TextareaType::class, array('required' => false,'label' => 'Açıklama', 'attr' => array('class' => 'form-control mb-4') ))
            ->add('Kaydet',SubmitType::class, array('label' => 'Kaydet', 'attr' => array('class' => 'btn btn-outline-success waves-effect')))
            ->getForm();


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brand = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($brand);
            $entityManager->flush();
            return $this->redirectToRoute('etic_ayarlar');
        }

        return $this->render($this->TempDir.'template/form.html.twig', array('form' => $form->createView(),'title' => 'Ayarlar'));
    }


}