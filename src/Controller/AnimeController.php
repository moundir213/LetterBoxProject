<?php

namespace App\Controller;

use App\Repository\AnimeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AnimeController extends AbstractController
{
    private AnimeRepository $animeRepository;

    public function __construct(AnimeRepository $animeRepository)
    {
        $this->animeRepository = $animeRepository;
    }

    #[Route('/anime', name: 'app_anime')]
    public function showAllAnimes(): Response
    {
        $animes = $this->animeRepository->findAll();
        $animesData = [];
        foreach ($animes as $anime) {
            $animesData[] = [
                'id' => $anime->getId(),
                'title' => $anime->getTitle(),
                'picture' => $anime->getPicture(),
            ];
        }
        return $this->render('anime/all.html.twig', [
            'animes' => $animesData,
        ]);
    }

    #[Route('/anime/{id}', name: 'anime_show')]
    public function showAnime(int $id): Response
    {
        $anime = $this->animeRepository->find($id);

        if (!$anime) {
            throw $this->createNotFoundException('Anime non trouvé');
        }

        $animeData = [
            'id' => $anime->getId(),
            'title' => $anime->getTitle(),
            'picture' => $anime->getPicture(),
        ];

        return $this->render('anime/one.html.twig', [
            'anime' => $animeData,
        ]);
    }
}
