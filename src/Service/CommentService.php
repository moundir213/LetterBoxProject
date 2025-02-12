<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\ResponseInterface;

interface CommentService
{
    public function getCommentsByAnime(int $id): array;

    public function getCommentsByUser(int $id): array;

    public function addCommentOnAnime(int $animeId, string $content, int $authorId): ResponseInterface;
}