<?php
namespace Geekdevs\OAuth2\Client\Provider;

use Geekdevs\OAuth2\Client\Criteria\FreeBusyCriteria;
use Geekdevs\OAuth2\Client\Cursor\CursorInterface;
use Geekdevs\OAuth2\Client\Cursor\PaginatedCursor;
use Geekdevs\OAuth2\Client\Hydrator\FreeBusyHydrator;
use Geekdevs\OAuth2\Client\Hydrator\HydratorInterface;
use Geekdevs\OAuth2\Client\Model\FreeBusy;
use Geekdevs\OAuth2\Client\Util\UrlUtil;
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
    const BASE_RESOURCE_URL = 'https://api.cronofy.com/v1';
    const ACCESS_TOKEN_RESOURCE_OWNER_ID = 'account_id';

    use BearerAuthorizationTrait;

    /**
     * @var callable
     */
    protected $tokenCallback;


    /**
     * Builds request options used for requesting an access token.
     *
     * @param  array $params
     * @return array
     */
    protected function getAccessTokenOptions(array $params)
    {
        $options = [];

        if ($this->getAccessTokenMethod() === self::METHOD_POST) {
            $options['body'] = json_encode($params);
        }

        return $options;
    }


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
        return self::BASE_RESOURCE_URL.'/account';
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
     * @param FreeBusyCriteria  $criteria
     * @param AccessToken       $token
     * @param HydratorInterface $hydrator
     *
     * @return CursorInterface | FreeBusy[]
     */
    public function getFreeBusy(FreeBusyCriteria $criteria, AccessToken $token, HydratorInterface $hydrator = null)
    {
        $namespace = 'free_busy';

        $params = $criteria->toRaw();
        $qs = UrlUtil::createQueryString($params);

        /* Make a request */
        $request = $this->getAuthenticatedRequest(
            'GET',
            self::BASE_RESOURCE_URL.'/'.$namespace.'?'.$qs,
            $token
        );

        $hydrator = $hydrator ?: new FreeBusyHydrator();

        return new PaginatedCursor($namespace, $request, $this, $token, $hydrator);
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

    /**
     * @return array
     */
    protected function getDefaultHeaders()
    {
        return [
            'Content-Type' => 'application/json; charset=utf-8'
        ];
    }
}
