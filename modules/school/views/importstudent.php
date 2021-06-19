<?php

/**
 * @filesource modules/school/views/importstudent.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace School\Importstudent;

use Kotchasan\Html;
use Kotchasan\Http\Request;
use Kotchasan\Http\UploadedFile;
use Kotchasan\Language;

/**
 * module=school-import&type=student
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Gcms\View
{
    /**
     * ฟอร์มนำเข้าข้อมูลนักเรียน
     *
     * @param Request $request
     * @param array   $login
     *
     * @return string
     */
    public function render(Request $request, $login)
    {
        $form = Html::create('form', array(
            'id' => 'setup_frm',
            'class' => 'setup_frm',
            'autocomplete' => 'off',
            'action' => 'index.php/school/model/import/submit',
            'onsubmit' => 'doFormSubmit',
            'ajax' => true,
            'token' => true,
        ));
        $fieldset = $form->add('fieldset', array(
            'title' => '{LNG_Details of} {LNG_Student}',
        ));
        // หมวดหมู่ของนักเรียน
        $category = \School\Category\Model::init();
        $categories = array();
        foreach ($category->typies() as $type) {
            $fieldset->add('select', array(
                'id' => $type,
                'labelClass' => 'g-input icon-office',
                'itemClass' => 'item',
                'label' => $category->label($type),
                'options' => $category->toSelect($type),
                'value' => $request->request($type)->toInt(),
            ));
            $categories[] = '<a href="' . WEB_URL . 'index.php?module=school-categories&amp;type=' . $type . '" target=_blank>' . $category->label($type) . '</a>';
        }
        // import
        $fieldset->add('file', array(
            'id' => 'import',
            'labelClass' => 'g-input icon-excel',
            'itemClass' => 'item',
            'label' => '{LNG_Browse file}',
            'comment' => Language::replace('File size is less than :size', array(':size' => UploadedFile::getUploadSize())),
            'accept' => array('csv'),
        ));
        $file = 'modules/school/views/importstudent_' . Language::name() . '.html';
        if (!is_file(ROOT_PATH . $file)) {
            $file = 'modules/school/views/importstudent_th.html';
        }
        $fieldset->add('div', array(
            'class' => 'message',
            'innerHTML' => str_replace(array('{CATEGORIES}', '{ENCODE}'), array(implode(', ', $categories), Language::find('CSV_ENCODING', '', self::$cfg->csv_language)), file_get_contents(ROOT_PATH . $file)),
        ));
        $fieldset = $form->add('fieldset', array(
            'class' => 'submit',
        ));
        // submit
        $fieldset->add('submit', array(
            'class' => 'button save large icon-import',
            'value' => '{LNG_Import}',
        ));
        // type
        $fieldset->add('hidden', array(
            'id' => 'type',
            'value' => 'student',
        ));
        // Javascript
        $form->script('initSchoolImportStudent();');
        // คืนค่า HTML Form
        return $form->render();
    }
}
