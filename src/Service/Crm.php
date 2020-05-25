<?php

namespace John\QiYeWechat\Service;

use John\QiYeWechat\Exception\QyWechatException;

class Crm extends Base
{
    protected static $type_url = 'externalcontact/';

    protected static $token_type = 'customer_token';

    protected $contact_field = ['type', 'scene', 'style', 'remark', 'skip_verify', 'state', 'user', 'party', 'is_temp', 'expires_in', 'chat_expires_in', 'unionid', 'conclusions'];

    private $tag_array = [];

    /**
     * 初始化部门管理服务.
     *
     * @throws \Exception
     *
     * @return Crm
     */
    public static function init()
    {
        $client =  new self();

        $client->getToken(self::$token_type);

        return $client;
    }

    /**
     * 获取配置了客户联系功能的成员列表.
     *
     * @return array
     */
    public function getFollowUserList()
    {
        $action_url = 'get_follow_user_list';

        $url = self::$access_url . self::$type_url . $action_url . '?' . \http_build_query([
            'access_token'=> $this->token,
        ]);

        return self::HttpRequest($url);
    }

    /**
     * type	必填	联系方式类型,1-单人, 2-多人
     * scene 必填 场景，1-在小程序中联系，2-通过二维码联系
     * style 必填 在小程序中联系时使用的控件样式，详见附表
     * remark 必填 联系方式的备注信息，用于助记，不超过30个字符
     * skip_verify 非必填 外部客户添加时是否无需验证，默认为true
     * state 非必填 企业自定义的state参数，用于区分不同的添加渠道，在调用“获取外部联系人详情”时会返回该参数值，不超过30个字符
     * user	非必填 使用该联系方式的用户userID列表，在type为1时为必填，且只能有一个
     * party 非必填 使用该联系方式的部门id列表，只在type为2时有效
     * is_temp 非必填 是否临时会话模式，true表示使用临时会话模式，默认为false
     * expires_in 非必填	临时会话二维码有效期，以秒为单位。该参数仅在is_temp为true时有效，默认7天
     * chat_expires_in	非必填 临时会话有效期，以秒为单位。该参数仅在is_temp为true时有效，默认为添加好友后24小时
     * unionid	非必填	可进行临时会话的客户unionid，该参数仅在is_temp为true时有效，如不指定则不进行限制
     * conclusions	非必填	结束语，会话结束时自动发送给客户，可参考“结束语定义”，仅在is_temp为true时有效.
     */
    public function addContactWay(array $array)
    {
        $action_url = 'add_contact_way';

        if (array_diff(array_keys($array), $this->contact_field)) {
            throw new QyWechatException('Contains unknown parameters');
        }

        $url = self::$access_url . self::$type_url . $action_url . '?' . \http_build_query([
            'access_token'=> $this->token,
        ]);

        return self::HttpRequest($url, $array, 'POST');
    }

    /**
     * 获取企业配置的联系我方式.
     *
     * @param $config_id
     *
     * @return array
     */
    public function getContactWay($config_id)
    {
        $action_url = 'get_contact_way';

        $url = self::$access_url . self::$type_url . $action_url . '?' . \http_build_query([
            'access_token'=> $this->token,
        ]);

        return self::HttpRequest($url, [
            'config_id'=> $config_id,
        ], 'POST');
    }

    /**
     * 更新[联系我]方式.
     *
     * @param $config_id
     * @param $array
     *
     * @return array
     */
    public function updateContactWay($config_id, $array)
    {
        $action_url = 'update_contact_way';

        $url = self::$access_url . self::$type_url . $action_url . '?' . \http_build_query([
            'access_token'=> $this->token,
        ]);

        $array['config_id'] = $config_id;

        return self::HttpRequest($url, $array, 'POST');
    }

    /**
     * 删除[联系我方式].
     *
     * @param $config_id
     *
     * @return array
     */
    public function delContactWay($config_id)
    {
        $action_url = 'del_contact_way';

        $url = self::$access_url . self::$type_url . $action_url . '?' . \http_build_query([
            'access_token'=> $this->token,
        ]);

        return self::HttpRequest($url, [
            'config_id'=> $config_id,
        ], 'POST');
    }

    /**
     * 结束临时会话.
     *
     * @param $user_id
     * @param $external_userid
     *
     * @return array
     */
    public function closeTempChat($user_id, $external_userid)
    {
        $action_url = 'close_temp_chat';

        $url = self::$access_url . self::$type_url . $action_url . '?' . \http_build_query([
            'access_token'=> $this->token,
        ]);

        return self::HttpRequest($url, [
            'userid'         => $user_id,
            'external_userid'=> $external_userid,
        ], 'POST');
    }

    /**
     * 获取配置了外部联系功能的指定员工的外部联系人.
     *
     * @param $user_id
     *
     * @return array
     */
    public function customerList($user_id)
    {
        $action_url = 'list';

        $url = self::$access_url . self::$type_url . $action_url . '?' . \http_build_query([
            'access_token'=> $this->token,
            'userid'      => $user_id,
        ]);

        return self::HttpRequest($url);
    }

    /**
     * 获取外部联系人详情.
     *
     * @param $external_userid
     *
     * @return array
     */
    public function get($external_userid)
    {
        $action_url = 'get';

        $url = self::$access_url . self::$type_url . $action_url . '?' . \http_build_query([
            'access_token'   => $this->token,
            'external_userid'=> $external_userid,
        ]);

        return self::HttpRequest($url);
    }

    /**
     * 修改客户备注信息.
     *
     * @param $user_id
     * @param $external_userid
     * @param array $remark:客户备注
     *
     * @return array
     */
    public function remark($user_id, $external_userid, array $remark=[
        'remark'            => '',
        'description'       => '',
        'remark_company'    => '',
        'remark_mobiles'    => [],
        'remark_pic_mediaid'=> '',
    ])
    {
        $action_url = 'remark';

        $url = self::$access_url . self::$type_url . $action_url . '?' . \http_build_query([
            'access_token'   => $this->token,
        ]);

        $remark['userid']          = $user_id;
        $remark['external_userid'] = $external_userid;

        return self::HttpRequest($url, $remark, 'POST');
    }

    /**
     * 客户标签详情.
     *
     * @param array $tag_ids
     *
     * @return array
     */
    public function getCorpTagList($tag_ids = [])
    {
        $action_url = 'get_corp_tag_list';

        $url = self::$access_url . self::$type_url . $action_url . '?' . \http_build_query([
            'access_token'   => $this->token,
        ]);

        return self::HttpRequest($url, [
            'tag_id'=> $tag_ids,
        ], 'POST');
    }

    /**
     * 设置客户标签的标签组属性（后期调用confirmTag创建）.
     *
     * @param int    $group_id
     * @param string $group_name
     * @param int    $group_order
     *
     * @throws QyWechatException
     *
     * @return $this
     */
    public function setTagGroup($group_id = 0, $group_name = '', $group_order = 0)
    {
        if (!($group_id || $group_name)) {
            throw new QyWechatException('group_id, group_name cannot be empty at the same time');
        }

        $array = [];

        if ($group_name) {
            $array['group_name'] = $group_name;
        }

        if ($group_order) {
            $array['order'] = $group_order;
        }

        if ($group_id) {
            $array['group_id'] = $group_id;
        }

        $this->tag_array = $array;

        return $this;
    }

    /**
     * 赋予tag属性（后期调用confirmTag创建）.
     *
     * @param $tag_name
     * @param int $tag_order
     *
     * @return $this
     */
    public function setTag($tag_name, $tag_order = 0)
    {
        $this->tag_array['tag'][] = [
            'name' => $tag_name,
            'order'=> $tag_order,
        ];

        return $this;
    }

    /**
     * 批量赋予tag属性（后期调用confirmTag创建）.
     *
     * @return $this
     */
    public function setTags(array $tag_arrays)
    {
        if ($this->tag_array['tag']) {
            $this->tag_array['tag'] = array_merge($this->tag_array['tag'], $tag_arrays);
        } else {
            $this->tag_array['tag'] = $tag_arrays;
        }

        return $this;
    }

    /**
     * 创建外部联系人tag标签.
     *
     * @throws QyWechatException
     *
     * @return array
     */
    public function confirmTag()
    {
        if (!$this->tag_array) {
            throw new QyWechatException('Give label properties using the setTagGroup and setTag methods');
        }

        $action_url = 'add_corp_tag';

        $url = self::$access_url . self::$type_url . $action_url . '?' . \http_build_query([
            'access_token'   => $this->token,
        ]);

        $array = $this->tag_array;

        $this->tag_array = [];

        return self::HttpRequest($url, $array, 'POST');
    }

    /**
     * 编辑客户标签/标签组名称或者次序值
     *
     * @param $tag_or_group_id
     * @param $name
     * @param string $order
     *
     * @return array
     */
    public function editTag($tag_or_group_id, $name = '', $order = '')
    {
        $action_url = 'edit_corp_tag';

        $array['id']   = $tag_or_group_id;

        if ('' !== $name) {
            $array['name'] = $name;
        }

        if ('' !== $order) {
            $array['order']=$order;
        }

        $url = self::$access_url . self::$type_url . $action_url . '?' . \http_build_query([
            'access_token'   => $this->token,
        ]);

        return self::HttpRequest($url, $array, 'POST');
    }

    /**
     * 删除客户标签或者标签组.
     *
     * @param array $tag_ids
     * @param array $group_ids
     *
     * @return array
     */
    public function deleteTagsOrTagGroup($tag_ids = [], $group_ids = [])
    {
        $action_url = 'del_corp_tag';

        $url = self::$access_url . self::$type_url . $action_url . '?' . \http_build_query([
            'access_token'   => $this->token,
        ]);

        $array = [];

        $array['tag_id']    = $tag_ids;

        $array['group_id']  = $group_ids;

        return self::HttpRequest($url, $array, 'POST');
    }

    /**
     * 编辑客户企业标签.
     *
     * @param $user_id
     * @param $external_userid
     * @param array $add_tag_ids
     * @param array $remove_tag_ids
     *
     * @return array
     */
    public function markTag($user_id, $external_userid, $add_tag_ids = [], $remove_tag_ids =[])
    {
        $action_url = 'mark_tag';

        $url = self::$access_url . self::$type_url . $action_url . '?' . \http_build_query([
            'access_token'   => $this->token,
        ]);

        $array['userid']          = $user_id;
        $array['external_userid'] = $external_userid;
        $array['add_tag']         = $add_tag_ids;
        $array['remove_tag']      = $remove_tag_ids;

        return self::HttpRequest($url, $array, 'POST');
    }

    /**
     * 获取离职成员的客户列表.
     *
     * @param $page:当前页
     * @param $page_size:每页数量
     *
     * @return array
     */
    public function getUnassignedList($page = 1, $page_size = 100)
    {
        $action_url = 'get_unassigned_list';

        $url = self::$access_url . self::$type_url . $action_url . '?' . \http_build_query([
            'access_token'   => $this->token,
        ]);

        return self::HttpRequest($url, [
            'page_id'  => $page - 1,
            'page_size'=> $page_size,
        ], 'POST');
    }

    /**
     * 离职成员的外部联系人再分配.
     *
     * @param $external_userid
     * @param $handover_userid
     * @param $takeover_userid
     *
     * @return array
     */
    public function transfer($external_userid, $handover_userid, $takeover_userid)
    {
        $action_url = 'transfer';

        $url = self::$access_url . self::$type_url . $action_url . '?' . \http_build_query([
            'access_token'   => $this->token,
        ]);

        return self::HttpRequest($url, [
            'external_userid'=> $external_userid,
            'handover_userid'=> $handover_userid,
            'takeover_userid'=> $takeover_userid,
        ], 'POST');
    }
}
