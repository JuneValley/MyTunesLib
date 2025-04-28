<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Entity\Song;
use App\Model\GenreEnum;
use App\Repository\ArtistRepository;
use App\Repository\SongRepository;
use App\Service\UtilsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

final class SongsController extends AbstractController
{
    #[Route('/', name: 'app_songs')]
    public function allSongs(SongRepository $songRepository, UtilsService $utilsService): Response
    {
        $allSongs = $songRepository->findAllSongs();
        return $this->render('songs/allSongs.html.twig', [
            'allSongs' => $allSongs,
            'utilsService' => $utilsService
        ]);
    }

    #[Route('/song/{song_id}', name: 'app_song_details')]
    public function songDetails(SongRepository $songRepository, UtilsService $utilsService, string $song_id): Response
    {
        $songDetails = $songRepository->findSongById($song_id);
        return $this->render('songs/songDetails.html.twig', [
            'song' => $songDetails,
            'utilsService' => $utilsService
        ]);
    }

    #[Route('/newSong', name: 'app_new_song')]
    public function newSong(SongRepository $songRepository, ArtistRepository $artistRepository, UtilsService $utilsService, Request $request): Response
    {
        $form = $this->createFormBuilder(null, ['method' => 'POST'])
            ->add('Titre', TextType::class)
            ->add('Artistes', TextType::class)
            ->add('Genre', ChoiceType::class, [
                'choices' => [
                    'Pop' => GenreEnum::POP,
                    'Rock' => GenreEnum::ROCK,
                    'EDM' => GenreEnum::EDM,
                    'Rap' => GenreEnum::RAP,
                    'Jazz' => GenreEnum::JAZZ,
                    'Classique' => GenreEnum::CLASSIC,
                    'Autre' => GenreEnum::OTHER,
                ]
            ])
            ->add('Duree_minutes', NumberType::class)
            ->add('Duree_secondes', NumberType::class)
            ->add('Date_de_sortie', DateType::class)
            ->add('Url_de_la_cover', UrlType::class, ['required' => false])
            ->add('Url_Youtube', UrlType::class, ['required' => false])
            ->add('new_song', SubmitType::class, ['label' => 'Ajouter un nouveau titre ♫'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $song = new Song();
            $song->setTitle($form->get('Titre')->getData());
            $song->setGenre($form->get('Genre')->getData());
            $duration = $utilsService->formatDurationToSeconds($form->get('Duree_minutes')->getData(), $form->get('Duree_secondes')->getData());
            $song->setDuration($duration);
            $song->setReleaseDate($form->get('Date_de_sortie')->getData());
            if ($form->get('Url_de_la_cover')->getData()) {
                $song->setUrlCover($form->get('Url_de_la_cover')->getData());
            }
            if ($form->get('Url_Youtube')->getData()) {
                $song->setYtbLink($form->get('Url_Youtube')->getData());
            }

            $artists = explode(',', $form->get('Artistes')->getData());

            foreach ($artists as $artist) {
                if ($artistRepository->findArtistByName($artist)) {
                    $song->addArtist($artistRepository->findArtistByName($artist));
                } else {
                    $newArtist = new Artist();
                    $newArtist->setName($artist);
                    $artistRepository->newArtist($newArtist);
                    $song->addArtist($artistRepository->findArtistByName($artist));
                }
            }

            $songRepository->newSong($song);
            return $this->redirectToRoute('app_songs');
        }

        return $this->render('songs/newSong.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/deleteSong/{song_id}', name: 'app_delete_song')]
    public function deleteSong(SongRepository $songRepository, string $song_id): Response
    {
        $songRepository->deleteSongById($song_id);

        return $this->redirectToRoute('app_songs');
    }
}
