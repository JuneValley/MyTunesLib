<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Entity\Song;
use App\Model\GenreEnum;
use App\Repository\ArtistRepository;
use App\Repository\SongRepository;
use App\Repository\UserRepository;
use App\Service\UtilsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\ORM\EntityManagerInterface;

final class SongsController extends AbstractController
{
    /**
     * Route that fetches all songs from database and redirect to the main app page.
     */
    #[Route('/', name: 'app_songs')]
    public function allSongs(SongRepository $songRepository, UtilsService $utilsService): Response
    {
        $allSongs = $songRepository->findAllSongs();
        return $this->render('songs/allSongs.html.twig', [
            'allSongs' => $allSongs,
            'utilsService' => $utilsService
        ]);
    }

    /**
     * Route that fetches the currently connected user and its corresponding playlist, then redirect to the playlist page.
     * If no user is connected, redirect to the main app page.
     */
    #[Route('/playlist', name: 'app_playlist')]
    public function playlist(UserRepository $userRepository, UtilsService $utilsService, Request $request): Response
    {
        $connectedUser = $userRepository->findUserByUsername($request->getSession()->get('user')['username']);

        if ($connectedUser) {
            $playlist = $connectedUser->getPlaylist();
        } else {
            return $this->redirectToRoute('app_songs');
        }

        return $this->render('songs/playlist.html.twig', [
            'playlist' => $playlist,
            'utilsService' => $utilsService
        ]);
    }

    /**
     * Route that adds a song to the playlist of the currently connected user via its id.
     * If no user is connected, redirect to the main app page.
     */
    #[Route('/addToPlaylist/{song_id}', name: 'app_add_to_playlist')]
    public function addToPlaylist(SongRepository $songRepository, UserRepository $userRepository, Request $request, string $song_id): Response
    {
        if($request->getSession()->get('user') !== null){
            $connectedUser = $userRepository->findUserByUsername($request->getSession()->get('user')['username']);
            $songToAdd = $songRepository->findSongById($song_id);
            if($songToAdd){
                $userRepository->addToPlaylist($connectedUser->getId(), $songToAdd);
            }
        } else {
            return $this->redirectToRoute('app_songs');
        }

        return $this->redirectToRoute('app_playlist');
    }

    /**
     * Route that removes a song from the playlist of the currently connected user via its id.
     * If no user is connected, redirect to the main app page.
     */
    #[Route('/removeFromPlaylist/{song_id}', name: 'app_remove_from_playlist')]
    public function removeFromPlaylist(SongRepository $songRepository, UserRepository $userRepository, Request $request, string $song_id): Response
    {
        if($request->getSession()->get('user') !== null){
            $connectedUser = $userRepository->findUserByUsername($request->getSession()->get('user')['username']);
            $songToRemove = $songRepository->findSongById($song_id);
            if($songToRemove){
                $userRepository->removeFromPlaylist($connectedUser->getId(), $songToRemove);
            }
        } else {
            return $this->redirectToRoute('app_songs');
        }

        return $this->redirectToRoute('app_playlist');
    }

    /**
     * Route that redirect to the corresponding details of a song via its id.
     * If no corresponding song is found, redirect to the main app page.
     */
    #[Route('/song/{song_id}', name: 'app_song_details')]
    public function songDetails(SongRepository $songRepository, UtilsService $utilsService, string $song_id): Response
    {
        $songDetails = $songRepository->findSongById($song_id);

        if(!$songDetails){
            return $this->redirectToRoute('app_songs');
        }

        return $this->render('songs/songDetails.html.twig', [
            'song' => $songDetails,
            'utilsService' => $utilsService
        ]);
    }

    /**
     * Route that redirect to the page for creation of a new song and generates the form to fill up. Only accessible to administrators.
     * Handles the creation of the song and corresponding artists in the database.
     * If no user is connected or the connected user is not an admin, redirect to the main app page.
     */
    #[Route('/newSong', name: 'app_new_song')]
    public function newSong(SongRepository $songRepository, ArtistRepository $artistRepository, UtilsService $utilsService, Request $request): Response
    {
        //ADMIN ONLY GUARD
        if ($request->getSession()->get('user') == null || $request->getSession()->get('user')['role'][0] !== 'ADMIN') {
            return $this->redirectToRoute('app_songs');
        }

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
            ->add('Duree_minutes', IntegerType::class)
            ->add('Duree_secondes', IntegerType::class)
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

    /**
     * Route that redirect to the page for edition of an existing song via its id and generates the form with pre-filled fields.
     * Only accessible to administrators.
     * Handles the edition of the song and corresponding artists in the database.
     * If no user is connected or the connected user is not an admin or no corresponding song is found, redirect to the main app page.
     */
    #[Route('/editSong/{song_id}', name: 'app_edit_song')]
    public function editSong(SongRepository $songRepository, ArtistRepository $artistRepository, UtilsService $utilsService, Request $request, EntityManagerInterface $entityManager, string $song_id): Response
    {
        //ADMIN ONLY GUARD
        if ($request->getSession()->get('user') == null || $request->getSession()->get('user')['role'][0] !== 'ADMIN') {
            return $this->redirectToRoute('app_songs');
        }

        $songToEdit = $songRepository->findSongById($song_id);
        if(!$songToEdit){
            return $this->redirectToRoute('app_songs');
        }
        
        $rawArtists = $songToEdit->getArtists()->toArray();
        $artistsNames = '';
        foreach ($rawArtists as $artist) {
            if ($artistsNames != '') {
                $artistsNames = $artistsNames . ',' . $artist->getName();
            } else {
                $artistsNames = $artistsNames . $artist->getName();
            }
        }
        $formattedDuration = explode(',', $utilsService->formatDurationToMinutesSeconds($songToEdit->getDuration(), true));

        $form = $this->createFormBuilder(null, ['method' => 'POST'])
            ->add('Titre', TextType::class, ['data' => $songToEdit->getTitle()])
            ->add('Artistes', TextType::class, ['data' => $artistsNames])
            ->add('Genre', ChoiceType::class, [
                'choices' => [
                    'Pop' => GenreEnum::POP,
                    'Rock' => GenreEnum::ROCK,
                    'EDM' => GenreEnum::EDM,
                    'Rap' => GenreEnum::RAP,
                    'Jazz' => GenreEnum::JAZZ,
                    'Classique' => GenreEnum::CLASSIC,
                    'Autre' => GenreEnum::OTHER,
                ],
                'data' => $songToEdit->getGenre()
            ])
            ->add('Duree_minutes', IntegerType::class, ['data' => $formattedDuration[0]])
            ->add('Duree_secondes', IntegerType::class, ['data' => $formattedDuration[1]])
            ->add('Date_de_sortie', DateType::class, ['data' => $songToEdit->getReleaseDate()])
            ->add('Url_de_la_cover', UrlType::class, ['required' => false, 'data' => $songToEdit->getUrlCover()])
            ->add('Url_Youtube', UrlType::class, ['required' => false, 'data' => $songToEdit->getYtbLink()])
            ->add('update_song', SubmitType::class, ['label' => 'Modifier le titre ✏️'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $songToEdit->setTitle($form->get('Titre')->getData());
            $songToEdit->setGenre($form->get('Genre')->getData());
            $duration = $utilsService->formatDurationToSeconds($form->get('Duree_minutes')->getData(), $form->get('Duree_secondes')->getData());
            $songToEdit->setDuration($duration);
            $songToEdit->setReleaseDate($form->get('Date_de_sortie')->getData());
            if ($form->get('Url_de_la_cover')->getData()) {
                $songToEdit->setUrlCover($form->get('Url_de_la_cover')->getData());
            }
            if ($form->get('Url_Youtube')->getData()) {
                $songToEdit->setYtbLink($form->get('Url_Youtube')->getData());
            }

            $artists = explode(',', $form->get('Artistes')->getData());

            foreach ($artists as $artist) {
                if ($artistRepository->findArtistByName($artist)) {
                    $songToEdit->addArtist($artistRepository->findArtistByName($artist));
                } else {
                    $newArtist = new Artist();
                    $newArtist->setName($artist);
                    $artistRepository->newArtist($newArtist);
                    $songToEdit->addArtist($artistRepository->findArtistByName($artist));
                }
            }

            $entityManager->flush();
            return $this->redirectToRoute('app_song_details', ['song_id' => $song_id]);
        }

        return $this->render('songs/editSong.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Route that deletes a song via its id. Only accessible to administrators.
     * Handles the deletion of the song in the database.
     * If no user is connected or the connected user is not an admin, redirect to the main app page.
     */
    #[Route('/deleteSong/{song_id}', name: 'app_delete_song')]
    public function deleteSong(SongRepository $songRepository, Request $request, string $song_id): Response
    {
        //ADMIN ONLY GUARD
        if ($request->getSession()->get('user') == null || $request->getSession()->get('user')['role'][0] !== 'ADMIN') {
            return $this->redirectToRoute('app_songs');
        }

        $songRepository->deleteSongById($song_id);

        return $this->redirectToRoute('app_songs');
    }
}
