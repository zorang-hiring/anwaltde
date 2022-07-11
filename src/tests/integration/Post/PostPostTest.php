<?php

namespace Tests\integration\Post;

class PostPostTest extends AbstractPostTestCase
{
    function getApiMethod(): string
    {
        return 'POST';
    }

    public function testIfNotLoggedPostIsProtected(): void
    {
        $this->makeApiCallWithoutCredentials($this->getApiEndpoint());
        $this->assertResponseStatusCodeSame(401);
    }

    public function dataProvider_testInvalidRequest()
    {
        return [
            [
                'request' => [],
                'expectedResponse' => [
                    "hydra:description" =>
                        "title: This value should not be blank.\n" .
                        "body: This value should not be blank.",
                    "violations" => [
                        [
                            "propertyPath" => "body",
                            "message" => "This value should not be blank.",
                            "code" => "c1051bb4-d103-4f74-8988-acbcafc7fdc3"
                        ],
                        [
                            "propertyPath" => "title",
                            "message" => "This value should not be blank.",
                            "code" => "c1051bb4-d103-4f74-8988-acbcafc7fdc3"
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @dataProvider dataProvider_testInvalidRequest
     */
    public function testInvalidRequest(array $requestData, array $expectedResponse): void
    {
        // ARRANGE
        $this->resetDb();

        // ACT
        $actual = $this->postApiWithCredentials($requestData);

        // ASSERT
        $this->assertResponseStatusCodeSame(422);
        $this->assertStatus422Body($expectedResponse, $actual);
        self::assertEmpty($this->getPostsRepo()->findAll(), 'Posts Repo should be empty');
    }

    public function testSuccess(): void
    {
        // ARRANGE
        $this->resetDb();

        // ACT
        $this->postApiWithCredentials([
            'title' => 'some title',
            'body' => 'some body',
        ]);

        // ASSERT
        $this->assertResponseStatusCodeSame(201);

        $actualRecords = $this->getPostsRepo()->findAll();
        self::assertCount(1, $actualRecords);
        self::assertEquals([
            'title' => 'some title',
            'body' => 'some body',
            'user' => 'valid@email.com'
        ], $actualRecords[0]->toArray());
    }
}
