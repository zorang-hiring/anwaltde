<?php

namespace Tests\integration\Todo;

class TodoPostTest extends AbstractTodoTestCase
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
                        "status: This value should not be blank.",
                    "violations" => [
                        [
                            "propertyPath" => "status",
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
            ],
            [
                'request' => [
                    'title' => 'some title',
                    'status' => 'invalid status',
                ],
                'expectedResponse' => [
                    "hydra:description" =>
                        "status: The value can be 'pending' or 'completed'.",
                    "violations" => [
                        [
                            "propertyPath" => "status",
                            "message" => "The value can be 'pending' or 'completed'.",
                            "code" => "8e179f1b-97aa-4560-a02f-2a8b42e49df7"
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
        self::assertEmpty($this->getTodoRepo()->findAll(), 'Todo Repo should be empty');
    }

    /**
     * @testWith ["pending", null]
     *           ["completed", "2001-02-03T04:05:06.000Z"]
     */
    public function testSuccess(string $status, $dueDate): void
    {
        // ARRANGE
        $this->resetDb();

        // ACT
        $data = [
            'title' => 'some title',
            'dueOn' => $dueDate,
            'status' => $status
        ];
        if (!$dueDate) {
            unset($data['dueOn']);
        }
        $this->postApiWithCredentials($data);

        // ASSERT
        $this->assertResponseStatusCodeSame(201);

        $actualRecords = $this->getTodoRepo()->findAll();
        self::assertCount(1, $actualRecords);
        self::assertEquals([
            'title' => 'some title',
            'dueOn' => $dueDate ? new \DateTime($dueDate) : null,
            'status' => $status,
            'user' => 'valid@email.com'
        ], $actualRecords[0]->toArray());
    }
}
