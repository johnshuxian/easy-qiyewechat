<?php

namespace John\QiYeWechat;

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
}
