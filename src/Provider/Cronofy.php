<?php
namespace Geekdevs\OAuth2\Client\Provider;

use Geekdevs\OAuth2\Client\Criteria\CalendarCriteria;
use Geekdevs\OAuth2\Client\Criteria\CriteriaInterface;
use Geekdevs\OAuth2\Client\Criteria\EventCriteria;
use Geekdevs\OAuth2\Client\Criteria\FreeBusyCriteria;
use Geekdevs\OAuth2\Client\Criteria\ProfileCriteria;
use Geekdevs\OAuth2\Client\Cursor\CursorInterface;
use Geekdevs\OAuth2\Client\Cursor\PaginatedCursor;
use Geekdevs\OAuth2\Client\Hydrator\HydratorInterface;
use Geekdevs\OAuth2\Client\Hydrator\ObjectHydrator;
use Geekdevs\OAuth2\Client\Model\Account;
use Geekdevs\OAuth2\Client\Model\Calendar;
use Geekdevs\OAuth2\Client\Model\Event;
use Geekdevs\OAuth2\Client\Model\FreeBusy;
use Geekdevs\OAuth2\Client\Model\NotificationChannel;
use Geekdevs\OAuth2\Client\Model\Profile;
use Geekdevs\OAuth2\Client\Util\UrlUtil;
use GuzzleHttp\HandlerStack;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Somoza\OAuth2Middleware\OAuth2Middleware;
use Somoza\OAuth2Middleware\TokenService\Bearer;
use ArrayIterator;
use DateTime;

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
     * Revokes authorization
     * @see https://www.cronofy.com/developers/api/#revoke-authorization
     *
     * @param AccessToken $token
     * @throws IdentityProviderException
     */
    public function revokeToken(AccessToken $token)
    {
        $request = $this->createRequest(
            'POST',
            'https://api.cronofy.com/oauth/token/revoke',
            null,
            [
                'headers' => [
                    'Content-Type' => 'application/json; charset=utf-8',
                ],
                'body' => json_encode([
                    'client_id'     => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'token'         => $token->getRefreshToken() ?: $token->getToken(),
                ]),
            ]
        );

        $this->getParsedResponse($request);
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
        $bearerMiddleware = new OAuth2Middleware(
            new Bearer($this, $token, $this->tokenCallback)
        );

        $handlerStack = $this->getHandlerStack();
        $handlerStack->remove('access_token');
        $handlerStack->push($bearerMiddleware, 'access_token');

        return $this->createRequest($method, $url, null, $options);
    }

    /**
     * @param AccessToken $accessToken
     *
     * @return AccessToken
     */
    public function refreshAccessTokenIfExpired(AccessToken $accessToken)
    {
        if ($accessToken->hasExpired()) {
            $accessToken = $this->refreshAccessToken($accessToken);
        }

        return $accessToken;
    }

    /***
     * @param AccessToken $oldAccessToken
     *
     * @return AccessToken
     */
    public function refreshAccessToken(AccessToken $oldAccessToken)
    {
        $newAccessToken = $this->getAccessToken('refresh_token', [
            'refresh_token' => $oldAccessToken->getRefreshToken(),
        ]);

        //Add old resource owner id to new token (because resource owner id is not returned on token refresh)
        $newAccessToken = new AccessToken(
            ['resource_owner_id' => $oldAccessToken->getResourceOwnerId()] + $newAccessToken->jsonSerialize()
        );

        return $newAccessToken;
    }

    /**
     * @param AccessToken $token
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
        $criteria = $criteria ?: new ProfileCriteria([]);

        return $this->executeArrayRequest($token, 'profiles', Profile::class, $criteria, $hydrator);
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
        $criteria = $criteria ?: new CalendarCriteria([]);

        return $this->executeArrayRequest($token, 'calendars', Calendar::class, $criteria, $hydrator);
    }

    /**
     * @param AccessToken            $token
     * @param EventCriteria|null     $criteria
     * @param HydratorInterface|null $hydrator
     *
     * @return CursorInterface | Event[]
     */
    public function getEvents(
        AccessToken $token,
        EventCriteria $criteria = null,
        HydratorInterface $hydrator = null
    ) {
        return $this->executePaginatedRequest($token, 'events', Event::class, $criteria, $hydrator);
    }

    /**
     * Creates or updates event
     *
     * @param AccessToken $token
     * @param string      $uid
     * @param string      $calendarId
     * @param string      $summary
     * @param string      $description
     * @param DateTime    $startsAt
     * @param DateTime    $endsAt
     * @param array       $extra
     */
    public function persistEvent(
        AccessToken $token,
        $uid,
        $calendarId,
        $summary,
        $description,
        DateTime $startsAt,
        DateTime $endsAt,
        array $extra = []
    ) {
        $eventData = [
            'event_id'      => $uid,
            'summary'       => $summary,
            'description'   => $description,
            'start'         => $startsAt->format('c'),
            'end'           => $endsAt->format('c'),
        ] + $extra;

        $request = $this->getAuthenticatedRequest(
            'POST',
            self::BASE_RESOURCE_URL.'/calendars/'.$calendarId.'/events',
            $token,
            [
                'headers' => [
                    'Content-Type' => 'application/json; charset=utf-8',
                ],
                'body' => json_encode($eventData),
            ]
        );

        //Has no response body, will throw IdentityProviderException in case of error
        $this->getParsedResponse($request);
    }


    /**
     * @param AccessToken $token
     * @param string      $uid
     * @param string      $calendarId
     */
    public function deleteEvent(AccessToken $token, $uid, $calendarId)
    {
        $request = $this->getAuthenticatedRequest(
            'DELETE',
            self::BASE_RESOURCE_URL.'/calendars/'.$calendarId.'/events',
            $token,
            [
                'headers' => [
                    'Content-Type' => 'application/json; charset=utf-8',
                ],
                'body' => json_encode([
                    'event_id' => $uid,
                ]),
            ]
        );

        //Has no response body, will throw IdentityProviderException in case of error
        $this->getParsedResponse($request);
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
        $criteria = $criteria ?: new FreeBusyCriteria([]);

        return $this->executePaginatedRequest($token, 'free_busy', FreeBusy::class, $criteria, $hydrator);
    }

    /**
     * @param AccessToken $token
     * @param string      $callbackUrl
     *
     * @return string     ID of the created notificaiton channel
     */
    public function createNotificationChannel(AccessToken $token, $callbackUrl)
    {
        $request = $this->getAuthenticatedRequest(
            'POST',
            self::BASE_RESOURCE_URL.'/channels',
            $token,
            [
                'headers' => [
                    'Content-Type' => 'application/json; charset=utf-8',
                ],
                'body' => json_encode([
                    'callback_url' => $callbackUrl,
                ]),
            ]
        );

        //Will throw IdentityProviderException in case of error
        $response = $this->getParsedResponse($request);

        return isset($response['channel']['channel_id']) ? $response['channel']['channel_id'] : null;
    }

    /**
     * @param AccessToken $token
     *
     * @return ArrayIterator
     */
    public function getNotificationChannels(AccessToken $token)
    {
        return $this->executeArrayRequest($token, 'channels', NotificationChannel::class);
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
     * @param AccessToken            $token
     * @param string                 $namespace
     * @param string                 $class
     * @param CriteriaInterface|null $criteria
     * @param HydratorInterface|null $hydrator
     *
     * @return ArrayIterator
     */
    private function executeArrayRequest(
        AccessToken $token,
        $namespace,
        $class,
        CriteriaInterface $criteria = null,
        HydratorInterface $hydrator = null
    ) {
        $hydrator = $hydrator ?: new ObjectHydrator($class);
        $request = $this->getNamespacedAuthenticatedRequest($namespace, $token, $criteria);

        return $this->hydrateArrayCursor($namespace, $request, $hydrator);
    }

    /**
     * @param string            $namespace
     * @param RequestInterface  $request
     * @param HydratorInterface $hydrator
     *
     * @return ArrayIterator
     */
    private function hydrateArrayCursor($namespace, RequestInterface $request, HydratorInterface $hydrator)
    {
        $responseData = $this->getParsedResponse($request);
        $data = isset($responseData[$namespace]) ? $responseData[$namespace] : [];

        $result = [];
        foreach ($data as $row) {
            $result[] = $hydrator->hydrate($row);
        }

        return new ArrayIterator($result);
    }

    /**
     * @param AccessToken            $token
     * @param string                 $namespace
     * @param string                 $class
     * @param CriteriaInterface|null $criteria
     * @param HydratorInterface|null $hydrator
     *
     * @return PaginatedCursor
     */
    private function executePaginatedRequest(
        AccessToken $token,
        $namespace,
        $class,
        CriteriaInterface $criteria = null,
        HydratorInterface $hydrator = null
    ) {
        $hydrator = $hydrator ?: new ObjectHydrator($class);
        $request = $this->getNamespacedAuthenticatedRequest($namespace, $token, $criteria);

        return new PaginatedCursor($namespace, $request, $this, $token, $hydrator);
    }

    /**
     * @param string                 $namespace
     * @param AccessToken            $token
     * @param CriteriaInterface|null $criteria
     *
     * @return \Psr\Http\Message\RequestInterface
     */
    private function getNamespacedAuthenticatedRequest(
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
