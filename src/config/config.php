<?php
/**
 * created by zhang.
 */
return [
    //定义自创建app(最大管理范围的自建应用)
    //    'app'=> [
    //        'agent_id'     => env('AGENT_ID', ''),
    //        'agent_secrets'=> env('AGENT_SECRETS', ''),
    //    ],

    //企业id
    'qy_id'        => env('QY_ID', ''),

    //通讯录 secret
    'contacts_secrets'=> env('CONTACTS_SECRETS', ''),

    //客户联系 secret
    'customer_token'  => env('CUSTOMER_TOKEN', ''),
];
