<?php

namespace Tests\integration;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class LoginTest extends ApiTestCase
{
    public function testOnInvalidCredentialsReturn401(): void
    {
        $response = static::createClient()->request(
            'POST',
            '/authentication_token',
            ['json' => [
                'email' => 'some@email.com',
                'password' => 'some-pass',
            ]]
        );

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonEquals([
            'code' => 401,
            'message' => 'Invalid credentials.'
        ]);
    }

    public function testOnValidCredentialsReturnAuthToken(): void
    {
        $response = static::createClient()->request(
            'POST',
            '/authentication_token',
            ['json' => [
                'email' => 'valid@email.com',
                'password' => 'valid-pass',
            ]]
        );

        $response = json_decode($response->getContent(), true);

        $this->assertResponseStatusCodeSame(200);
        self::assertArrayHasKey('token', $response, 'There should be "token" in response body.');
        self::assertNotEmpty($response['token'], '"token" in response body should not be empty');
    }
}
