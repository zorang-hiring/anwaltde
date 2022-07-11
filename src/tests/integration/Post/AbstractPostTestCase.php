<?php

namespace Tests\integration\Post;

use App\Entity\Post;
use App\Repository\PostRepository;
use Tests\integration\AbstractTestCase;

abstract class AbstractPostTestCase extends AbstractTestCase
{
    private const API_ENDPOINT = '/api/posts';

    protected function getPostsRepo(): PostRepository
    {
        /** @var PostRepository $repo */
        $repo = $this->getEntityManager()->getRepository(Post::class);
        return $repo;
    }

    function getApiEndpoint(): string
    {
        return self::API_ENDPOINT;
    }
}