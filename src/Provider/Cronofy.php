<?php
namespace Geekdevs\OAuth2\Client\Provider;

use Geekdevs\OAuth2\Client\Criteria\CalendarCriteria;
use Geekdevs\OAuth2\Client\Criteria\CriteriaInterface;
use Geekdevs\OAuth2\Client\Criteria\FreeBusyCriteria;
use Geekdevs\OAuth2\Client\Criteria\ProfileCriteria;
use Geekdevs\OAuth2\Client\Cursor\CursorInterface;
use Geekdevs\OAuth2\Client\Cursor\PaginatedCursor;
use Geekdevs\OAuth2\Client\Hydrator\CalendarHydrator;
use Geekdevs\OAuth2\Client\Hydrator\FreeBusyHydrator;
use Geekdevs\OAuth2\Client\Hydrator\HydratorInterface;
use Geekdevs\OAuth2\Client\Hydrator\ProfileHydrator;
use Geekdevs\OAuth2\Client\Model\Account;
use Geekdevs\OAuth2\Client\Model\Calendar;
use Geekdevs\OAuth2\Client\Model\FreeBusy;
use Geekdevs\OAuth2\Client\Model\Profile;
use Geekdevs\OAuth2\Client\Util\UrlUtil;
use GuzzleHttp\HandlerStack;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;
use Somoza\Psr7\OAuth2Middleware;
use ArrayIterator;

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
     * @param AccessToken            $token
     *
     * @return Account
     */
    public function getAccount(AccessToken $token)
    {
        $response = $this->fetchResourceOwnerDetails($token);

        return new Account($response['account']);
    }

    /**
     * @param AccessToken            $token
     * @param ProfileCriteria|null   $criteria
     * @param HydratorInterface|null $hydrator
     *
     * @return ArrayIterator | Profile[]
     */
    public function getProfiles(
        AccessToken $token,
        ProfileCriteria $criteria = null,
        HydratorInterface $hydrator = null
    ) {
        $namespace = 'profiles';
        $hydrator = $hydrator ?: new ProfileHydrator();

        $request = $this->getNamespacedAuthenticatedRequest($namespace, $token, $criteria);
        $responseData = $this->getResponse($request);
        $profilesData = isset($responseData[$namespace]) ? $responseData[$namespace] : [];

        $result = [];
        foreach ($profilesData as $profileData) {
            $result[] = $hydrator->hydrate($profileData);
        }

        return new ArrayIterator($result);
    }

    /**
     * @param AccessToken            $token
     * @param CalendarCriteria|null  $criteria
     * @param HydratorInterface|null $hydrator
     *
     * @return ArrayIterator | Calendar[]
     */
    public function getCalendars(
        AccessToken $token,
        CalendarCriteria $criteria = null,
        HydratorInterface $hydrator = null
    ) {
        $namespace = 'calendars';
        $hydrator = $hydrator ?: new CalendarHydrator();

        $request = $this->getNamespacedAuthenticatedRequest($namespace, $token, $criteria);
        $responseData = $this->getResponse($request);
        $calendarsData = isset($responseData[$namespace]) ? $responseData[$namespace] : [];

        $result = [];
        foreach ($calendarsData as $calendarData) {
            $result[] = $hydrator->hydrate($calendarData);
        }

        return new ArrayIterator($result);
    }

    /**
     * @param AccessToken            $token
     * @param FreeBusyCriteria|null  $criteria
     * @param HydratorInterface|null $hydrator
     *
     * @return CursorInterface | FreeBusy[]
     */
    public function getFreeBusy(
        AccessToken $token,
        FreeBusyCriteria $criteria = null,
        HydratorInterface $hydrator = null
    ) {
        $namespace = 'free_busy';
        $hydrator = $hydrator ?: new FreeBusyHydrator();
        $request = $this->getNamespacedAuthenticatedRequest($namespace, $token, $criteria);

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

    /**
     * @param string                 $namespace
     * @param AccessToken            $token
     * @param CriteriaInterface|null $criteria
     *
     * @return \Psr\Http\Message\RequestInterface
     */
    protected function getNamespacedAuthenticatedRequest(
        $namespace,
        AccessToken $token,
        CriteriaInterface $criteria = null
    ) {
        if ($criteria) {
            $params = $criteria->toRaw();
            $qs = '?'.UrlUtil::createQueryString($params);
        } else {
            $qs = '';
        }

        return $this->getAuthenticatedRequest(
            'GET',
            self::BASE_RESOURCE_URL.'/'.$namespace.$qs,
            $token
        );
    }
}
