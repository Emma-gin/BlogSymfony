<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Entity\User;
use App\Form\ArticlesType;
use App\Repository\ArticlesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;


/**
 * @Route("/")
 */
class ArticlesController extends AbstractController
{
    /**
     * @Route("/", name="articles_index", methods={"GET"})
     */
    public function index(Request $request, ArticlesRepository $articlesRepository, PaginatorInterface $paginator): Response
    {

        $donnees = $this->getDoctrine()->getRepository(Articles::class)->findBy(
            ['isPublished' => true],
            ['isPublished' => 'asc']
        );

        $articles = $paginator->paginate(
            $donnees,
            $request->query->getInt('page', 1),
            6,
        );

        return $this->render('articles/index.html.twig', ['articles' => $articles]);

    }

    /**
     * @Route("/article/new", name="articles_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = new Articles();
        $form = $this->createForm(ArticlesType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($article->getPicture() !== null) {
                
                $file = $form->get('picture')->getData();
                $fileName =  uniqid(). '.' .$file->guessExtension();

                try {
                    $file->move(
                        $this->getParameter('images_directory'), // Le dossier dans lequel le fichier va être charger
                        $fileName
                    );
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }

                $article->setPicture($fileName);
            }

            $article->setLastUpdateDate(new \DateTime());
            $article->setIsPublished(0);
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('articles_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('articles/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/article/{id}", name="articles_show", methods={"GET"})
     */
    public function show(Articles $article): Response
    {
        return $this->render('articles/show.html.twig', [
            'article' => $article,
        ]);
    }


    /**
     * @Route("/article/{id}/edit", name="articles_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Articles $article, EntityManagerInterface $entityManager): Response
    {
        // $oldPicture = $article->getPicture();

        $form = $this->createForm(ArticlesType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if($article->getPicture() !== null && $article->getPicture() !== $oldPicture){
                $file = $form->get('picture')->getData();
                $fileName = uniqid(). '.' .$file->guessExtension();

                $article->setPicture($fileName);
            }else{
                $article->setPicture($oldPicture);
            }

            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('articles_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('articles/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/article/{id}/delete", name="articles_delete", methods={"GET","POST"})
     */
    public function delete(Request $request, Articles $article, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin', [], Response::HTTP_SEE_OTHER);
    }


    /**
     * @Route("/article/{id}/publish", name="article_publish", methods={"GET","POST"})
     */
    public function publish(Request $request, Articles $article, EntityManagerInterface $entityManager): Response
    {
        
            $article->setIsPublished(1);
            $article->setLastUpdateDate(new \DateTime());
            $entityManager->flush();

        return $this->redirectToRoute('admin', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/admin/index", name="admin", methods={"GET","POST"})
     */
    public function admin(Request $request, PaginatorInterface $paginator) {
        $donneesArticles = $this->getDoctrine()->getRepository(Articles::class)->findBy(
            [],
            ['last_update_date' => 'DESC']
        );
        $articles = $paginator->paginate(
            $donneesArticles, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            9 // Nombre de résultats par page
        );

        $donneesUsers = $this->getDoctrine()->getRepository(User::class)->findAll();

        $users = $paginator->paginate(
            $donneesUsers, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            2 // Nombre de résultats par page
        );
        

        return $this->render('admin/index.html.twig', [
            'articles' => $articles,
            'users' => $users
        ]);
    }

}