<?php

namespace Tests\integration\Post;

class PostPatchTest extends AbstractPostTestCase
{
    function getApiMethod(): string
    {
        return 'PATCH';
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
        $actual = $this->patchApiWithCredentials(1, []);

        // ASSERT
        $this->assertResponseStatusCodeSame(404);
    }

    public function testSuccess(): void
    {
        // ARRANGE
        $this->resetDb();
        $posted = $this->postApiWithCredentials([
            'title' => 'posted title',
            'body' => 'posted body'
        ]);

        // ACT
        $this->patchApiWithCredentials(
            $this->getEntityIdFromResponse($posted),
            [
                'title' => 'new title',
                'body' => 'new body'
            ]
        );

        // ASSERT
        $this->assertResponseStatusCodeSame(200);
        // assert db content
        $dbContent = $this->getPostsRepo()->findAll();
        self::assertCount(1, $dbContent);
        self::assertEquals([
            'title' => 'new title',
            'body' => 'new body',
            'user' => 'valid@email.com'
        ], $dbContent[0]->toArray());
    }
}
