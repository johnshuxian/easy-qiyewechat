<?php

namespace John\QiYeWechat\Service;

class AppChat extends Base
{
    protected static $type_url = 'message/';

    protected static $token_type = 'app_token';

    protected $message_array = [];

    /**
     *@throws \John\QiYeWechat\Exception\QyWechatException
     *
     * @return AppChat
     */
    public static function init()
    {
        $client =  new self();

        $client->getToken(self::$token_type);

        return $client;
    }

    /**
     * 发送给指定人.
     *
     * @param array|string $user_id
     *
     * @return $this
     */
    public function sendToUsers($user_id)
    {
        $user_ids = $this->params($user_id);

        $this->message_array['touser'] = $user_ids;

        return $this;
    }

    /**
     * 发送给指定部门.
     *
     * @param array|string $depart_id
     *
     * @return $this
     */
    public function sendToParts($depart_id)
    {
        $depart_id = $this->params($depart_id);

        $this->message_array['topart'] = $depart_id;

        return $this;
    }

    /**
     * 发送给指定标签的人.
     *
     * @param array|string $tag_id
     *
     * @return $this
     */
    public function sendToTags(array $tag_id)
    {
        $tag_id = $this->params($tag_id);

        $this->message_array['totag'] = $tag_id;

        return $this;
    }

    /**
     * 表示是否是保密消息，false表示可对外分享，true表示不能分享且内容显示水印，默认为false.
     *
     * @return $this
     */
    public function withSafe(bool $bool)
    {
        $this->message_array['safe'] = $bool ? 1 : 0;

        return $this;
    }

    /**
     * 表示是否开启id转译，默认false。仅第三方应用需要用到，企业自建应用可以忽略。
     *
     * @return $this
     */
    public function enableIdTrans(bool $bool)
    {
        $this->message_array['enable_id_trans'] = $bool ? 1 : 0;

        return $this;
    }

    /**
     * 表示是否开启并设置重复消息检查的时间间隔，默认不开启.
     *
     * @return $this
     */
    public function duplicateCheck(int $interval = 1800)
    {
        $this->message_array['enable_duplicate_check']   = 1;
        $this->message_array['duplicate_check_interval'] = $interval;

        return $this;
    }

    /**
     * 文本消息发送
     *
     * @param $content
     *
     * @return array
     */
    public function sendText($content)
    {
        $this->message_array['msgtype'] = 'text';

        $this->message_array['text']    = [
            'content'=> $content,
        ];

        return $this->send();
    }

    /**
     * 自定义发送消息
     * https://open.work.weixin.qq.com/api/doc/90000/90135/90236
     * @param $msg_type
     * @param array $content
     * @return array
     */
    public function sendDiy($msg_type,array $content)
    {
        $this->message_array['msgtype'] = $msg_type;

        $this->message_array[$msg_type] = $content;

        return $this->send();
    }

    /**
     * 发送对象处理.
     *
     * @param $params
     *
     * @return string
     */
    private function params($params)
    {
        return is_array($params) ? implode('|', $params) : $params;
    }

    /**
     * 消息发送
     *
     * @return array
     */
    private function send()
    {
        $action_url = 'send';

        $this->message_array['agentid'] = config('qy_wechat.app.agent_id');

        $url = self::$access_url . self::$type_url . $action_url . '?' . \http_build_query([
            'access_token'=> $this->token,
        ]);

        $array = $this->message_array;

        $this->message_array = [];

        return self::HttpRequest($url, $array, 'POST');
    }
}
