<?php
/**
 * Created by PhpStorm.
 * User: qiaoc
 * Date: 2017/3/6
 * Time: 13:59
 */

namespace app\index\controller;

use think\Controller;
use think\Request;
use think\Model;
use think\Db;


class Inspection extends Controller{
    function index() {
        //echo 'SHALL WE BEGIN NOW??';
        $request = Request::instance();
        return $this->fetch('index');
    }

    function test() {
        //echo 'SHALL WE BEGIN NOW??';
        $request = Request::instance();
        return $this->fetch('test');
    }

    function testq() {
        //echo 'SHALL WE BEGIN NOW??';
        $request = Request::instance();
        return $this->fetch('testq');
    }

    function leak() {
        $request = Request::instance();
        $this->assign('form_token', uniqid());
        return $this->fetch('leak');
    }

    function crack() {
        $request = Request::instance();

        $checkpointId = $request->param('cpid');
        $inspectionId = $request->param('iid');
        $checkpoint_code = ltrim($request->param('cpcode'));

        if ($request->isPost()) {
            // 获取表单上传文件
            $data['token'] = $request->post('form_token');
            $data['inspection_id'] = $request->post('iid');
            $data['checkpoint_id'] = $request->post('cpid');
            $data['type'] = '区间隧道巡检';
            $data['item'] = '裂缝宽度';
            $data['record_txt'] = $request->post('crack_width');
            $data['record_checkbox'] = $request->post('crack_risk');
            $data['timestamp'] = date("Y-m-d H:i:s");
            $file = $request->file('file');
            if (empty($file)) {
                $this->error('请选择上传文件');
            }
            // 移动到框架应用根目录/public/uploads/ 目录下
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');

            if ($info) {
                $data['record_pic'] = $info->getSaveName();
                Db::table('inspection_record')->insert($data);
                Db::table('checkpoint')->where('id', '=', $data['checkpoint_id'])->update(['new_info' => 1]);
                $this->success('巡检报告提交成功：' . $info->getRealPath());
            } else {
                // 上传失败获取错误信息
                $this->error('文件上传失败：' . $file->getError());
            }


        } else {
            if ($checkpoint_code) {
                $data = Db::table('checkpoint')->where('checkpoint_code', '=', $checkpoint_code)->find();
                $checkpointId = $data['id'];
            }
            $this->assign('form_token', uniqid());
            $this->assign('iid', $inspectionId);
            $this->assign('cpid', $checkpointId);
            return $this->fetch('crack');
        }

    }

    function api(Request $request) {
        // The header conf from Sever side for cross origin access:
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:GET, POST, OPTIONS');
        header("Access-Control-Allow-Headers: *, Just_Test, Origin, Content-Type, X-Auth-Token , Authorization");
        //+ check header

        $request = Request::instance();
        $action = $request->param('action');

        if (!strcmp($action, 'checkpoints')) {
            $data = Db::table('checkpoint')->select();
            return json($data);
        }

        if (!strcmp($action, 'rawrecord')) {
            $checkpointId = $request->param('cpid');
            if ($checkpointId)
                $data = Db::table('inspection_record')->where('checkpoint_id', '=', $checkpointId)->where('archive', '=', 0)->select();
            else
                $data = Db::table('inspection_record')->where('archive', '=', 0)->select();
            return json($data);
        }

    }

}