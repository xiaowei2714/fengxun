<?php

namespace App\Modules\Base;

use Illuminate\Support\Facades\Redis;

class TokenBiz
{
    private $redis;
    private string $tokenPrefix;
    private string $unIdTokenPrefix;
    private string $refreshTokenPrefix;
    private $expireTime;
    private $refreshExpireTime;
    private int $queueLength = 30;

    private $accessToken;
    private $refreshToken;

    function __construct($projectName = '', $redisConnect = 'default')
    {
        $this->redis = Redis::connection($redisConnect);
        $this->tokenPrefix = $projectName !== '' ? $projectName . '_TOKEN_' : 'TOKEN_';
        $this->unIdTokenPrefix = $projectName !== '' ? $projectName . '_UN_' : 'UN_';
        $this->refreshTokenPrefix = $projectName !== '' ? $projectName . '_RT_' : 'RT_';
        $this->expireTime = config('service.params.token_expire_time');
        $this->refreshExpireTime = config('service.params.token_expire_time');
    }

    /**
     * 设置过期时间
     *
     * @param $expireTime
     * @return $this
     */
    public function setExpireTime($expireTime): TokenBiz
    {
        $this->expireTime = $expireTime;
        return $this;
    }

    /**
     * @param $refreshExpireTime
     * @return $this
     */
    public function setRefreshExpireTime($refreshExpireTime): TokenBiz
    {
        $this->refreshExpireTime = $refreshExpireTime;
        return $this;
    }

    /**
     * 设置可用token最大数量
     *
     * @param $queueLength
     * @return $this
     */
    public function setQueueLength($queueLength): TokenBiz
    {
        $this->queueLength = $queueLength;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param $accessToken
     * @return $this
     */
    public function setAccessToken($accessToken): TokenBiz
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * @param $refreshToken
     * @return $this
     */
    public function setRefreshToken($refreshToken): TokenBiz
    {
        $this->refreshToken = $refreshToken;
        return $this;
    }

    /**
     * 生成一个随机token
     *
     * @return string
     */
    private function genToken(): string
    {
        return md5(uniqid());
    }

    /**
     * 得到一个可使用的token
     *
     * @param $token
     * @return string
     */
    private function getCommonToken($token): string
    {
        return $this->tokenPrefix . $token;
    }

    /**
     * 得到一个唯一 token
     *
     * @param $token
     * @return string
     */
    private function getUnIdToken($token): string
    {
        return $this->unIdTokenPrefix . $token;
    }

    /**
     * 建立关系token数据
     *
     * @param $unId
     * @param $field
     * @return bool
     */
    private function genTokenRelation($unId, $field): bool
    {
        $unIdToken = $this->getUnIdToken($unId);

        $this->redis->rpush($unIdToken, [$field]);
        $this->redis->expire($unIdToken, ($this->expireTime + 200));

        return true;
    }

    /**
     * 返回关联token
     *
     * @param $unId
     * @return string[]
     */
    public function getTokenByUnId($unId): array
    {
        $unIdToken = $this->getUnIdToken($unId);
        return $this->redis->lrange($unIdToken, 0, $this->queueLength);
    }

    /**
     * 返回关联token
     *
     * @param $unId
     * @return string[]
     */
    public function getUnPrefixTokenByUnId($unId): array
    {
        $unIdToken = $this->getUnIdToken($unId);
        $data = $this->redis->lrange($unIdToken, 0, $this->queueLength);
        if (empty($data)) {
            return [];
        }

        foreach ($data as $key => $value) {
            $data[$key] = str_replace($this->tokenPrefix, '', $value);
        }

        return $data;
    }

    /**
     * 移除关系token数据
     *
     * @param $unId
     * @return bool
     */
    private function removeTokenRelation($unId): bool
    {
        $unIdToken = $this->getUnIdToken($unId);
        $tokenLength = $this->redis->llen($unIdToken);
        if (empty($tokenLength)) {
            return true;
        }

        $removeLength = $tokenLength - ($this->queueLength - 1);
        if ($removeLength > 0) {
            $removeTokenList = [];
            for ($i = 0; $i < $removeLength; $i++) {
                $removeTokenList[] = $this->redis->lpop($unIdToken);
            }

            $this->removeAccessToken($removeTokenList);
        }

        return true;
    }

    /**
     * 设置缓存
     *
     * @param array $data
     * @param null $unId
     * @return TokenBiz
     */
    public function genAccessToken(array $data = [], $unId = null): TokenBiz
    {
        if (!empty($unId)) {
            $this->removeTokenRelation($unId);
        }

        $token = $this->genToken();
        $useToken = $this->getCommonToken($token);
        $this->redis->setex($useToken, $this->expireTime, serialize($data));

        if (!empty($unId)) {
            $this->genTokenRelation($unId, $useToken);
        }

        $this->setAccessToken($token);
        return $this;
    }

    /**
     * 得到token
     *
     * @param $token
     * @return array|mixed
     */
    public function getAccessTokenData($token)
    {
        $useToken = $this->getCommonToken($token);
        $data = $this->redis->get($useToken);

        return !empty($data) ? unserialize($data) : [];
    }

    /**
     * 检查token是否存在
     *
     * @param $token
     * @return bool
     */
    public function checkAccessToken($token): bool
    {
        $res = $this->redis->exists($this->getCommonToken($token));
        return !empty($res);
    }

    /**
     * 删除token
     *
     * @param $token
     * @param $unId
     * @return bool
     */
    public function removeToken($token, $unId = null): bool
    {
        $token = $this->getCommonToken($token);
        $this->redis->del($token);
        if (!empty($unId)) {
            $tokenList = $this->getTokenByUnId($unId);
            $key = array_search($token, $tokenList);
            if ($key === false) {
                return true;
            }

            unset($tokenList[$key]);
        }

        return true;
    }

    /**
     * 删除token
     *
     * @param $token
     * @return bool
     */
    public function removeAccessToken($token): bool
    {
        $this->redis->del($token);
        return true;
    }

    /**
     * 移除绑定的token
     *
     * @param $unId
     * @return bool
     */
    public function removeUnIdToken($unId): bool
    {
        $tokens = $this->getTokenByUnId($unId);
        if (!empty($tokens)) {
            $this->removeAccessToken($tokens);
        }

        $this->redis->del($this->getUnIdToken($unId));
        return true;
    }

    /**
     * @param $managerCode
     * @return $this
     */
    public function genRefreshToken($managerCode): TokenBiz
    {
        $refreshToken = $this->genToken();
        $refreshToken = $this->refreshTokenPrefix . $refreshToken;
        $this->redis->setex($refreshToken, $this->refreshExpireTime, $managerCode);

        $this->setRefreshToken($refreshToken);
        return $this;
    }

    /**
     * @param $refreshToken
     * @return string|null
     */
    public function getRefreshTokenData($refreshToken): ?string
    {
        return $this->redis->get($refreshToken);
    }

    /**
     * @param $refreshToken
     * @return $this
     */
    public function delRefreshToken($refreshToken): TokenBiz
    {
        $this->redis->del($refreshToken);
        return $this;
    }
}
