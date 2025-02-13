<?php

return [
    // Core Symfony framework bundle
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],

    // Development tool for generating code (only enabled in dev environment)
    Symfony\Bundle\MakerBundle\MakerBundle::class => ['dev' => true],

    // Doctrine ORM bundle for database interactions
    Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class => ['all' => true],

    // Handles database migrations
    Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle::class => ['all' => true],

    // Security bundle for authentication & authorization
    Symfony\Bundle\SecurityBundle\SecurityBundle::class => ['all' => true],

    // Doctrine fixtures bundle (for loading test/development data)
    Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle::class => ['dev' => true, 'test' => true],

    // Webpack Encore bundle for asset management
    Symfony\WebpackEncoreBundle\WebpackEncoreBundle::class => ['all' => true],

    // Twig template engine bundle
    Symfony\Bundle\TwigBundle\TwigBundle::class => ['all' => true],

    // Email verification bundle from SymfonyCasts
    SymfonyCasts\Bundle\VerifyEmail\SymfonyCastsVerifyEmailBundle::class => ['all' => true],
];
