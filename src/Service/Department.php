<?php

namespace John\QiYeWechat\Service;

use John\QiYeWechat\Exception\QyWechatException;

class Department extends Base
{
    protected static $type_url = 'department/';

    protected static $token_type = "contacts_token";

    /**
     * 初始化部门管理服务.
     *
     *@return Department
     *@throws \Exception
     *
     */
    public static function init()
    {
        $client =  new self();

        $client->getToken(self::$token_type);

        return $client;
    }

    /**
     * 创建部门.
     *
     * @param $name
     * @param $parent_id
     * @param int    $id
     * @param int    $order
     * @param string $name_en
     *
     * @return array
     */
    public function createDepartment($name, $parent_id, $id = 0, $order= 0, $name_en = '')
    {
        $action_url = 'create';

        $url = self::$access_url . self::$type_url . $action_url . '?' . \http_build_query([
            'access_token'=> $this->token,
        ]);

        $array = [];

        $array['name']        = $name;
        $array['parentid']    = $parent_id;
        if ($id) {
            $array['id']          = $id;
        }
        if ($order) {
            $array['order']       = $order;
        }
        if ($name_en) {
            $array['name_en']     = $name_en;
        }

        return self::HttpRequest($url, $array, 'POST');
    }

    /**
     * 修改部门信息.
     *
     * @param $department_id
     *
     * @throws QyWechatException
     *
     * @return array
     */
    public function editDepartment($department_id, array $array)
    {
        $field = [
            'id',
            'name',
            'name_en',
            'parentid',
            'order',
        ];

        if (\array_diff(array_keys($array), $field)) {
            throw new QyWechatException('The elements currently supporting modification are:' . \implode(',', $field));
        }

        $array['id'] = $department_id;

        $action_url = 'update';

        $url = self::$access_url . self::$type_url . $action_url . '?' . \http_build_query([
            'access_token'=> $this->token,
        ]);

        return self::HttpRequest($url, $array, 'POST');
    }

    /**
     * 删除部门.
     *
     * @param $department_id
     *
     * @return array
     */
    public function deleteDepartment($department_id)
    {
        $action_url = 'delete';

        $url = self::$access_url . self::$type_url . $action_url . '?' . \http_build_query([
            'access_token'=> $this->token,
            'id'          => $department_id,
        ]);

        return self::HttpRequest($url);
    }

    /**
     * 获取部门列表 不传department_id时拉取全量组织架构.
     *
     * @param int $department_id
     *
     * @return array
     */
    public function list($department_id = 0)
    {
        $action_url = 'list';

        $url = self::$access_url . self::$type_url . $action_url . '?' . \http_build_query(
            [
                'access_token'=> $this->token,
                'id'          => $department_id,
            ]
        );

        return self::HttpRequest($url);
    }
}
