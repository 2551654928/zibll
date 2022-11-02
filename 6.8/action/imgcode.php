<?php
/*
 * @Author        : Qinver
 * @Url           : zibll.com
 * @Date          : 2020-09-29 13:18:37
 * @LastEditTime: 2022-04-05 21:18:46
 * @Email         : 770349780@qq.com
 * @Project       : Zibll子比主题
 * @Description   : 一款极其优雅的Wordpress主题
 * @Read me       : 感谢您使用子比主题，主题源码有详细的注释，支持二次开发。
 * @Remind        : 使用盗版主题会存在各种未知风险。支持正版，从我做起！
 */

$code_id = !empty($_REQUEST['id']) ? $_REQUEST['id'] : 'code';
ob_start();
$code = new zib_img_code($code_id);
$code->doimg();
$data = ob_get_contents();
ob_end_clean();

$imageString = base64_encode($data);
header("content-type:application/json; charset=utf-8");

echo json_encode(['img' => 'data:image/jpeg;base64,' . $imageString]);
exit();

class zib_img_code
{

    private $charset = "abcdefghjklmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789"; //随机因子
    private $code; //验证码文
    private $codelen = 4; //验证码显示几个文字
    private $width   = 100; //验证码宽度
    private $height  = 40; //验证码高度
    private $img; //验证码资源句柄
    private $font; //指定的字体
    private $fontsize = 20; //指定的字体大小
    private $code_id;
    //构造类 编写字体
    public function __construct($id = 'code')
    {
        $this->font    = '../fonts/img-code.ttf';
        $this->code_id = $id;
    }

    //创建4个随机码
    private function createCode()
    {

        $_leng = strlen($this->charset) - 1;
        for ($i = 1; $i <= $this->codelen; $i++) {
            $this->code .= $this->charset[mt_rand(0, $_leng)];
        }
        @session_start();
        $_SESSION['machine_img_code_' . $this->code_id] = strtolower($this->code);

        return $this->code;
    }

    //创建背景
    private function createBg()
    {
        //创建画布 给一个资源
        $this->img = imagecreatetruecolor($this->width, $this->height);
        //背景颜色
        $color = imagecolorallocate($this->img, mt_rand(160, 220), mt_rand(160, 220), mt_rand(160, 220));
        //画出一个矩形
        imagefilledrectangle($this->img, 0, $this->height, $this->width, 0, $color);
    }

    //创建字体
    private function createFont()
    {

        $_x = ($this->width / $this->codelen); //字体长度
        for ($i = 0; $i < $this->codelen; $i++) {
            //文字颜色
            $color = imagecolorallocate($this->img, mt_rand(0, 110), mt_rand(0, 110), mt_rand(0, 110));
            //资源句柄 字体大小 倾斜度 字体长度 字体高度 字体颜色 字体 具体文本
            imagettftext($this->img, $this->fontsize, mt_rand(-30, 30), $_x * $i + mt_rand(1, 5), $this->height / 1.4, $color, $this->font, $this->code[$i]);
        }
    }

    //随机线条
    private function createLine()
    {

        //随机线条
        for ($i = 0; $i < 6; $i++) {
            $color = imagecolorallocate($this->img, mt_rand(0, 220), mt_rand(0, 220), mt_rand(0, 220));
            imageline($this->img, mt_rand(0, $this->width), mt_rand(0, $this->height), mt_rand(0, $this->width), mt_rand(0, $this->height), $color);
        }

        //随机雪花
        for ($i = 0; $i < 45; $i++) {
            $color = imagecolorallocate($this->img, mt_rand(210, 255), mt_rand(210, 255), mt_rand(210, 255));
            imagestring($this->img, mt_rand(1, 5), mt_rand(0, $this->width), mt_rand(0, $this->height), '*', $color);
        }
    }

    //输出背景
    private function outPut()
    {
        //生成标头
        header('Content-type:image/png');
        //输出图片
        imagepng($this->img);
        //销毁结果集
        imagedestroy($this->img);
    }

    //对外输出
    public function doimg()
    {

        //加载背景
        $this->createBg();
        //加载文件
        $this->createCode();
        //加载线条
        $this->createLine();
        //加载字体
        $this->createFont();
        //加载背景
        $this->outPut();
    }

    //获取验证码

    public function getCode()
    {
        return strtolower($this->code);
    }

    public function ajax_send()
    {

    }

    public function get_img_code()
    {
        ob_clean();
        @session_start();
        $_SESSION['machine_img_code'] = null;

        $code                         = $this->rand_str(4);
        $expire                       = time() + 30;
        $_SESSION['machine_img_code'] = $code;
        setcookie('machine_img_code', time(), $expire, '/', md5($code), false);

        $x_size = 75;
        $y_size = 30;
        $aimg   = imagecreate($x_size, $y_size);
        $back   = imagecolorallocate($aimg, 255, 255, 255);
        $border = imagecolorallocate($aimg, 204, 53, 53);
        imagefilledrectangle($aimg, 10, 10, $x_size + 1, $y_size + 1, $back);
        imagerectangle($aimg, 100, 100, $x_size, $y_size, $border);
        imageString($aimg, 30, 20, 8, $code, $border);
        header("Pragma:no-cache");
        header("Cache-control:no-cache");
        header("Content-type: image/png");
        imagepng($aimg);
        imagedestroy($aimg);
    }

    //生成随机字符串
    public function rand_str($len)
    {
        $chars    = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z", "A", "B", "D", "E", "F", "G", "H", "J", "L", "M", "N", "Q", "R", "T", "U", "Y", "2", "3", "4", "5", "6", "7", "8", "9");
        $charsLen = count($chars) - 1;
        shuffle($chars);
        $outStr = '';
        for ($i = 0; $i < $len; $i++) {
            $outStr .= $chars[mt_rand(0, $charsLen)];
        }
        return $outStr;
    }
}
