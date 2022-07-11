<?php

namespace Tests\integration\Todo;

class TodoGetTest extends AbstractTodoTestCase
{
    function getApiMethod(): string
    {
        return 'GET';
    }

    public function testSuccess_pagination_search_by_title(): void
    {
        // ARRANGE
        $this->resetDb();
        $this->postItems(1, 0, [
            'title' => 'title',
            'dueOn' => '2001-02-03',
            'status' => 'pending'
        ]);
        $this->postItems(5, 1, [
            'title' => 'titleFind',
            'dueOn' => '2002-03-04',
            'status' => 'completed'
        ]);
        $this->postItems(1, 6, [
            'title' => 'title',
            'dueOn' => '2001-02-03',
            'status' => 'pending'
        ]);

        // ACT
        $actual = $this->getApiWithCredentials(
            $this->getApiEndpoint() . '?title=Find&_page=2'
        );

        // ASSERT
        $this->assertResponseStatusCodeSame(200);
        self::assertEquals(
            [
                '@context' => '/api/contexts/Todo',
                '@type' => 'hydra:Collection',
                'hydra:view' => [
                    '@type' => 'hydra:PartialCollectionView',
                    '@id' => '/api/todos?title=Find&_page=2',
                    'hydra:first' => '/api/todos?title=Find&_page=1',
                    'hydra:previous' => '/api/todos?title=Find&_page=1',
                    'hydra:last' => '/api/todos?title=Find&_page=3',
                    'hydra:next' => '/api/todos?title=Find&_page=3',
                ],
                'hydra:member' => [
                    [
                        '@type' => 'Todo',
                        'title' => 'titleFind3',
                        'dueOn' => '2002-03-04T00:00:00+00:00',
                        'status' => 'completed',
                        'user' => [
                            '@type' => 'User',
                            'email' => 'valid@email.com'
                        ]
                    ],
                    [
                        '@type' => 'Todo',
                        'title' => 'titleFind4',
                        'dueOn' => '2002-03-04T00:00:00+00:00',
                        'status' => 'completed',
                        'user' => [
                            '@type' => 'User',
                            'email' => 'valid@email.com'
                        ]
                    ],
                ],
                'hydra:search' => $this->expectedHidraSearch(),
                'hydra:totalItems' => 5
            ],
            $this->getResponseDataWithoutResourcesIds($actual)
        );
    }

    public function testSuccess_pagination_search_by_status(): void
    {
        // ARRANGE
        $this->resetDb();
        $this->postItems(1, 0, [
            'title' => 'title',
            'dueOn' => '2001-02-03',
            'status' => 'pending'
        ]);
        $this->postItems(5, 1, [
            'title' => 'titleFind',
            'dueOn' => '2002-03-04',
            'status' => 'completed'
        ]);
        $this->postItems(1, 6, [
            'title' => 'title',
            'dueOn' => '2001-02-03',
            'status' => 'pending'
        ]);

        // ACT
        $actual = $this->getApiWithCredentials(
            $this->getApiEndpoint() . '?status=completed&_page=2'
        );

        // ASSERT
        $this->assertResponseStatusCodeSame(200);
        self::assertEquals(
            [
                '@context' => '/api/contexts/Todo',
                '@type' => 'hydra:Collection',
                'hydra:view' => [
                    '@type' => 'hydra:PartialCollectionView',
                    '@id' => '/api/todos?status=completed&_page=2',
                    'hydra:first' => '/api/todos?status=completed&_page=1',
                    'hydra:previous' => '/api/todos?status=completed&_page=1',
                    'hydra:last' => '/api/todos?status=completed&_page=3',
                    'hydra:next' => '/api/todos?status=completed&_page=3',
                ],
                'hydra:member' => [
                    [
                        '@type' => 'Todo',
                        'title' => 'titleFind3',
                        'dueOn' => '2002-03-04T00:00:00+00:00',
                        'status' => 'completed',
                        'user' => [
                            '@type' => 'User',
                            'email' => 'valid@email.com'
                        ]
                    ],
                    [
                        '@type' => 'Todo',
                        'title' => 'titleFind4',
                        'dueOn' => '2002-03-04T00:00:00+00:00',
                        'status' => 'completed',
                        'user' => [
                            '@type' => 'User',
                            'email' => 'valid@email.com'
                        ]
                    ],
                ],
                'hydra:search' => $this->expectedHidraSearch(),
                'hydra:totalItems' => 5
            ],
            $this->getResponseDataWithoutResourcesIds($actual)
        );
    }

    public function testSuccess_pagination_search_by_user(): void
    {
        // ARRANGE
        $this->resetDb();
        $this->setTestAuthToken('valid@email.com', 'valid-pass');
        $this->postItems(1, 0, [
            'title' => 'title',
            'dueOn' => '2001-02-03',
            'status' => 'pending'
        ]);
        $this->setTestAuthToken('valid2@email.com', 'valid-pass2');
        $this->postItems(5, 1, [
            'title' => 'titleFind',
            'dueOn' => '2002-03-04',
            'status' => 'completed'
        ]);
        $this->setTestAuthToken('valid@email.com', 'valid-pass');
        $this->postItems(1, 6, [
            'title' => 'title',
            'dueOn' => '2001-02-03',
            'status' => 'pending'
        ]);
        $secondUserId = $this->getUserIdByEmail('valid2@email.com');

        // ACT
        $actual = $this->getApiWithCredentials(
            $this->getApiEndpoint() . '?user.id=' . $secondUserId . '&_page=2'
        );

        // ASSERT
        $this->assertResponseStatusCodeSame(200);
        self::assertEquals(
            [
                '@context' => '/api/contexts/Todo',
                '@type' => 'hydra:Collection',
                'hydra:view' => [
                    '@type' => 'hydra:PartialCollectionView',
                    '@id' => '/api/todos?user.id=' . $secondUserId . '&_page=2',
                    'hydra:first' => '/api/todos?user.id=' . $secondUserId . '&_page=1',
                    'hydra:previous' => '/api/todos?user.id=' . $secondUserId . '&_page=1',
                    'hydra:last' => '/api/todos?user.id=' . $secondUserId . '&_page=3',
                    'hydra:next' => '/api/todos?user.id=' . $secondUserId . '&_page=3',
                ],
                'hydra:member' => [
                    [
                        '@type' => 'Todo',
                        'title' => 'titleFind3',
                        'dueOn' => '2002-03-04T00:00:00+00:00',
                        'status' => 'completed',
                        'user' => [
                            '@type' => 'User',
                            'email' => 'valid2@email.com'
                        ]
                    ],
                    [
                        '@type' => 'Todo',
                        'title' => 'titleFind4',
                        'dueOn' => '2002-03-04T00:00:00+00:00',
                        'status' => 'completed',
                        'user' => [
                            '@type' => 'User',
                            'email' => 'valid2@email.com'
                        ]
                    ],
                ],
                'hydra:search' => $this->expectedHidraSearch(),
                'hydra:totalItems' => 5
            ],
            $this->getResponseDataWithoutResourcesIds($actual)
        );
    }

    public function testSuccess_pagination_search_by_dueOn(): void
    {
        // ARRANGE
        $this->resetDb();
        $this->postItems(1, 0, [
            'title' => 'title',
            'dueOn' => '2001-02-03',
            'status' => 'pending'
        ]);
        $this->postItems(5, 1, [
            'title' => 'titleFind',
            'dueOn' => '2002-03-04',
            'status' => 'completed'
        ]);
        $this->postItems(1, 6, [
            'title' => 'title',
            'dueOn' => '2001-02-03',
            'status' => 'pending'
        ]);

        // ACT
        $actual = $this->getApiWithCredentials(
            $this->getApiEndpoint() . '?dueOn%5Bafter%5D=2002-01-01&_page=2'
        );

        // ASSERT
        $this->assertResponseStatusCodeSame(200);
        self::assertEquals(
            [
                '@context' => '/api/contexts/Todo',
                '@type' => 'hydra:Collection',
                'hydra:view' => [
                    '@type' => 'hydra:PartialCollectionView',
                    '@id' => '/api/todos?dueOn%5Bafter%5D=2002-01-01&_page=2',
                    'hydra:first' => '/api/todos?dueOn%5Bafter%5D=2002-01-01&_page=1',
                    'hydra:previous' => '/api/todos?dueOn%5Bafter%5D=2002-01-01&_page=1',
                    'hydra:last' => '/api/todos?dueOn%5Bafter%5D=2002-01-01&_page=3',
                    'hydra:next' => '/api/todos?dueOn%5Bafter%5D=2002-01-01&_page=3',
                ],
                'hydra:member' => [
                    [
                        '@type' => 'Todo',
                        'title' => 'titleFind3',
                        'dueOn' => '2002-03-04T00:00:00+00:00',
                        'status' => 'completed',
                        'user' => [
                            '@type' => 'User',
                            'email' => 'valid@email.com'
                        ]
                    ],
                    [
                        '@type' => 'Todo',
                        'title' => 'titleFind4',
                        'dueOn' => '2002-03-04T00:00:00+00:00',
                        'status' => 'completed',
                        'user' => [
                            '@type' => 'User',
                            'email' => 'valid@email.com'
                        ]
                    ],
                ],
                'hydra:search' => $this->expectedHidraSearch(),
                'hydra:totalItems' => 5
            ],
            $this->getResponseDataWithoutResourcesIds($actual)
        );
    }

    /**
     * @param int $count Number of items to post
     * @return void
     */
    public function postItems(
        int $count,
        int $from,
        array $data
    ): void {
        for ($i=$from; $i<($count+$from); $i++) {
            $this->postApiWithCredentials([
                'title' => $data['title'] . $i,
                'dueOn' => $data['dueOn'],
                'status' => $data['status'],
            ]);
        }
    }

    /**
     * @return array
     */
    public function expectedHidraSearch(): array
    {
        return [
            '@type' => 'hydra:IriTemplate',
            'hydra:template' => '/api/todos{?title,status,user.id,user.id[],dueOn[before],dueOn[strictly_before],dueOn[after],dueOn[strictly_after]}',
            'hydra:variableRepresentation' => 'BasicRepresentation',
            'hydra:mapping' => [
                [
                    '@type' => 'IriTemplateMapping',
                    'variable' => 'title',
                    'property' => 'title',
                    'required' => false
                ],
                [
                    '@type' => 'IriTemplateMapping',
                    'variable' => 'status',
                    'property' => 'status',
                    'required' => false
                ],
                [
                    '@type' => 'IriTemplateMapping',
                    'variable' => 'user.id',
                    'property' => 'user.id',
                    'required' => false
                ],
                [
                    '@type' => 'IriTemplateMapping',
                    'variable' => 'user.id[]',
                    'property' => 'user.id',
                    'required' => false
                ],
                [
                    '@type' => 'IriTemplateMapping',
                    'variable' => 'dueOn[before]',
                    'property' => 'dueOn',
                    'required' => false
                ],
                [
                    '@type' => 'IriTemplateMapping',
                    'variable' => 'dueOn[strictly_before]',
                    'property' => 'dueOn',
                    'required' => false

                ],
                [
                    '@type' => 'IriTemplateMapping',
                    'variable' => 'dueOn[after]',
                    'property' => 'dueOn',
                    'required' => false
                ],
                [
                    '@type' => 'IriTemplateMapping',
                    'variable' => 'dueOn[strictly_after]',
                    'property' => 'dueOn',
                    'required' => false

                ]
            ]
        ];
    }
}
