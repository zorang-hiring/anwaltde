<?php

namespace Tests\integration\Todo;

class TodoPutTest extends AbstractTodoTestCase
{
    function getApiMethod(): string
    {
        return 'PUT';
    }

    public function testIfNotLoggedPostIsProtected(): void
    {
        $this->makeApiCallWithoutCredentials($this->getApiEndpoint() . '/1');
        $this->assertResponseStatusCodeSame(401);
    }

    public function testNotFound(): void
    {
        // ARRANGE
        $this->resetDb();

        // ACT
        $actual = $this->putApiWithCredentials(1, []);

        // ASSERT
        $this->assertResponseStatusCodeSame(404);
    }

    public function testSuccess(): void
    {
        // ARRANGE
        $this->resetDb();
        $posted = $this->postApiWithCredentials([
            'title' => 'posted title',
            'status' => 'pending',
            'dueOn' => '2001-02-03'
        ]);

        // ACT
        $this->putApiWithCredentials(
            $this->getEntityIdFromResponse($posted),
            [
                'title' => 'new title',
                'status' => 'completed',
                'dueOn' => '2002-03-04'
            ]
        );

        // ASSERT
        $this->assertResponseStatusCodeSame(200);
        // assert db content
        $dbContent = $this->getTodoRepo()->findAll();
        self::assertCount(1, $dbContent);
        self::assertEquals([
            'title' => 'new title',
            'status' => 'completed',
            'dueOn' => new \DateTime('2002-03-04'),
            'user' => 'valid@email.com'
        ], $dbContent[0]->toArray());
    }
}
