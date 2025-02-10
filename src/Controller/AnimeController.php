<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\AnimeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Config\Framework\RequestConfig;

final class AnimeController extends AbstractController
{
    private AnimeRepository $animeRepository;

    public function __construct(AnimeRepository $animeRepository)
    {
        $this->animeRepository = $animeRepository;
    }

    #[Route('/anime', name: 'all_anime')]
    public function showAllAnimes(): Response
    {
        $animes = $this->animeRepository->findAll();
        /** @var User $user */
        $user = $this->getUser();
        $animesData = [];
        foreach ($animes as $anime) {
            $animesData[] = [
                'id' => $anime->getId(),
                'title' => $anime->getTitle(),
                'picture' => $anime->getPicture(),
                'isLiked' => $anime->getUsersLiking()->contains($user)
            ];
        }
        return $this->render('anime/all.html.twig', [
            'animes' => $animesData,
            'isAuthenticated' => $this->getUser() !== null,
        ]);
    }

    #[Route('/anime/{id}', name: 'one_anime')]
    public function showAnime(int $id): Response
    {
        $anime = $this->animeRepository->find($id);

        /** @var User $user */
        $user = $this->getUser();

        if (!$anime) {
            throw $this->createNotFoundException('Anime non trouvé');
        }

        $animeData = [
            'id' => $anime->getId(),
            'title' => $anime->getTitle(),
            'picture' => $anime->getPicture(),
            'isLiked' => $anime->getUsersLiking()->contains($user)
        ];

        return $this->render('anime/one.html.twig', [
            'anime' => $animeData,
            'isAuthenticated' => $this->getUser() !== null,
        ]);
    }

    #[Route('/anime/{id}/like', name: 'anime_like',methods: ['POST'])]
    public function likeAnime(int $id, EntityManagerInterface $entityManager, Request $request): Response
    {
        $anime = $this->animeRepository->find($id);

        if (!$anime) {
            throw $this->createNotFoundException('Anime non trouvé');
        }

        /** @var User $user */
        $user = $this->getUser();
        $user->addLikedAnime($anime);

        $entityManager->persist($user);
        $entityManager->flush();

        $route = $request->headers->get('referer');

        return $this->redirect($route);
    }

    #[Route('/anime/{id}/dislike', name: 'anime_dislike', methods: ['POST'])]
    public function disLikeAnime(int $id, EntityManagerInterface $entityManager, Request $request): Response
    {
        $anime = $this->animeRepository->find($id);

        if (!$anime) {
            throw $this->createNotFoundException('Anime non trouvé');
        }

        /** @var User $user */
        $user = $this->getUser();
        $user->removeLikedAnime($anime);

        $entityManager->persist($user);
        $entityManager->flush();

        $route = $request->headers->get('referer');
        return $this->redirect($route);
    }
}
