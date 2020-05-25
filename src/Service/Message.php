<?php

namespace John\QiYeWechat\Service;

class Message extends Base
{
    protected static $type_url = 'externalcontact/';

    protected static $token_type = 'customer_token';

    protected $message_array = [];

    /**
     * @throws \John\QiYeWechat\Exception\QyWechatException
     *
     * @return Message
     */
    public static function init()
    {
        $client =  new self();

        $client->getToken(self::$token_type);

        return $client;
    }

    /**
     * 文本消息
     * @param $content
     * @return $this
     */
    public function text($content)
    {
        $this->message_array['text']['content'] = $content;

        return $this;
    }

    /**
     * 图片消息
     * @param $media_id
     * @return $this
     */
    public function image($media_id)
    {
        $this->message_array['image']['media_id'] = $media_id;

        return $this;
    }

    /**
     * 链接消息
     * @param $url
     * @param $title
     * @param string $desc
     * @param string $picture
     * @return $this
     */
    public function link($url, $title, $desc = '', $picture = '')
    {
        $this->message_array['link'] = [
            'url'   => $url,
            'title' => $title,
            'desc'  => $desc,
            'picurl'=> $picture,
        ];

        foreach ($this->message_array['link'] as $key=>$value) {
            if (!$value) {
                unset($this->message_array['link'][$key]);
            }
        }

        return $this;
    }

    /**
     * 小程序
     * @param $app_id
     * @param $title
     * @param $pic_media_id
     * @param $page
     * @return $this
     */
    public function miniProgram($app_id, $title, $pic_media_id, $page)
    {
        $this->message_array['miniprogram'] = [
            'appid'       => $app_id,
            'title'       => $title,
            'pic_media_id'=> $pic_media_id,
            'page'        => $page,
        ];

        return $this;
    }

    /**
     * 携带发送人
     * @param $user_id
     * @return $this
     */
    public function withSender($user_id)
    {
        $this->message_array['sender'] = $user_id;

        return $this;
    }

    /**
     * 指定用户发送
     * @param array $external_userids
     * @return array
     */
    public function sendToExternalUserids(array $external_userids)
    {
        $this->message_array['chat_type'] = 'single';

        $this->message_array['external_userid'] = $external_userids;

        return $this->send();
    }

    /**
     * 群组发送
     * @return array
     */
    public function sendToChatGroup()
    {
        $this->message_array['chat_type'] = 'group';

        return $this->send();
    }

    /**
     * 消息发送
     * @return array
     */
    private function send()
    {
        $action_url = 'add_msg_template';

        $url = self::$access_url . self::$type_url . $action_url . '?' . \http_build_query([
            'access_token'=> $this->token,
        ]);

        $array = $this->message_array;

        $this->message_array = [];

        return self::HttpRequest($url, $array, 'POST');
    }
}
