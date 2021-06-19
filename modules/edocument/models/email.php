<?php

/**
 * @filesource modules/edocument/models/email.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Edocument\Email;

use Kotchasan\Email;
use Kotchasan\KBase;
use Kotchasan\Language;

/**
 * ส่งอีเมลแจ้งสมาชิก
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends KBase
{
    /**
     * ส่งอีเมลไปยังผู้รับ
     *
     * @param array $reciever
     * @param array $login
     *
     * @return string
     */
    public static function send($reciever, $login)
    {
        $ret = array();
        // ข้อมูลอีเมล
        $subject = Language::replace('There are new documents sent to you at %WEBTITLE%', array('%WEBTITLE%' => self::$cfg->web_title));
        $msg = Language::replace('You received a new document %URL%', array('%URL%' => WEB_URL . 'index.php?module=edocument-received'));
        // query สมาชิกสถานะที่เลือก
        $query = \Kotchasan\Model::createQuery()
            ->select('name', 'username')
            ->from('user')
            ->where(array(
                array('status', $reciever),
                array('username', '!=', ''),
                array('id', '!=', (int) $login['id']),
            ))
            ->cacheOn();
        foreach ($query->execute() as $item) {
            $err = Email::send($item->name . '<' . $item->username . '>', self::$cfg->noreply_email, $subject, $msg);
            if ($err->error()) {
                // คืนค่า error
                $ret[] = $err->getErrorMessage();
            }
        }
        if (isset($err)) {
            if (empty($ret)) {
                // ส่งอีเมสำเร็จ
                return Language::get('Save and email completed');
            } else {
                // error กการส่งเมล
                return implode("\n", $ret);
            }
        } else {
            // ไม่มีอีเมลต้องส่ง
            return Language::get('Saved successfully');
        }
    }
}
