<?php

namespace Tests\integration\Post;

class PostGetTest extends AbstractPostTestCase
{
    function getApiMethod(): string
    {
        return 'GET';
    }

    public function testSuccess_pagination_search_by_title(): void
    {
        // ARRANGE
        $this->resetDb();
        $this->postItems(1, 0, 'title', 'body');
        $this->postItems(5, 1, 'titleFind', 'bodyFind');
        $this->postItems(1, 6, 'title', 'body');

        // ACT
        $actual = $this->getApiWithCredentials(
            $this->getApiEndpoint() . '?title=Find&_page=2'
        );

        // ASSERT
        $this->assertResponseStatusCodeSame(200);
        self::assertEquals(
            [
                '@context' => '/api/contexts/Post',
                '@type' => 'hydra:Collection',
                'hydra:view' => [
                    '@type' => 'hydra:PartialCollectionView',
                    '@id' => '/api/posts?title=Find&_page=2',
                    'hydra:first' => '/api/posts?title=Find&_page=1',
                    'hydra:previous' => '/api/posts?title=Find&_page=1',
                    'hydra:last' => '/api/posts?title=Find&_page=3',
                    'hydra:next' => '/api/posts?title=Find&_page=3',
                ],
                'hydra:member' => [
                    [
                        '@type' => 'Post',
                        'title' => 'titleFind3',
                        'body' => 'bodyFind3',
                        'user' => [
                            '@type' => 'User',
                            'email' => 'valid@email.com'
                        ]
                    ],
                    [
                        '@type' => 'Post',
                        'title' => 'titleFind4',
                        'body' => 'bodyFind4',
                        'user' => [
                            '@type' => 'User',
                            'email' => 'valid@email.com'
                        ]
                    ],
                ],
                'hydra:search' => $this->expectedHydraSearch(),
                'hydra:totalItems' => 5
            ],
            $this->getResponseDataWithoutResourcesIds($actual)
        );
    }

    public function testSuccess_pagination_search_by_body(): void
    {
        // ARRANGE
        $this->resetDb();
        $this->postItems(1, 0, 'title', 'body');
        $this->postItems(5, 1, 'titleFind', 'bodyFind');
        $this->postItems(1, 6, 'title', 'body');

        // ACT
        $actual = $this->getApiWithCredentials(
            $this->getApiEndpoint() . '?body=Find&_page=2'
        );

        // ASSERT
        $this->assertResponseStatusCodeSame(200);
        self::assertEquals(
            [
                '@context' => '/api/contexts/Post',
                '@type' => 'hydra:Collection',
                'hydra:view' => [
                    '@type' => 'hydra:PartialCollectionView',
                    '@id' => '/api/posts?body=Find&_page=2',
                    'hydra:first' => '/api/posts?body=Find&_page=1',
                    'hydra:previous' => '/api/posts?body=Find&_page=1',
                    'hydra:last' => '/api/posts?body=Find&_page=3',
                    'hydra:next' => '/api/posts?body=Find&_page=3',
                ],
                'hydra:member' => [
                    [
                        '@type' => 'Post',
                        'title' => 'titleFind3',
                        'body' => 'bodyFind3',
                        'user' => [
                            '@type' => 'User',
                            'email' => 'valid@email.com'
                        ]
                    ],
                    [
                        '@type' => 'Post',
                        'title' => 'titleFind4',
                        'body' => 'bodyFind4',
                        'user' => [
                            '@type' => 'User',
                            'email' => 'valid@email.com'
                        ]
                    ],
                ],
                'hydra:search' => $this->expectedHydraSearch(),
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
        $this->postItems(1, 0, 'title', 'body');

        $this->setTestAuthToken('valid2@email.com', 'valid-pass2');
        $this->postItems(5, 1, 'titleFind', 'bodyFind');

        $this->setTestAuthToken('valid@email.com', 'valid-pass');
        $this->postItems(1, 6, 'title', 'body');

        $secondUserId = $this->getUserIdByEmail('valid2@email.com');

        // ACT
        $actual = $this->getApiWithCredentials(
            $this->getApiEndpoint() . '?user.id=' . $secondUserId . '&_page=2'
        );

        // ASSERT
        $this->assertResponseStatusCodeSame(200);
        self::assertEquals(
            [
                '@context' => '/api/contexts/Post',
                '@type' => 'hydra:Collection',
                'hydra:view' => [
                    '@type' => 'hydra:PartialCollectionView',
                    '@id' => '/api/posts?user.id=' . $secondUserId . '&_page=2',
                    'hydra:first' => '/api/posts?user.id=' . $secondUserId . '&_page=1',
                    'hydra:previous' => '/api/posts?user.id=' . $secondUserId . '&_page=1',
                    'hydra:last' => '/api/posts?user.id=' . $secondUserId . '&_page=3',
                    'hydra:next' => '/api/posts?user.id=' . $secondUserId . '&_page=3',
                ],
                'hydra:member' => [
                    [
                        '@type' => 'Post',
                        'title' => 'titleFind3',
                        'body' => 'bodyFind3',
                        'user' => [
                            '@type' => 'User',
                            'email' => 'valid2@email.com'
                        ]
                    ],
                    [
                        '@type' => 'Post',
                        'title' => 'titleFind4',
                        'body' => 'bodyFind4',
                        'user' => [
                            '@type' => 'User',
                            'email' => 'valid2@email.com'
                        ]
                    ],
                ],
                'hydra:search' => $this->expectedHydraSearch(),
                'hydra:totalItems' => 5
            ],
            $this->getResponseDataWithoutResourcesIds($actual)
        );
    }

    /**
     * @param int $count Number of items to post
     * @return void
     */
    public function postItems(int $count, int $from, string $withTitleBase, string $withBodyBase): void
    {
        for ($i=$from; $i<($count+$from); $i++) {
            $this->postApiWithCredentials([
                'title' => $withTitleBase . $i,
                'body' => $withBodyBase . $i
            ]);
        }
    }

    /**
     * @return array[]
     */
    public function expectedHydraSearch(): array
    {
        return [
            '@type' => 'hydra:IriTemplate',
            'hydra:template' => '/api/posts{?title,body,user.id,user.id[]}',
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
                    'variable' => 'body',
                    'property' => 'body',
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
                ]
            ]
        ];
    }
}
