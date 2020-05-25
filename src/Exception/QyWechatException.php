<?php

namespace John\QiYeWechat\Exception;

/**
 * Class OssException
 *
 * This is the class that OSSClient is expected to thrown, which the caller needs to handle properly.
 * It has the OSS specific errors which is useful for troubleshooting.
 *
 * @package OSS\Core
 */
class QyWechatException extends \Exception
{
    private $details = array();

    function __construct($details)
    {
        if (is_array($details)) {
            $message = $details['code'] . ': ' . $details['message']
                . ' RequestId: ' . $details['request-id'];
            parent::__construct($message);
            $this->details = $details;
        } else {
            $message = $details;
            parent::__construct($message);
        }
    }

    public function getHTTPStatus()
    {
        return isset($this->details['status']) ? $this->details['status'] : '';
    }

    public function getErrorCode()
    {
        return isset($this->details['code']) ? $this->details['code'] : '';
    }

    public function getErrorMessage()
    {
        return isset($this->details['message']) ? $this->details['message'] : '';
    }
}
