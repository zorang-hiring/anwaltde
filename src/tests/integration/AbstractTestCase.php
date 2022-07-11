<?php

namespace Tests\integration;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Hautelook\AliceBundle\PhpUnit\BaseDatabaseTrait;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Output\BufferedOutput;

abstract class AbstractTestCase extends ApiTestCase
{
    /**
     * @var string Auth Token
     */
    private $token;

    use BaseDatabaseTrait;

    abstract function getApiEndpoint(): string;
    abstract function getApiMethod(): string;

    public function setUp(): void
    {
        self::bootKernel();
    }

    public function getApiWithCredentials(string $endpoint): ResponseInterface
    {
        return AbstractTestCase::createClientWithCredentials()
            ->request(
                'GET',
                $endpoint
            );
    }

    public function postApiWithCredentials(array $requestData): ResponseInterface
    {
        return AbstractTestCase::createClientWithCredentials()
            ->request(
                'POST',
                $this->getApiEndpoint(),
                ['json' => $requestData]
            );
    }

    public function putApiWithCredentials(int $resourceId, array $requestData): ResponseInterface
    {
        return AbstractTestCase::createClientWithCredentials()
            ->request(
                'PUT',
                $this->getApiEndpoint() . '/' . $resourceId,
                ['json' => $requestData]
            );
    }

    public function patchApiWithCredentials(int $resourceId, array $requestData): ResponseInterface
    {
        return AbstractTestCase::createClientWithCredentials()
            ->request(
                'PATCH',
                $this->getApiEndpoint() . '/' . $resourceId,
                [
                    'json' => $requestData,
                    'headers' => [
                        'authorization' => 'Bearer ' . $this->getToken(),
                        'Content-Type' => 'application/merge-patch+json'
                    ]
                ]
            );
    }

    public function deleteApiResourceWithCredentials(int $resourceId): ResponseInterface
    {
        return AbstractTestCase::createClientWithCredentials()
            ->request(
                'DELETE',
                $this->getApiEndpoint() . '/' . $resourceId
            );
    }

    public function makeApiCallWithoutCredentials(string $endpoint): void
    {
        ApiTestCase::createClient()->request(
            $this->getApiMethod(),
            $endpoint
        );
    }

    /**
     * @return mixed|null
     */
    public function getEntityIdFromResponse(ResponseInterface $response)
    {
        $response = $this->responseJsonDecode($response);
        return $response['id'] ?? null;
    }

    protected function resetDb(): void
    {
        self::populateDatabase();

        $this->execCommand(new ArrayInput([
            'command' => 'doctrine:fixtures:load',
            '--no-interaction' => true
        ]));
    }

    protected function execCommand(ArrayInput $command): void
    {
        $application = new Application(self::$kernel);
        $application->setAutoExit(false);

        $output = new BufferedOutput();
        $application->run($command, $output);
    }

    protected function createClientWithCredentials($token = null): Client
    {
        $token = $token ?: $this->getToken();

        return static::createClient([], [
            'headers' => ['authorization' => 'Bearer ' . $token]
        ]);
    }

    protected function clearTestAuthToken()
    {
        $this->token = null;
    }

    /**
     * Clear current token and set a new one
     *
     * @param $forUserEmail
     * @param $forUserPassword
     * @return string
     */
    protected function setTestAuthToken($forUserEmail, $forUserPassword): string
    {
        $this->clearTestAuthToken();

        return $this->token = $this->_getToken([
            'email' => $forUserEmail,
            'password' => $forUserPassword,
        ]);
    }

    /**
     * Get current token, if not exists create it
     * @return string
     */
    protected function getToken(): string
    {
        if ($this->token) {
            return $this->token;
        }

        return $this->token = $this->_getToken([
            'email' => 'valid@email.com',
            'password' => 'valid-pass',
        ]);
    }

    private function _getToken(array $body = []): string
    {
        $response = static::createClient()->request(
            'POST',
            '/authentication_token',
            ['json' => $body]);

        $this->assertResponseIsSuccessful();
        $data = $this->responseJsonDecode($response);
        return $data['token'];
    }

    public function assertStatus422Body(
        array             $expectedResponse,
        ResponseInterface $response
    ): void
    {

        // add common content
        $expectedResponse = array_merge($expectedResponse, [
            "@context" => "/api/contexts/ConstraintViolationList",
            "@type" => "ConstraintViolationList",
            "hydra:title" => "An error occurred",
        ]);

        $this->assertJsonEquals(json_encode($expectedResponse), $response->getContent(false));
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        /** @var EntityManagerInterface $em */
        $em = self::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        return $em;
    }

    protected function responseJsonDecode(ResponseInterface $actual)
    {
        return json_decode($actual->getContent(false), true);
    }

    /**
     * Response can contain IDs which are dynamic, we don't want to test them in tests.
     * This method will cut these IDs and return response as array.
     */
    protected function getResponseDataWithoutResourcesIds(ResponseInterface $response): array
    {
        $responseBody = $this->responseJsonDecode($response);

        unset($responseBody['@id']);
        unset($responseBody['id']);
        unset($responseBody['user']['@id']);

        if (isset($responseBody['hydra:member'])) {
            foreach ($responseBody['hydra:member'] as $k => $item) {
                unset($item['@id']);
                unset($item['id']);
                unset($item['user']['@id']);
                $responseBody['hydra:member'][$k] = $item;
            }
        }

        return $responseBody;
    }

    protected function getUserIdByEmail(string $email): int
    {
        /** @var UserRepository $repo */
        $repo = $this->getEntityManager()->getRepository(User::class);
        return $repo->findOneBy(['email' => $email])->getId();
    }
}