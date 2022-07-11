<?php

namespace Tests\integration\Post;

class PostGetOneTest extends AbstractPostTestCase
{
    function getApiMethod(): string
    {
        return 'GET';
    }

    public function testNotFound(): void
    {
        // ARRANGE
        $this->resetDb();

        // ACT
        $this->getApiWithCredentials($this->getApiEndpoint() . '/1');

        // ASSERT
        $this->assertResponseStatusCodeSame(404);
    }

    public function testSuccess(): void
    {
        // ARRANGE
        $this->resetDb();
        $posted = $this->postApiWithCredentials([
            'title' => 'title1',
            'body' => 'body1'
        ]);
        $postedId = $this->getEntityIdFromResponse($posted);

        // ACT
        $actual = $this->getApiWithCredentials(
            $this->getApiEndpoint() . '/' . $postedId
        );

        // ASSERT
        $this->assertResponseStatusCodeSame(200);
        $responseBody = $this->responseJsonDecode($actual);
        unset($responseBody['user']['@id']);
        self::assertEquals(
            [
                '@context' => '/api/contexts/Post',
                '@id' => '/api/posts/' . $postedId,
                '@type' => 'Post',
                'id' => $postedId,
                'title' => 'title1',
                'body' => 'body1',
                'user' => [
                    '@type' => 'User',
                    'email' => 'valid@email.com'
                ],

            ],
            $responseBody
        );
    }
}
