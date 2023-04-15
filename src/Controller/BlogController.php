<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\BlogRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Form\BlogType;
use App\Form\SearchType;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Blog;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\SerializerInterface;
use Knp\Component\Pager\PaginatorInterface;

class BlogController extends AbstractController
{
    #[Route('/blog', name: 'app_blog')]
    public function blog(BlogRepository $repo,SerializerInterface $serializer,PaginatorInterface $paginator, Request $request): Response
    { 
        $blogsQuery = $repo->createQueryBuilder('b')->getQuery();
       
        $blogs = $paginator->paginate(
            $blogsQuery,
            $request->query->getInt('page', 1), 
            3 
        );

        return $this->render('blog/blog.html.twig',['blogs'=>$blogs]);
   
    }

    #[Route('/actualite/details/{id}', name: 'details_blog')]
    public function details(ManagerRegistry $doctrine,SerializerInterface $serializer,CategoryRepository $repo,BlogRepository $repo1,$id): Response
    {
        $em = $doctrine->getManager();
        $blog = $doctrine->getRepository(Blog::class)->find($id);
        $json = $serializer->serialize($blog, 'json',['groups'=>'blogs']);
        
        
        $categories = $repo->findAll();
        $blogs = $repo1->findAll();
        $json2 = $serializer->serialize($blogs, 'json',['groups'=>'blogs']);
            
        return $this->render('blog/details.html.twig', [
            'blog' => $blog,'categories' => $categories,'blogs'=>json_decode($json2)
        ]);
        //return new Response($json);
    }

    #[Route('/actualite', name: 'app_actualite')]
    public function actulite(BlogRepository $repo,SerializerInterface $serializer): Response
    { 
        $blogs = $repo->findAll();
        $json = $serializer->serialize($blogs, 'json',['groups'=>'blogs']);
        return $this->render('blog/actualite.html.twig',
        ['blogs'=>json_decode($json)
        ]);
    }

    #[Route('/blog/remove/{id}', name: 'remove_blog')]
    public function remove(ManagerRegistry $doctrine,SerializerInterface $serializer,$id): Response
    {
        $em = $doctrine->getManager();
        $blog = $doctrine->getRepository(Blog::class)->find($id);
        
            $em->remove($blog);
            $em->flush();
            $json = $serializer->serialize($blog, 'json',['groups'=>'blogs']);
            return new RedirectResponse('/blog?data=' . urlencode($json));  
            //return $this->redirectToRoute('app_blog');
        
    }



   #[Route('/blog/update/{id}', name: 'update_blog')]
    public function update(ManagerRegistry $doctrine,Request $request, SluggerInterface $slugger,SerializerInterface $serializer,$id): Response
    {
        $em = $doctrine->getManager();
        $blog = $doctrine->getRepository(Blog::class)->find($id);
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            // $em->persist($blog);
            $photo = $form->get('photo')->getData();

            if ($photo) {
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photo->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $photo->move(
                        $this->getParameter('photo_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                   
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $blog->setPhoto($newFilename);
            }
            
            $em->flush();
            
            $json = $serializer->serialize($blog, 'json',['groups'=>'blogs']);
            
            return new RedirectResponse('/blog?data=' . urlencode($json));  
        }
        return $this->renderForm('blog/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/blog/create', name: 'app_blog_create')]
    public function blogCreate(ManagerRegistry $doctrine,Request $request, SluggerInterface $slugger,SerializerInterface $serializer): Response
    {
        $em = $doctrine->getManager();
        $blog = new Blog();
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $photo = $form->get('photo')->getData();

            if ($photo) {
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photo->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $photo->move(
                        $this->getParameter('photo_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                   
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $blog->setPhoto($newFilename);
            }
            $em->persist($blog);
            $em->flush();
            $json = $serializer->serialize($blog, 'json',['groups'=>'blogs']);
            
            return new RedirectResponse('/blog?data=' . urlencode($json));
        }
        
        return $this->renderForm('blog/create.html.twig', [
            'form' => $form,
        ]);
    }


    #[Route('/blog/category/{id}', name: 'app_blog_category')]
    public function showBlogsByCategory($id): Response
    {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->find($id);

        $blogs = $this->getDoctrine()
            ->getRepository(Blog::class)
            ->findBy(['category' => $category]);

        return $this->render('blog/category.html.twig', [
            'category' => $category,
            'blogs' => $blogs,
        ]);
    }

    #[Route('/blog/download/{id}', name: 'app_blog_download')]
    public function download(Request $request, EntityManagerInterface $entityManager, int $id)
{
    $blog = $entityManager->getRepository(Blog::class)->find($id);
    // Create a new response object
    $response = new Response();

    // Set the content type and disposition headers
    $response->headers->set('Content-Type', 'text/plain');
    $response->headers->set('Content-Disposition', $response->headers->makeDisposition(
        ResponseHeaderBag::DISPOSITION_ATTACHMENT,
        'blog-' . $blog->getId() . '.txt'
    ));

    // Build the text to be downloaded
    $text = "Title: {$blog->getName()}\n";

    // Set the content of the response to the text to be downloaded
    $response->setContent($text);

    // Return the response
    return $response;
}
}
