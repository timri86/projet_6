<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Tricks;
use App\Entity\Video;
use App\Form\CommentType;
use App\Form\TricksType;
use App\Repository\CommentRepository;
use App\Repository\ImageRepository;
use App\Repository\TricksRepository;
use App\Repository\VideoRepository;
use App\Services\ImageUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class TricksController extends AbstractController
{
    /**
     * @Route("/", name="tricks_index", methods={"GET"})
     */
    public function index(TricksRepository $tricksRepository): Response
    {
        return $this->render('tricks/index.html.twig', [
            'tricks' => $tricksRepository->findAll(),
        ]);
    }

    /**
     * @Route("/member/tricks/new", name="tricks_new", methods={"GET","POST"})
     */
    public function new(Request $request, ImageUploadService $imageUploadService): Response
    {
        $user=$this->getuser();
        $trick = new Tricks();
        $form = $this->createForm(TricksType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();



            foreach ($trick->getImage() as $image)
                $filename=$trick->getName();
            {
                $imageUploadService->upload($trick, $image);
                $image->setSource($filename);
                $image->setAlternatif($trick->getName());

            }
            foreach ($trick->getVideo() as $video)
            {
                $entityManager->persist($video);
            }
            $trick->setUser($user);
            $entityManager->persist($trick);
            $entityManager->flush();


            return $this->redirectToRoute('tricks_index');
        }

        return $this->render('tricks/new.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="tricks_show", methods={"GET","POST"})
     * @param Tricks $trick
     * @param Request $request
     */
    public function show($id,Tricks $trick, Request $request, TricksRepository $repository): Response
    {

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if($this->getUser()){
          $user=$this->getUser();
        }


        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setTrick($trick);
            if($user){
                $comment->setUser($user);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('tricks_show', ['id'=>$trick->getId()]);
        }

        $trick=$repository->showOneTrick($id);



        return $this->render('tricks/show.html.twig', [
            'trick' => $trick,
            'form'=>$form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="tricks_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Tricks $trick, string $oldImagePath): Response
    {
        $oldImage = new ArrayCollection();
        foreach ($trick->getImage() as $image) {
            $oldImage->add($image);
        }

        $form = $this->createForm(TricksType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $isImage=false;
            $entityManager = $this->getDoctrine()->getManager();
            foreach ($trick->getImage() as $image){
                if($image->getSource()!=null){
                    $isImage=true;
                }
            }
            if ($isImage){
                foreach ($trick->getImage() as $image)
                {
                    $file=$image->getSource();
                    $filename=$trick->getName();
                    $filename= str_replace(' ', '', $filename);
                    $unwanted_array = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
                        'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
                        'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
                        'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
                        'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
                    $filename = strtr( $filename, $unwanted_array );
                    $filename=$filename."_".md5(uniqid()).".".$file->guessExtension();
                    if($file){
                        try {
                            $file->move(
                                $this->getParameter('images_directory'),
                                $filename
                            );
                        } catch (FileException $e) {
                            // ... handle exception if something happens during file upload
                        }

                    }
                    $image->setSource($filename);
                    $image->setAlternatif($trick->getName());
                }
            }else{
                $i=1;
                foreach ($trick->getImage() as $image){
                    $source = $request->request->get($i);
                    $image->setSource($source);
                    $image->setAlternatif($trick->getName());
                    $i++;

                }

            }




            foreach ($trick->getVideo() as $video)
            {
                $entityManager->persist($video);
            }
            $entityManager->persist($trick);

            $entityManager->flush();


            return $this->redirectToRoute('tricks_index');
        }

        return $this->render('tricks/edit.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="tricks_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Tricks $trick): Response
    {
        if ($this->isCsrfTokenValid('delete'.$trick->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($trick);
            $entityManager->flush();
        }

        return $this->redirectToRoute('tricks_index');
    }
}
