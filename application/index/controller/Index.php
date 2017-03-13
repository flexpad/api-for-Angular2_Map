<?php
namespace app\index\controller;

use think\Controller;
use think\Request;
use think\Model;
use think\Db;
use think\Controller\Rest;

class Index extends Controller
{
    public function index()
    {
        $request = Request::instance();
        // 获取当前域名
        echo 'domain: ' . $request->domain() . '<br/>';
        // 获取当前入口文件
        echo 'file: ' . $request->baseFile() . '<br/>';
        $data = Db::table('hero')->select();
        var_dump($data);    return '<style type="text/css">*{ padding: 0; margin: 0; } .think_default_text{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_bd568ce7058a1091"></thinkad>';
    }
    
    public function api(Request $request) {

        // The header conf from Sever side for cross origin access:
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:GET, POST, OPTIONS');
        header("Access-Control-Allow-Headers: *, Just_Test, Origin, Content-Type, X-Auth-Token , Authorization");
        //+ check header

        $request = Request::instance();
        $id = $request->param('id');
        $action = $request->param('action');
        // 获取当前域名
        //echo 'domain: ' . $request->domain() . '<br/>';
        // 获取当前入口文件
        //echo 'file: ' . $request->baseFile() . '<br/>';
        //var_dump($request->param());


        if ($request->isPost()) {
            $heroName = input('post.name');
            $data = $data = ['id' => 0, 'name' => $heroName];
            Db::table('hero')->insert($data);
            $data['id'] = intval(Db::table('hero')->getLastInsID());
            return json($data);
        }
        if (! strcmp($action, 'query')) {
            /*
             * Token Checking.
             */
            if ($id)
                $data = Db::table('hero')->where('id', 'eq', $id)->select();
            else
                $data = Db::table('hero')->select();
            //var_dump($action);
            return json($data);
        }

        else if (! strcmp($action, 'add')) {
            $heroName = $request->param('name');
            $data = ['name' => $heroName, 'id' => 0];

            Db::table('hero')->insert($data);
            $data['id'] = intval(Db::table('hero')->getLastInsID());
            return json($data);
        }

        else if (!strcmp($action, 'delete')) {
            $heroName = $request->param('name');
            $id = $request->param('id');

            if (Db::table('hero')->where('name', 'LIKE', $heroName)->delete())
                return 'OK';
            if (Db::table('hero')->where('id', 'eq', $id)->delete())
                return 'OK';
            else return 'Del Failed';
        }

        else if (!strcmp($action,'modify')) {
            $heroName = $request->param('name');
            $id = $request->param('id');
            if (Db::table('hero')->where('id', 'eq', $id)->update(['name' => $heroName]))
                return 'OK';
            else return 'Update Failed';
        }
    }


    function test (){


        $arr=array(array('id'=>1,'value'=>3),array('id'=>2,'value'=>4),array('id'=>1,'value'=>9));


        var_dump($this->array_average($arr));
        $a = array(
            0 => array('部门ID' => 3, '名字' => '张三'),
            1 => array('部门ID' => 3, '名字' => '李四'),
            2 => array('部门ID' => 2, '名字' => '王五'),
            3 => array('部门ID' => 1, '名字' => '黄儿'),
        );

        foreach($a as $v) $r[$v['部门ID']] = $v['部门ID'];//join(':', $v);
        var_dump($a);
        var_dump($r);
        echo join(',', $r);


    }

    /**
     * @param $array
     * @return array
     */
    private function array_average($array) {
        $arr_sum = array();
        $arr_ave = array();
        foreach ($array as $row) {
            $arr_sum[$row['id']][] = $row['value'];
        }
        foreach ($arr_sum as $key => $row) {
            $count = count($row);
            $sum = 0;
            foreach ($row as $val) {
                $sum += $val;
            }
            $arr_ave[$key] = $sum/$count;
        }
        return $arr_ave;
    }
}
