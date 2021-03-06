<?php
class TokenUtils
{
    const TOKENS_FILE_PATH = __DIR__.'/tokens.data';

    /**
     * @param \League\OAuth2\Client\Token\AccessToken $accessToken
     * @return boolean
     */
    public static function storeAccessToken(\League\OAuth2\Client\Token\AccessToken $accessToken)
    {
        $result = file_put_contents(self::TOKENS_FILE_PATH, json_encode($accessToken->jsonSerialize()) . "\n", FILE_APPEND);
        if (!$result) {
            throw new \RuntimeException(sprintf(
                "Could not store token into file %s - check file permissions",
                self::TOKENS_FILE_PATH
            ));
        }

        return $result;
    }

    /**
     * @return \League\OAuth2\Client\Token\AccessToken[]
     */
    public static function getStoredAccessTokens()
    {
        if (!file_exists(self::TOKENS_FILE_PATH)) {
            return [];
        }

        $accessTokens = [];
        $tokensRows = fopen(self::TOKENS_FILE_PATH, 'r');

        while ($tokenRow = fgets($tokensRows)) {
            $tokenData = json_decode($tokenRow, true);
            $accessTokens[] = new \League\OAuth2\Client\Token\AccessToken($tokenData);
        };

        return $accessTokens;
    }

    /**
     * @param $token
     * @return \League\OAuth2\Client\Token\AccessToken|null
     */
    public static function findStoredAccessToken($token)
    {
        $storedAccessTokens = self::getStoredAccessTokens();

        foreach ($storedAccessTokens as $accessToken) {
            if ($accessToken->getToken() === $token) {
                return $accessToken;
            }
        }

        return null;
    }

    /**
     * @param $token
     */
    public static function removeStoredAccessToken($token)
    {
        $storedAccessTokens = self::getStoredAccessTokens();
        foreach ($storedAccessTokens as $idx=>$accessToken) {
            if ($accessToken->getToken() === $token) {
                unset($storedAccessTokens[$idx]);
            }
        }

        //Store all tokens except deleted
        file_put_contents(self::TOKENS_FILE_PATH, '');
        foreach ($storedAccessTokens as $accessToken) {
            self::storeAccessToken($accessToken);
        }
    }
}
