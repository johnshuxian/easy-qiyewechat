<?php

namespace John\QiYeWechat\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;
use John\QiYeWechat\Exception\QyWechatException;

class Base
{
    protected $token;

    protected static $access_url = 'https://qyapi.weixin.qq.com/cgi-bin/';

    public static function HttpRequest($url, $params = [], $method = 'GET', $headers = [
        'content-type' => 'application/json',
    ])
    {
        try {
            $client = new Client();

            $option = [
                'verify'  => false,
                'timeout' => 5,
            ];
            if ($headers) {
                $option['headers'] = $headers;
            }

            if ('GET' === strtoupper($method)) {
                if ($params) {
                    if (is_array($params)) {
                        $params = http_build_query($params);
                    }
                    $url .= (strpos($url, '?') ? '&' : '?') . $params;
                    $response = $client->get($url, $option);
                } else {
                    $response = $client->get($url, $option);
                }
            } else {
                $option['body']        = json_encode($params, JSON_UNESCAPED_UNICODE);
                $response              = $client->post($url, $option);
            }

            return self::returnFormate(['code'=>1, 'data'=>json_decode($response->getBody()->getContents(), true)]);
        } catch (ClientException $clientException) {
            return ['code'=>0, 'data'=>$clientException->getMessage()];
        } catch (GuzzleException $e) {
            return ['code'=>0, 'data'=>$e->getMessage()];
        }
    }

    public static function returnFormate($info)
    {
        if (1 == $info['code']) {
            //接口响应正常
            if (isset($info['data']['errcode'])) {
                if (0 == $info['data']['errcode']) {
                    return ['code'=>1, 'data'=>$info['data']];
                }

                return ['code'=>0, 'data'=>$info['data']['errmsg']];
            }
        }

        //接口响应不正常
        throw new QyWechatException($info['data']);
    }

    public function getToken($token_action)
    {
        switch ($token_action) {
                case 'contacts_token':
                    $corpid     = config('qy_wechat.qy_id');
                    $corpsecret = config('qy_wechat.contacts_secrets');
                    $cache_key  = 'qy_wechat:contacts_token_'.$corpid;

                    break;
                case 'app_token':
                    $corpid     = config('qy_wechat.qy_id');
                    $corpsecret = config('qy_wechat.app.agent_secrets');
                    $cache_key  = 'qy_wechat:access_token_'.$corpid;

                    break;
                case 'customer_token':
                    $corpid     = config('qy_wechat.qy_id');
                    $corpsecret = config('qy_wechat.customer_token');
                    $cache_key  = 'qy_wechat:customer_token_'.$corpid;

                break;
                default:
                    throw new QyWechatException('unknow token type');
            }

        $this->fetchToken($corpid, $corpsecret, $cache_key);
    }

    protected function fetchToken($corpid, $corpsecret, $cache_key)
    {
        if ($token = Cache::get($cache_key)) {
            $this->token = $token;
        } else {
            $token_url = 'gettoken';

            $info = self::HttpRequest(self::$access_url . $token_url, [
                'corpid'    => $corpid,
                'corpsecret'=> $corpsecret,
            ]);

            if (1 == $info['code'] && 0 == $info['data']['errcode']) {
                Cache::add($cache_key, $info['data']['access_token'], $info['data']['expires_in'] - 10);

                $this->token = $info['data']['access_token'];
            } else {
                throw new \Exception($info['data'] ?? '未知故障');
            }
        }
    }
}
