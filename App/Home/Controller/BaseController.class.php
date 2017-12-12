<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/20
 * Time: 17:07
 */
namespace Home\Controller;
use Think\Controller;
class BaseController extends Controller
{
    public $rules = array();
    //初始化，判断是否登录
    public function _initialize()
    {
        $ruleResults = M('Rule')->select();
        foreach ($ruleResults as $rule) {
            $this->rules[$rule["name"]] = $rule["value"];
        }

        foreach ($ruleResults as $rule) {
            $this->rules[$rule["name"]] = $rule["value"];
        }
        if (!session('user_id')) {
//            header('location:' . U('Home/Login/login'));
            $this->error('登录超时，请重新登录！',U('Home/Login/login'));
        } else {
            $header = $_SERVER["PHP_SELF"];
            if ($header == $_SERVER["SCRIPT_NAME"]) {
                header('location:' . U('Home/Index/agent'));
            }
        }
//    	var_dump($_SERVER["PHP_SELF"]);
        $this->assign('role', session('role'));
        $this->assign('user_id',session('user_id'));
        $this->assign('adminTel', session('adminTel'));
    }

    /**
     *获取分页
     * @param $re array 要分页的结果集
     * @param $pageSize int 每页的记录数
     * @param return array('pageStr'=>$str,分页信息
     * 'arr'=>$arr   分页后的结果集
     * )
     */
    public function showPage($re, $pageSize = '')
    {
        $count = count($re);
        $pageSize = $pageSize ? $pageSize : 10;
        $pageOb = new \Think\Page($count, $pageSize);
        $pageOb->setConfig('header', '<span class="rows">共 %TOTAL_ROW% 条记录&nbsp; %NOW_PAGE%/%TOTAL_PAGE%页</span>');
        $pageOb->setConfig('first', '首页');
        $pageOb->setConfig('prev', '上一页');
        $pageOb->setConfig('next', '下一页');
        $pageOb->setConfig('last', '最后一页');
        $pageOb->setConfig('theme', '%HEADER%  %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
        if (empty($_GET['p'])) {
            $_GET['p'] = 1;
        }
        $offset = ($_GET['p'] - 1) * $pageSize;
        $arr = array_slice($re, $offset, $pageSize);
        $str = $pageOb->show();
        if ($str == '') {
            $str = '共 0 条记录';
        }
        $data = array('pageStr' => $str, 'arr' => $arr);
        return $data;
        /*$this->assign('str',$str);
        $this->assign('arr',$arr);
        $this->assign('p',$_GET['p']);*/
    }

    public function isAgentRole($role)
    {
        return $role > 0 && $role < 10;
    }

    public function isAdminRole($role)
    {
        return $role > 90;
    }

    //记录函数
    public function action($userId, $type, $gmId)
    {
        $aOb = M('Action');
        $data['username'] = $userId;
        $data['gmname'] = $gmId;
        $data['type'] = $type;
        $data['addtime'] = time();
        $aOb->add($data);
    }


    public function exportExcel($expTitle, $expCellName, $expTableData)
    {
        $xlsTitle = iconv('utf8', 'gb2312', $expTitle); //文件名称
        $fileName = $expTitle . date('Y-m-d');  //自定义文件名
        $cellNum = count($expCellName);
        $dataNum = count($expTableData);
        vendor("PHPExcel.PHPExcel");
//        import('PHPExcel','','.php');
        $objPHPExcel = new \PHPExcel();
        $cellName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ');

        $objPHPExcel->getActiveSheet()->getColumnDimension('A1')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getStyle()->getFont()->setSize(16);
        $objPHPExcel->getActiveSheet()->getStyle()->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet(0)->mergeCells('A1:' . $cellName[$cellNum - 1] . '1');  //合并单元格
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle . ' 导出时间:' . date('Y-m-d H:i:s'));

        for ($i = 0; $i < $cellNum; $i++) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i] . '2', $expCellName[$i][1]);
        }
        // Miscellaneous glyphs, UTF-8
        for ($i = 0; $i < $dataNum; $i++) {
            for ($j = 0; $j < $cellNum; $j++) {
                $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j] . ($i + 3), $expTableData[$i][$expCellName[$j][0]]);
            }
        }

        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="' . $xlsTitle . '.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }
}