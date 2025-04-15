<?php

namespace App\Controller;

use App\Repository\SongRepository;
use App\Service\UtilsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SongsController extends AbstractController
{
    #[Route('/', name: 'app_songs')]
    public function allSongs(SongRepository $songs, UtilsService $utilsService): Response
    {
        $allSongs = $songs->findAllSongs();
        return $this->render('songs/allSongs.html.twig', [
            'allSongs' => $allSongs,
            'utilsService' => $utilsService
        ]);
    }

    #[Route('/song/{song_id}', name: 'app_song_details')]
    public function songDetails(SongRepository $song, UtilsService $utilsService, string $song_id): Response
    {
        $songDetails = $song->findSongById($song_id);
        return $this->render('songs/songDetails.html.twig', [
            'song' => $songDetails,
            'utilsService' => $utilsService
        ]);
    }
}
