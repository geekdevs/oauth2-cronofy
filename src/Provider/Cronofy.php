<?php
namespace Geekdevs\OAuth2\Client\Provider;

use GuzzleHttp\HandlerStack;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;
use Somoza\Psr7\OAuth2Middleware;

/**
 * Represents Cronofy service provider
 * @see https://www.cronofy.com/developers/api/
 */
class Cronofy extends AbstractProvider
{
    const ACCESS_TOKEN_RESOURCE_OWNER_ID = 'account_id';

    use BearerAuthorizationTrait;

    /**
     * @var callable
     */
    protected $tokenCallback;

    /**
     * @inheritdoc
     */
    public function getBaseAuthorizationUrl()
    {
        return 'https://app.cronofy.com/oauth/authorize';
    }

    /**
     * @inheritdoc
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return 'https://api.cronofy.com/oauth/token';
    }

    /**
     * @inheritdoc
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return 'https://api.cronofy.com/v1/account';
    }

    /**
     * @inheritdoc
     */
    public function getDefaultScopes()
    {
        return ['read_account'];
    }

    /**
     * @return string
     */
    protected function getScopeSeparator()
    {
        return ' ';
    }

    /**
     * @inheritdoc
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        $statusCode = $response->getStatusCode();

        if (isset($data['error']) || $statusCode<200 || $statusCode>299) {
            $error = isset($data['error']) ? $data['error'] : 'Bad status code: ' . $statusCode;
            throw new IdentityProviderException($error, $statusCode, $data);
        }
    }

    /**
     * @inheritdoc
     */
    public function getAuthenticatedRequest($method, $url, $token, array $options = [])
    {
        $oauth2 = new OAuth2Middleware\Bearer($this, $token, $this->tokenCallback);

        $handlerStack = $this->getHandlerStack();
        $handlerStack->remove('access_token');
        $handlerStack->push($oauth2, 'access_token');

        return $this->createRequest($method, $url, null, $options);
    }

    /**
     * @param callable $callback
     */
    public function setTokenCallback(callable $callback)
    {
        $this->tokenCallback = $callback;
    }

    /**
     * @inheritdoc
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new CronofyAccount($response);
    }

    /**
     * @return HandlerStack
     */
    private function getHandlerStack()
    {
        return $this->getHttpClient()->getConfig('handler');
    }
}
