<?php

namespace John\QiYeWechat\Service;

use Carbon\Carbon;
use John\QiYeWechat\Exception\QyWechatException;

class Member extends Base
{
    protected $array = [];

    protected static $type_url = 'user/';

    protected static $token_type = "contacts_token";

    /**
     * 创建员工时，可以填写的参数
     * userid	是	成员UserID。对应管理端的帐号，企业内必须唯一。不区分大小写，长度为1~64个字节。只能由数字、字母和“_-@.”四种字符组成，且第一个字符必须是数字或字母。
     * name	是	成员名称。长度为1~64个utf8字符
     * alias	否	成员别名。长度1~32个utf8字符
     * mobile	否	手机号码。企业内必须唯一，mobile/email二者不能同时为空
     * department	否	成员所属部门id列表,不超过20个
     * order	否	部门内的排序值，默认为0，成员次序以创建时间从小到大排列。个数必须和参数department的个数一致，数值越大排序越前面。有效的值范围是[0, 2^32)
     * position	否	职务信息。长度为0~128个字符
     * gender	否	性别。1表示男性，2表示女性
     * email	否	邮箱。长度6~64个字节，且为有效的email格式。企业内必须唯一，mobile/email二者不能同时为空
     * telephone	否	座机。32字节以内，由纯数字或’-‘号组成。
     * is_leader_in_dept	否	个数必须和参数department的个数一致，表示在所在的部门内是否为上级。1表示为上级，0表示非上级。在审批等应用里可以用来标识上级审批人
     * avatar_mediaid	否	成员头像的mediaid，通过素材管理接口上传图片获得的mediaid
     * enable	否	启用/禁用成员。1表示启用成员，0表示禁用成员
     * extattr	否	自定义字段。自定义字段需要先在WEB管理端添加，见扩展属性添加方法，否则忽略未知属性的赋值。与对外属性一致，不过只支持type=0的文本和type=1的网页类型，详细描述查看对外属性
     * to_invite	否	是否邀请该成员使用企业微信（将通过微信服务通知或短信或邮件下发邀请，每天自动下发一次，最多持续3个工作日），默认值为true。
     * external_profile	否	成员对外属性，字段详情见对外属性
     * external_position	否	对外职务，如果设置了该值，则以此作为对外展示的职务，否则以position来展示。长度12个汉字内
     * address	否	地址。长度最大128个字符
     * main_department	否	主部门.
     *
     * @var string[]
     */
    private $field_create = [
        'name', 'userid', 'alias', 'mobile', 'department', 'order', 'position', 'gender', 'email', 'telephone', 'is_leader_in_dept', 'avatar_mediaid', 'enable', 'extattr', 'to_invite', 'external_profile', 'external_position', 'address', 'main_department',
    ];

    /**
     * 设置成员属性.
     *
     * @param $name
     * @param $value
     *
     * @throws QyWechatException
     *
     * @return $this
     */
    public function __set($name, $value)
    {
        // TODO: Implement __set() method.

        if (!in_array($name, $this->field_create)) {
            throw new QyWechatException("the {$name} does not exists!");
        }

        $this->array[$name] = $value;

        return $this;
    }

    /**
     * 初始化成员管理.
     *
     * @return Member
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
     * 添加成员.
     *
     * @throws QyWechatException
     *
     * @return array
     */
    public function createMember()
    {
        $must_type = [
            'name',
            'mobile',
            'department',
            'userid',
        ];

        if (4 != count(array_intersect($must_type, array_keys($this->array)))) {
            throw new QyWechatException(implode(',', $must_type) . ' is required');
        }

        $action_url = 'create';

        $url = self::$access_url . self::$type_url . $action_url . '?' . http_build_query([
            'access_token'=> $this->token,
        ]);

        $array = $this->array;

        $this->array = [];

        return self::HttpRequest($url, $array, 'POST');
    }

    /**
     * 获取成员.
     *
     * @param $user_id
     *
     * @return array
     */
    public function getMemberById($user_id)
    {
        $action_url = 'get';

        $url = self::$access_url . self::$type_url . $action_url . '?' . http_build_query([
            'access_token'=> $this->token,
            'userid'      => $user_id,
        ]);

        return self::HttpRequest($url);
    }

    /**
     * 编辑成员属性.
     *
     * @param $user_id
     *
     * @throws QyWechatException
     *
     * @return array
     */
    public function editMemberById($user_id, array $array)
    {
        $user = $this->getMemberById($user_id);

        if (is_array($user['data'])) {
            $user_detail = $user['data'];
        } else {
            throw new QyWechatException($user['data']);
        }

        $array = array_merge($user_detail, $array);

        $action_url = 'update';

        $url = self::$access_url . self::$type_url . $action_url . '?' . http_build_query([
            'access_token'=> $this->token,
        ]);

        return self::HttpRequest($url, $array, 'POST');
    }

    /**
     * 删除单个成员.
     *
     * @param $user_id
     *
     * @return array
     */
    public function deleteMemberByUserId($user_id)
    {
        $action_url = 'delete';

        $url = self::$access_url . self::$type_url . $action_url . '?' . http_build_query([
            'access_token'=> $this->token,
            'userid'      => $user_id,
        ]);

        return self::HttpRequest($url);
    }

    /**
     * 批量删除.
     *
     * @return array
     */
    public function batchDelete(array $user_id_list)
    {
        $action_url = 'batchdelete';

        $url = self::$access_url . self::$type_url . $action_url . '?' . http_build_query([
            'access_token'=> $this->token,
        ]);

        return self::HttpRequest($url, [
            'useridlist'=> $user_id_list,
        ], 'POST');
    }

    /**
     * 获取部门成员列表.
     *
     * @param $department_id
     * @param bool $with_detail: 是否获取部门成员详情
     * @param bool $fetch_child  : 是否递归获取子部门下面的成员：true-递归获取，false-只获取本部门
     *
     * @return array
     */
    public function departmentMemberLists($department_id, $with_detail = false, bool $fetch_child = false)
    {
        $action_url = $with_detail ? 'list' : 'simplelist';

        $url = self::$access_url . self::$type_url . $action_url . '?' . http_build_query([
            'access_token' => $this->token,
            'department_id'=> $department_id,
            'fetch_child'  => $fetch_child ? 1 : 0,
        ]);

        return self::HttpRequest($url);
    }

    /**
     * 二次验证时，允许员工加入企业.
     *
     * @param $user_id
     *
     * @return array
     */
    public function authsucc($user_id)
    {
        $action_url = 'authsucc';

        $url = self::$access_url . self::$type_url . $action_url . '?' . http_build_query([
            'access_token' => $this->token,
            'user_id'      => $user_id,
        ]);

        return self::HttpRequest($url);
    }

    /**
     * 批量邀请成员使用企业微信
     *
     * @throws QyWechatException
     *
     * @return array
     */
    public function invite(array $user_ids = [], array $department_ids = [], array $tag_ids = [])
    {
        if (count($user_ids) > 1000) {
            throw new QyWechatException('The number of userids must not exceed 1000');
        }

        if (count($user_ids) > 100) {
            throw new QyWechatException('The number of department_id must not exceed 100');
        }

        if (count($user_ids) > 100) {
            throw new QyWechatException('The number of tag_id must not exceed 100');
        }

        $action_url = 'invite';

        $url = self::$access_url . 'batch/' . $action_url . '?' . http_build_query([
            'access_token' => $this->token,
        ]);

        $arr = [];

        if ($user_ids) {
            $arr['user'] = $user_ids;
        }
        if ($department_ids) {
            $arr['party'] = $department_ids;
        }
        if ($tag_ids) {
            $arr['tag'] = $tag_ids;
        }

        if (!$arr) {
            throw new QyWechatException('user_ids,department_ids,tag_ids最少要填写一个');
        }

        return self::HttpRequest($url, $arr, 'POST');
    }

    /**
     * 获取企业微信加入二维码
     *
     * @param int $size_type:尺寸类型，1: 171 x 171; 2: 399 x 399; 3: 741 x 741; 4: 2052 x 2052
     *
     * @throws QyWechatException
     *
     * @return array
     */
    public function makeJoinQr($size_type = 1)
    {
        $types = [
            1, 2, 3, 4,
        ];

        if (!in_array($size_type, $types)) {
            throw new QyWechatException('Unknown QR code size');
        }

        $action_url = 'get_join_qrcode';

        $url = self::$access_url . 'corp/' . $action_url . '?' . http_build_query([
            'access_token'   => $this->token,
            'size_type'      => $size_type,
        ]);

        return self::HttpRequest($url);
    }

    /**
     * 获取手机号随机串，该随机串可直接在企业微信终端搜索手机号对应的微信用户.
     *
     * @param $mobile
     * @param string $state
     *
     * @return array
     */
    public function getMobileHashcode($mobile, $state='')
    {
        $action_url = 'get_mobile_hashcode';

        $url = self::$access_url . self::$type_url . $action_url . '?' . http_build_query([
            'access_token' => $this->token,
        ]);

        $array = [];

        $array['mobile'] = $mobile;

        if ($state) {
            $array['state'] = $state;
        }

        return self::HttpRequest($url, $array, 'POST');
    }

    /**
     * 获取指定日期的日活跃人数（最多获取30天前）.
     *
     * @param $date
     *
     * @throws \Exception
     *
     * @return array
     */
    public function getActiveStat($date)
    {
        $action_url = 'get_active_stat';

        $url = self::$access_url . self::$type_url . $action_url . '?' . http_build_query([
            'access_token' => $this->token,
        ]);

        $date = Carbon::parse($date)->format('Y-m-d');

        return self::HttpRequest($url, [
            'date'=> $date,
        ], 'POST');
    }
}
