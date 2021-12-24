<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Document;
use App\Entity\Note;
use App\Form\NoteType;
use App\Repository\NoteRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/')]
class NoteController extends AbstractController
{
    #[Route('/', name: 'note_index', methods: ['GET'])]
    public function index(Request $request, NoteRepository $noteRepository, PaginatorInterface $paginator): Response
    {
        $notes = $noteRepository->findAll();
        $notes = $paginator->paginate(
            $notes,
            $request->query->getInt('page', 1),
            10
        );
        
        return $this->render('note/index.html.twig', [
            'notes' => $notes,
        ]);
    }

    #[Route('/new', name: 'note_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $note = new Note();
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Collect the documents
            $documents = $form->get('documents')->getData();
            foreach ($documents as $document) {
                // Generate new name
                $file = md5(uniqid()) . '.' . $document->guessExtension();
                // Move the file to the directory
                $document->move(
                    $this->getParameter('documents_directory'),
                    $file
                );
                // Store the document name in the database
                $doc = new Document();
                $doc->setName($file);
                $note->addDocument($doc);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($note);
            $entityManager->flush();

            return $this->redirectToRoute('note_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('note/new.html.twig', [
            'note' => $note,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'note_show', methods: ['GET'])]
    public function show(Note $note): Response
    {
        return $this->render('note/show.html.twig', [
            'note' => $note,
        ]);
    }

    #[Route('/{id}/edit', name: 'note_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Note $note): Response
    {
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Collect the documents
            $documents = $form->get('documents')->getData();
            foreach ($documents as $document) {
                // Generate new name
                $file = md5(uniqid()) . '.' . $document->guessExtension();
                // Move the file to the directory
                $document->move(
                    $this->getParameter('documents_directory'),
                    $file
                );
                // Store the document name in the database
                $doc = new Document();
                $doc->setName($file);
                $note->addDocument($doc);
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('note_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('note/edit.html.twig', [
            'note' => $note,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'note_delete', methods: ['POST'])]
    public function delete(Request $request, Note $note): Response
    {
        if ($this->isCsrfTokenValid('delete'.$note->getId(), (string) $request->request->get('_token'))) {
            $documents = $note->getDocuments();
            if ($documents) {
                foreach ($documents as $document) {
                    $file = $this->getParameter('documents_directory') . '/' . $document->getName();
                    if (file_exists($file)) {
                        unlink($file);
                    }
                }
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($note);
            $entityManager->flush();
        }

        return $this->redirectToRoute('note_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/delete/document/{id}', name: 'note_delete_document', methods: ['DELETE'])]
    public function deleteDocument(Document $document, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        // Check if token is valid
        if ($this->isCsrfTokenValid('delete' . $document->getId(), $data['_token'])) {
            $name = $document->getName();
            // Delete the document
            unlink($this->getParameter('documents_directory') . '/' . $name);
            // Remove the entry from the database
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($document);
            $entityManager->flush();
            return new JsonResponse(['success' => true]);
        } else {
            return new JsonResponse(['error' => 'Invalid Token'], 400);
        }
    }
}
