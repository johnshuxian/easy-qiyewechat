<?php

namespace John\QiYeWechat\Service;

use John\QiYeWechat\Exception\QyWechatException;

class GroupChat extends Base
{
    protected static $type_url = 'externalcontact/groupchat/';

    protected static $token_type = 'customer_token';

    private $group_array = [];

    /**
     * @throws QyWechatException
     *
     * @return GroupChat
     */
    public static function init()
    {
        $client =  new self();

        $client->getToken(self::$token_type);

        return $client;
    }

    /**
     * 获取客户群列表.
     *
     * @param int $page
     * @param int $page_size
     *
     * @return array
     */
    public function list($page = 1, $page_size = 100)
    {
        $action_url = 'list';

        $url = self::$access_url . self::$type_url . $action_url . '?' . \http_build_query([
            'access_token'=> $this->token,
        ]);

        $array = $this->group_array;

        $this->group_array = [];

        $array['offset'] = ($page - 1) * $page_size;

        $array['limit']  = $page_size;

        return self::HttpRequest($url, $array, 'POST');
    }

    /**
     * 设置客户群类型.
     *
     * @param $status:0 普通群;1 离职待继承;2 离职继承中;3 离职继承完成
     *
     * @throws QyWechatException
     *
     * @return $this
     */
    public function withStatus($status)
    {
        if (!in_array($status, [0, 1, 2, 3])) {
            throw new QyWechatException('Out of range: 0,1,2,3');
        }

        $this->group_array['status_filter'] = $status;

        return $this;
    }

    /**
     * 设置群用户id列表.
     *
     * @return $this
     */
    public function withUserIds(array $user_ids = [])
    {
        $this->group_array['owner_filter']['userid_list'] = $user_ids;

        return $this;
    }

    /**
     * 设置群部门id列表.
     *
     * @return $this
     */
    public function withDepartmentIds(array $department_ids = [])
    {
        $this->group_array['owner_filter']['partyid_list'] = $department_ids;

        return $this;
    }

    /**
     * 获取客户群详情.
     *
     * @param $chat_id
     *
     * @return array
     */
    public function get($chat_id)
    {
        $action_url = 'get';

        $url = self::$access_url . self::$type_url . $action_url . '?' . \http_build_query([
            'access_token'=> $this->token,
        ]);

        return self::HttpRequest($url, [
            'chat_id'=> $chat_id,
        ], 'POST');
    }

    /**
     * 离职人员群再分配
     * @param $take_over_user_id
     * @param array $chat_ids
     * @return array
     */
    public function transfer($take_over_user_id, array $chat_ids)
    {
        $action_url = 'transfer';

        $url = self::$access_url . self::$type_url . $action_url . '?' . \http_build_query([
            'access_token'=> $this->token,
        ]);

        return self::HttpRequest($url, [
            'chat_id_list'=> $chat_ids,
            'new_owner'   => $take_over_user_id,
        ], 'POST');
    }
}
