<?php
namespace Geekdevs\OAuth2\Client\Test\Provider;

use Geekdevs\OAuth2\Client\Provider\Cronofy;
use League\OAuth2\Client\Token\AccessToken;
use Mockery as m;

class CronofyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Cronofy
     */
    protected $provider;

    protected function setUp()
    {
        $this->provider = new Cronofy([
            'clientId' => 'mock_client_id',
            'clientSecret' => 'mock_secret',
            'redirectUri' => 'none',
            'hostedDomain' => 'mock_domain',
            'accessType' => 'mock_access_type'
        ]);
    }

    public function tearDown()
    {
        m::close();
        parent::tearDown();
    }

    public function testAuthorizationUrl()
    {
        $url = $this->provider->getAuthorizationUrl();
        $uri = parse_url($url);
        parse_str($uri['query'], $query);

        $this->assertArrayHasKey('client_id', $query);
        $this->assertArrayHasKey('redirect_uri', $query);
        $this->assertArrayHasKey('state', $query);
        $this->assertArrayHasKey('scope', $query);
        $this->assertArrayHasKey('response_type', $query);
        $this->assertArrayHasKey('approval_prompt', $query);

        $this->assertContains('read_account', $query['scope']);

        $this->assertAttributeNotEmpty('state', $this->provider);
    }

    public function testBaseAccessTokenUrl()
    {
        $url = $this->provider->getBaseAccessTokenUrl([]);
        $uri = parse_url($url);

        $this->assertEquals('/oauth/token', $uri['path']);
    }

    public function testResourceOwnerDetailsUrl()
    {
        $token = $this->mockAccessToken();

        $url = $this->provider->getResourceOwnerDetailsUrl($token);
        $uri = parse_url($url);

        $this->assertEquals('/v1/account', $uri['path']);
        $this->assertNotContains('mock_access_token', $url);

    }

    public function testAccountData()
    {
        /**
         * @var \GuzzleHttp\Psr7\Response | m\Mock $response
         */
        $response = m::mock('GuzzleHttp\Psr7\Response');

        $response->shouldReceive('getStatusCode')
            ->andReturn(200);

        $response->shouldReceive('getHeader')
            ->with('content-type')
            ->andReturn(['application/json']);

        $response->shouldReceive('getBody')
            ->andReturn('
                {
                    "account": {
                        "account_id": "acc_567236000909002",
                        "email": "janed@company.com",
                        "name": "Jane Doe",
                        "default_tzid": "Europe/London"
                    }
                }
            ');

        /**
         * @var Cronofy | m\Mock $provider
         */
        $provider = m::mock('Geekdevs\OAuth2\Client\Provider\Cronofy[sendRequest]')
            ->shouldAllowMockingProtectedMethods();

        $provider->shouldReceive('sendRequest')
            ->times(1)
            ->andReturn($response);

        $token = $this->mockAccessToken();
        $account = $provider->getResourceOwner($token);

        //Check Response
        $this->assertInstanceOf('League\OAuth2\Client\Provider\ResourceOwnerInterface', $account);

        $this->assertSame('acc_567236000909002', $account->getId());
        $this->assertSame('janed@company.com', $account->getEmail());
        $this->assertSame('Jane Doe', $account->getName());
        $this->assertSame('Europe/London', $account->getDefaultTimezone());
        
        $this->assertSame([
            'account_id' => 'acc_567236000909002',
            'email' => 'janed@company.com',
            'name' => 'Jane Doe',
            'default_tzid' => 'Europe/London'
        ], $account->toArray());
    }

    /**
     * @expectedException \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function testErrorResponse()
    {
        /**
         * @var \GuzzleHttp\Psr7\Response | m\Mock $response
         */
        $response = m::mock('GuzzleHttp\Psr7\Response');

        $response->shouldReceive('getStatusCode')
            ->andReturn(200);

        $response->shouldReceive('getHeader')
            ->with('content-type')
            ->andReturn(['application/json']);

        $response->shouldReceive('getBody')
            ->andReturn('{"error": "Format error"}');

        $provider = m::mock('Geekdevs\OAuth2\Client\Provider\Cronofy[sendRequest]')
            ->shouldAllowMockingProtectedMethods();

        /**
         * @var Cronofy | m\Mock $provider
         */
        $provider->shouldReceive('sendRequest')
            ->times(1)
            ->andReturn($response);

        $token = $this->mockAccessToken();
        $provider->getResourceOwner($token);
    }

    /**
     * @return AccessToken | m\Mock $token
     */
    private function mockAccessToken()
    {
        return m::mock('League\OAuth2\Client\Token\AccessToken', [['access_token' => 'mock_access_token']]);
    }
}