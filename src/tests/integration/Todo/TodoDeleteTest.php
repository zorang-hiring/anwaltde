<?php

namespace Tests\integration\Todo;

class TodoDeleteTest extends AbstractTodoTestCase
{
    function getApiMethod(): string
    {
        return 'DELETE';
    }

    public function testIfNotLoggedPostIsProtected(): void
    {
        $this->makeApiCallWithoutCredentials($this->getApiEndpoint() . '/1');
        $this->assertResponseStatusCodeSame(401);
    }

    public function testNonexistent(): void
    {
        // ARRANGE
        $this->resetDb();

        // ACT
        $this->deleteApiResourceWithCredentials(1);

        // ASSERT
        $this->assertResponseStatusCodeSame(404);
    }

    public function testSuccess(): void
    {
        // ARRANGE
        $this->resetDb();
        $response = $this->postApiWithCredentials([
            'title' => 'title',
            'status' => 'pending',
            'dueOn' => '2001-02-03'
        ]);

        // ACT
        $this->deleteApiResourceWithCredentials(
            $this->getEntityIdFromResponse($response)
        );

        // ASSERT
        $this->assertResponseStatusCodeSame(204);
        self::assertEmpty($this->getTodoRepo()->findAll());
    }
}
