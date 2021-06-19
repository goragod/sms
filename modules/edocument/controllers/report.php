<?php

/**
 * @filesource modules/edocument/controllers/report.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Edocument\Report;

use Gcms\Login;
use Kotchasan\Html;
use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * module=edocument-report
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Gcms\Controller
{
    /**
     * แสดงรายการเอกสาร
     *
     * @param Request $request
     *
     * @return string
     */
    public function render(Request $request)
    {
        // ตรวจสอบรายการที่เลือก
        $index = \Edocument\Report\Model::get($request->request('id')->toInt());
        // เลือกเมนู
        $this->menu = 'edocument';
        // ข้อความ title bar
        $this->title = Language::get('Download history');
        // สมาชิก
        if ($index && Login::isMember()) {
            // ข้อความ title bar
            $this->title .= ' ' . $index->topic;
            // แสดงผล
            $section = Html::create('section', array(
                'class' => 'content_bg',
            ));
            // breadcrumbs
            $breadcrumbs = $section->add('div', array(
                'class' => 'breadcrumbs',
            ));
            $ul = $breadcrumbs->add('ul');
            $ul->appendChild('<li><span class="icon-edocument">{LNG_E-Document}</span></li>');
            $ul->appendChild('<li><a href="{BACKURL?module=edocument-sent&id=0}">{LNG_sent document}</a></li>');
            $ul->appendChild('<li><span>' . $index->topic . '</span></li>');
            $ul->appendChild('<li><span>{LNG_Download}</span></li>');
            $section->add('header', array(
                'innerHTML' => '<h2 class="icon-list">' . $this->title . '</h2>',
            ));
            // รายละเอียดการรับหนังสือ
            $section->appendChild(\Edocument\Report\View::create()->render($request, $index));
            // คืนค่า HTML
            return $section->render();
        }
        // 404
        return \Index\Error\Controller::execute($this, $request->getUri());
    }
}
