<?php

namespace App\Controller;

use App\Entity\StarredAnime;
use App\Entity\User;
use App\Repository\AnimeRepository;
use App\Repository\UserRepository;
use App\Service\CommentService;
use App\Service\DescriptionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Controller responsible for handling anime-related operations.
 */
final class AnimeController extends AbstractController
{
    private AnimeRepository $animeRepository;
    private UserRepository $userRepository;
    private CommentService $commentService;
    private DescriptionService $descriptionService;

    /**
     * Constructor to inject dependencies.
     */
    public function __construct(AnimeRepository $animeRepository, UserRepository $userRepository, CommentService $commentService, DescriptionService $descriptionService)
    {
        $this->animeRepository = $animeRepository;
        $this->userRepository = $userRepository;
        $this->commentService = $commentService;
        $this->descriptionService = $descriptionService;
    }

    /**
     * Displays all available anime.
     */
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
                'isLiked' => $anime->getUsersLiking()->contains($user),
                'stars' => min(5, $anime->getStarsOfUser($user))
            ];
        }

        return $this->render('anime/all.html.twig', [
            'animes' => $animesData,
            'isAuthenticated' => $this->getUser() !== null,
        ]);
    }

    /**
     * Displays anime liked by the authenticated user.
     * 
     * @IsGranted ensures only authenticated users can access this route.
     */
    #[Route('/anime/liked', name: 'liked_anime')]
    #[IsGranted('ROLE_USER')]
    public function showLikedAnimes(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $animes = $user->getLikedAnimes();
        $animesData = [];

        foreach ($animes as $anime) {
            $animesData[] = [
                'id' => $anime->getId(),
                'title' => $anime->getTitle(),
                'picture' => $anime->getPicture(),
                'isLiked' => $anime->getUsersLiking()->contains($user),
                'stars' => min(5, $anime->getStarsOfUser($user))
            ];
        }

        return $this->render('anime/like.html.twig', [
            'animes' => $animesData,
            'isAuthenticated' => $this->getUser() !== null,
        ]);
    }

    /**
     * Displays a single anime by its ID.
     */
    #[Route('/anime/{id}', name: 'one_anime')]
    public function showAnime(int $id): Response
    {
        $anime = $this->animeRepository->find($id);

        /** @var User $user */
        $user = $this->getUser();

        if (!$anime) {
            throw $this->createNotFoundException('Anime not found');
        }

        // Retrieve comments for the anime
        $comments = $this->commentService->getCommentsByAnime($id);
        $commentsData = [];

        foreach ($comments as $comment) {
            /** @var User $author */
            $author = $this->userRepository->find(intval($comment['author']));
            $commentsData[] = [
                'id' => $comment['id'],
                'author' => $author->getEmail(),
                'content' => $comment['content'],
                'created_at' => $comment['created_at'],
            ];
        }

        // Fetch a short description for the anime
        $animeDescription = $this->descriptionService->getShortDescriptionForAnime($anime->getTitle());

        $animeData = [
            'id' => $anime->getId(),
            'title' => $anime->getTitle(),
            'description' => $animeDescription,
            'picture' => $anime->getPicture(),
            'isLiked' => $anime->getUsersLiking()->contains($user),
            'comments' => $commentsData,
            'stars' => min(5, $anime->getStarsOfUser($user))
        ];

        return $this->render('anime/one.html.twig', [
            'anime' => $animeData,
            'isAuthenticated' => $this->getUser() !== null,
        ]);
    }

    /**
     * Allows a user to like an anime.
     */
    #[Route('/anime/{id}/like', name: 'anime_like', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function likeAnime(int $id, EntityManagerInterface $entityManager, Request $request): Response
    {
        $anime = $this->animeRepository->find($id);

        if (!$anime) {
            throw $this->createNotFoundException('Anime not found');
        }

        /** @var User $user */
        $user = $this->getUser();
        $user->addLikedAnime($anime);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Allows a user to remove their like from an anime.
     */
    #[Route('/anime/{id}/dislike', name: 'anime_dislike', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function disLikeAnime(int $id, EntityManagerInterface $entityManager, Request $request): Response
    {
        $anime = $this->animeRepository->find($id);

        if (!$anime) {
            throw $this->createNotFoundException('Anime not found');
        }

        /** @var User $user */
        $user = $this->getUser();
        $user->removeLikedAnime($anime);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Adds a comment to an anime.
     */
    #[Route('/anime/{id}/comment', name: 'anime_comment', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function addCommentOnAnime(Request $request, int $id): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $body = $request->get('comment_body');

        if (!$this->animeRepository->find($id)) {
            throw $this->createNotFoundException('Anime not found');
        }

        $this->commentService->addCommentOnAnime($id, $body, $user->getId());

        return $this->redirectToRoute('one_anime', ['id' => $id]);
    }

    /**
     * Allows a user to rate an anime with stars.
     */
    #[Route('/anime/{id}/rate', name: 'anime_rate', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function starAnime(EntityManagerInterface $entityManager, Request $request, int $id): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $stars = $request->get('rating');
        $anime = $this->animeRepository->find($id);

        if (!$anime) {
            throw $this->createNotFoundException('Anime not found');
        }

        // Check if the user has already rated the anime
        $starredAnime = $user->getStarredAnime($anime);

        if (!$starredAnime) {
            $starredAnime = new StarredAnime();
            $starredAnime->setUser($user);
            $starredAnime->setAnime($anime);
        }

        $starredAnime->setStars($stars);
        $user->addStarredAnime($starredAnime);

        $entityManager->persist($starredAnime);
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('one_anime', ['id' => $id]);
    }
}
