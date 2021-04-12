<?php

namespace John\QiYeWechat;

use John\QiYeWechat\Exception\QyWechatException;
use John\QiYeWechat\Service\AppChat;
use John\QiYeWechat\Service\Crm;
use John\QiYeWechat\Service\Department;
use John\QiYeWechat\Service\GroupChat;
use John\QiYeWechat\Service\Member;
use John\QiYeWechat\Service\Message;
use John\QiYeWechat\Service\Tag;

class QiYeWechat
{
    /**
     * 外部联系人相关接口（除开群聊以及消息接口）.
     *
     * @throws \Exception
     *
     * @return Crm
     */
    public function crm()
    {
        return Crm::init();
    }

    /**
     * 清楚指定类型的token
     * @throws QyWechatException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public static function clearToken($token_action)
    {
        switch ($token_action) {
            case 'contacts_token':
                $corpid     = config('qy_wechat.qy_id');
                $cache_key  = 'qy_wechat:contacts_token_'.$corpid;

                break;
            case 'app_token':
                $corpid     = config('qy_wechat.qy_id');
                $cache_key  = 'qy_wechat:access_token_'.$corpid;

                break;
            case 'customer_token':
                $corpid     = config('qy_wechat.qy_id');
                $cache_key  = 'qy_wechat:customer_token_'.$corpid;

                break;
            default:
                throw new QyWechatException('unknow token type');
        }

        return \Cache::delete($cache_key);

    }

    /**
     * 成员标签接口.
     *
     * @throws \Exception
     *
     * @return Tag
     */
    public function tag()
    {
        return Tag::init();
    }

    /**
     * 公司部门接口.
     *
     * @throws \Exception
     *
     * @return Department
     */
    public function department()
    {
        return Department::init();
    }

    /**
     * 群聊接口.
     *
     * @throws Exception\QyWechatException
     *
     * @return GroupChat
     */
    public function groupchat()
    {
        return GroupChat::init();
    }

    /**
     * 公司成员相关接口.
     *
     * @throws \Exception
     *
     * @return Member
     */
    public function member()
    {
        return Member::init();
    }

    /**
     * 企业消息相关接口.
     *
     * @throws Exception\QyWechatException
     *
     * @return Message
     */
    public function message()
    {
        return Message::init();
    }

    /**
     * 应用消息推送
     * @return AppChat
     * @throws Exception\QyWechatException
     */
    public function appChat()
    {
        return AppChat::init();
    }
}
