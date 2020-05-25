<?php

namespace John\QiYeWechat\Service;

use John\QiYeWechat\Exception\QyWechatException;

class Tag extends Base
{
    protected static $type_url = 'tag/';

    protected static $token_type = "contacts_token";

    /**
     * 初始化部门管理服务.
     *
     * @throws \Exception
     *
     * @return Tag
     */
    public static function init()
    {
        $client =  new self();

        $client->getToken(self::$token_type);

        return $client;
    }

    /**
     * 创建标签.
     *
     * @param $tag_name
     * @param int $tag_id
     *
     * @return array
     */
    public function createTag($tag_name, $tag_id = 0)
    {
        $action_url = 'create';

        $url = self::$access_url . self::$type_url . $action_url . '?' . \http_build_query([
            'access_token'=> $this->token,
        ]);

        $array['tagname'] = $tag_name;

        if ($tag_id) {
            $array['tagid'] = $tag_id;
        }

        return self::HttpRequest($url, $array, 'POST');
    }

    /**
     * 编辑标签.
     *
     * @param $tag_id
     * @param $tag_name
     *
     * @return array
     */
    public function editTag($tag_id, $tag_name)
    {
        $action_url = 'update';

        $url = self::$access_url . self::$type_url . $action_url . '?' . \http_build_query([
            'access_token'=> $this->token,
        ]);

        return self::HttpRequest($url, [
            'tagid'  => $tag_id,
            'tagname'=> $tag_name,
        ], 'POST');
    }

    /**
     * 删除标签.
     *
     * @param $tag_id
     *
     * @return array
     */
    public function deleteTag($tag_id)
    {
        $action_url = 'delete';

        $url        = self::$access_url . self::$type_url . $action_url . '?' . \http_build_query([
            'access_token'=> $this->token,
            'tagid'       => $tag_id,
        ]);

        return self::HttpRequest($url);
    }

    /**
     * 获取应用可见范围的标签成员.
     *
     * @param $tag_id
     *
     * @return array
     */
    public function getTagMember($tag_id)
    {
        $action_url = 'get';

        $url        = self::$access_url . self::$type_url . $action_url . '?' . \http_build_query([
            'access_token'=> $this->token,
            'tagid'       => $tag_id,
        ]);

        return self::HttpRequest($url);
    }

    /**
     * 添加标签成员.
     *
     * @param $tag_id
     * @param array $user_ids:成员id
     * @param array $department_ids:部门id
     *
     * @throws QyWechatException
     *
     * @return array
     */
    public function addTagMembers($tag_id, array $user_ids = [], array $department_ids = [])
    {
        if (empty($user_ids) && empty($department_ids)) {
            throw new QyWechatException('user_ids and department_ids cannot be empty at the same time');
        }

        $action_url = 'addtagusers';

        $url        = self::$access_url . self::$type_url . $action_url . '?' . \http_build_query([
            'access_token'=> $this->token,
        ]);

        $array['tagid'] = $tag_id;

        $array['partylist'] = $department_ids;

        $array['userlist'] = $user_ids;

        return self::HttpRequest($url, $array, 'POST');
    }

    /**
     * 删除标签成员.
     *
     * @param $tag_id
     * @param array $user_ids:待删除成员id
     * @param array $department_ids:待删除部门id
     *
     * @throws QyWechatException
     *
     * @return array
     */
    public function delTagMembers($tag_id, array $user_ids = [], array $department_ids = [])
    {
        if (empty($user_ids) && empty($department_ids)) {
            throw new QyWechatException('user_ids and department_ids cannot be empty at the same time');
        }

        $action_url = 'deltagusers';

        $url        = self::$access_url . self::$type_url . $action_url . '?' . \http_build_query([
            'access_token'=> $this->token,
        ]);

        $array['tagid'] = $tag_id;

        $array['partylist'] = $department_ids;

        $array['userlist'] = $user_ids;

        return self::HttpRequest($url, $array, 'POST');
    }

    /**
     * 获取标签列表.
     *
     * @return array
     */
    public function list()
    {
        $action_url = 'list';

        $url        = self::$access_url . self::$type_url . $action_url . '?' . \http_build_query([
            'access_token'=> $this->token,
        ]);

        return self::HttpRequest($url);
    }
}
