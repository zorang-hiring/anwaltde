<?php

namespace Tests\integration\Todo;

use App\Entity\Todo;
use App\Repository\TodoRepository;
use Tests\integration\AbstractTestCase;

abstract class AbstractTodoTestCase extends AbstractTestCase
{
    private const API_ENDPOINT = '/api/todos';

    protected function getTodoRepo(): TodoRepository
    {
        /** @var TodoRepository $repo */
        $repo = $this->getEntityManager()->getRepository(Todo::class);
        return $repo;
    }

    function getApiEndpoint(): string
    {
        return self::API_ENDPOINT;
    }
}