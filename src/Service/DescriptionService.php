<?php

namespace App\Service;

interface DescriptionService
{
    public function getShortDescriptionForAnime(string $anime_name): string;
}