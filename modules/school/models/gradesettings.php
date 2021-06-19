<?php

/**
 * @filesource modules/school/models/gradesettings.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace School\Gradesettings;

use Gcms\Config;
use Gcms\Login;
use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * module=school-gradesettings
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\KBase
{
    /**
     * เงื่อนไขการตัดเกรด
     * ถ้าไม่มีคืนค่าเริ่มต้น
     * @param Request $request
     * @return array
     */
    public static function toDataTable(Request $request)
    {
        if ($request->request('default')->toBoolean()) {
            return array(
                array('score' => 49, 'grade' => 0),
                array('score' => 54, 'grade' => 1),
                array('score' => 59, 'grade' => 1.5),
                array('score' => 64, 'grade' => 2),
                array('score' => 69, 'grade' => 2.5),
                array('score' => 74, 'grade' => 3),
                array('score' => 79, 'grade' => 3.5),
                array('score' => 100, 'grade' => 4),
            );
        } else {
            $return = array();
            $scores = \School\Score\Model::scores();
            if (empty($scores)) {
                $return[] = array(
                    'score' => '',
                    'grade' => '',
                );
            } else {
                foreach ($scores as $k => $v) {
                    $return[] = array(
                        'score' => $k,
                        'grade' => $v,
                    );
                }
            }
            return $return;
        }
    }

    /**
     * รับค่าจากฟอร์ม (gradesettings.php)
     *
     * @param Request $request
     */
    public function submit(Request $request)
    {
        $ret = array();
        // session, token, can_config
        if ($request->initSession() && $request->isSafe() && $login = Login::isMember()) {
            if ($login['active'] == 1 && Login::checkPermission($login, 'can_config')) {
                try {
                    // โหลด config
                    $config = Config::load(ROOT_PATH . 'settings/config.php');
                    // รับค่าจากการ POST
                    $score = $request->post('score')->toInt();
                    $grade = $request->post('grade')->topic();
                    $config->school_grade_caculations = array();
                    foreach ($score as $k => $v) {
                        if ($v > 0 && $grade[$k] != '') {
                            $config->school_grade_caculations[$v] = $grade[$k];
                        }
                    }
                    ksort($config->school_grade_caculations);
                    // save config
                    if (Config::save($config, ROOT_PATH . 'settings/config.php')) {
                        // คืนค่า
                        $ret['alert'] = Language::get('Saved successfully');
                        $ret['location'] = 'reload';
                        // เคลียร์
                        $request->removeToken();
                    } else {
                        // ไม่สามารถบันทึก config ได้
                        $ret['alert'] = sprintf(Language::get('File %s cannot be created or is read-only.'), 'settings/config.php');
                    }
                } catch (\Kotchasan\InputItemException $e) {
                    $ret['alert'] = $e->getMessage();
                }
            }
        }
        if (empty($ret)) {
            $ret['alert'] = Language::get('Unable to complete the transaction');
        }
        // คืนค่าเป็น JSON
        echo json_encode($ret);
    }
}
