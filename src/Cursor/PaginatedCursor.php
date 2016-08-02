<?php
namespace Geekdevs\OAuth2\Client\Cursor;

use Geekdevs\OAuth2\Client\Hydrator\ArrayHydrator;
use Geekdevs\OAuth2\Client\Hydrator\HydratorInterface;
use Geekdevs\OAuth2\Client\Provider\Cronofy;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\RequestInterface;

/**
 * Class PaginatedCursor
 * @package Geekdevs\OAuth2\Client\Cursor
 */
class PaginatedCursor implements CursorInterface
{
    /**
     * @var string
     */
    private $namespace;

    /**
     * @var RequestInterface
     */
    private $firstRequest;

    /**
     * @var RequestInterface
     */
    private $nextRequest;

    /**
     * @var Cronofy
     */
    private $cronofy;

    /**
     * @var AccessToken
     */
    private $token;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var array
     */
    private $currentDataSet = null;

    /**
     * @var int
     */
    private $currentPage = null;

    /**
     * @var int
     */
    private $totalPages = null;

    /**
     * @var int
     */
    private $idx = null;

    /**
     * PaginatedCursor constructor.
     *
     * @param                   $namespace
     * @param RequestInterface  $request
     * @param Cronofy           $cronofy
     * @param AccessToken       $token
     * @param HydratorInterface $hydrator
     */
    public function __construct(
        $namespace,
        RequestInterface $request,
        Cronofy $cronofy,
        AccessToken $token,
        HydratorInterface $hydrator = null
    ) {
        $this->namespace = $namespace;
        $this->cronofy = $cronofy;
        $this->token = $token;
        $this->firstRequest = $request;
        $this->hydrator = $hydrator ?: new ArrayHydrator();
        $this->rewind();
    }

    /**
     * @return bool|mixed
     */
    public function current()
    {
        $dataSet = $this->getCurrentDataSet();
        $current = $dataSet ? current($dataSet) : false;

        //Initialize index
        if ($current && $this->idx === null) {
            $this->idx = 0;
        }

        return $current ? $this->hydrate($current) : false;
    }

    /**
     * @return bool|mixed
     */
    public function next()
    {
        $next = $this->currentDataSet ? next($this->currentDataSet) : false;

        if ($next === false) {
            $dataSet = $this->getNextDataSet();
            if ($dataSet !== null) {
                $next = current($dataSet);
            }
        }

        if ($next) {
            $this->idx = ($this->idx===null) ? 0 : ($this->idx+1);
            $next = $this->hydrate($next);
        } else {
            $next = false;
        }

        return $next;
    }

    /**
     * @return int
     */
    public function key()
    {
        $this->getCurrentDataSet();

        return $this->idx;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return $this->getCurrentDataSet() ? true : false;
    }

    /**
     * Rewind
     */
    public function rewind()
    {
        $this->idx = null;
        $this->currentDataSet = null;
        $this->nextRequest = $this->firstRequest;
    }

    /**
     * @return array
     */
    protected function getCurrentDataSet()
    {
        if ($this->currentDataSet === null) {
            //Lazy-load data set for the first call
            $this->currentDataSet = $this->getNextDataSet();
        }

        return $this->currentDataSet;
    }

    /**
     * @return array|null
     */
    private function getNextDataSet()
    {
        if (!$this->nextRequest) {
            return null;
        }

        $responseData = $this->cronofy->getResponse($this->nextRequest);

        $pageData = $responseData['pages'] + [
            'current'   => null,
            'total'     => null,
            'next_page' => null,
        ];

        $this->currentPage = $pageData['current'] ? (int)$pageData['current'] : null;
        $this->totalPages = $pageData['total'] ? (int)$pageData['total'] : null;

        if ($pageData['next_page']) {
            $this->nextRequest = $this->cronofy->getAuthenticatedRequest(
                'GET',
                $pageData['next_page'],
                $this->token
            );
        } else {
            $this->nextRequest = null;
        }

        return isset($responseData[$this->namespace]) ? $responseData[$this->namespace] : null;
    }

    /**
     * @param array $row
     *
     * @return array
     */
    private function hydrate(array $row)
    {
        return $this->hydrator->hydrate($row);
    }
}
