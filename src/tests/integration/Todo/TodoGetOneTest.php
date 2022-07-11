<?php

namespace Tests\integration\Todo;

class TodoGetOneTest extends AbstractTodoTestCase
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
            'status' => 'pending',
            'dueOn' => '2001-02-03'
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
                '@context' => '/api/contexts/Todo',
                '@id' => '/api/todos/' . $postedId,
                '@type' => 'Todo',
                'id' => $postedId,
                'title' => 'title1',
                'status' => 'pending',
                'dueOn' => '2001-02-03T00:00:00+00:00',
                'user' => [
                    '@type' => 'User',
                    'email' => 'valid@email.com'
                ],

            ],
            $responseBody
        );
    }
}
