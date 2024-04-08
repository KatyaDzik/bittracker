<?php

namespace App\Controller\User;

use App\Entity\TorrentFile;
use App\Enum\TorrentFileStatusEnum;
use App\Form\Torrent\CreateTorrentFileFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class TorrentFileController extends AbstractController
{
    #[Route(path: '/torrent/file', name: 'torrent_file', methods: ['GET|POST'])]
    public function create(
        Request $request,
        SluggerInterface $slugger,
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
    ): Response
    {
        $torrent = new TorrentFile();
        $form = $this->createForm(CreateTorrentFileFormType::class, $torrent);
        $form->handleRequest($request);
        $params = [
            'form' => $form,
        ];

        if ($form->isSubmitted() && $form->isValid()) {
            $torrent = $form->getData();
            $torrent->setAuthor($this->getUser());
            $torrent->setStatus(TorrentFileStatusEnum::Unverified->name);
            $torrentFile = $form->get('torrentFile')->getData();

            if($torrentFile) {
                $originalFilename = pathinfo($torrentFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$torrentFile->guessExtension();

                $torrentFile->move(
                    $this->getParameter('avatars_directory'),
                    $newFilename
                );

                $torrent->setFile($newFilename);
            }

            $entityManager->persist($torrent);
            $entityManager->flush();

            $params['success_msg'] = $translator->trans('File uploaded successfully');
        }

        return $this->render('create_torrent.html.twig', $params);
    }
}