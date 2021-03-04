<?php
namespace myclass;

	class Phpcom {
        /**
         * 返回word内容，和字数（字符数），版本
         * @param $url
         * @return array
         */
        public static function php_com($url)
        {
            $word = new \COM("word.application") or die("Unable to instantiate Word");
            //打开路径为URL的word，doc或docx都可以
            $word->Documents->Open($url);
            //读取内容
            $test= $word->ActiveDocument->content->Text;
            //统计字数
            $num = strlen($test);
            //解决读取过程中乱码问题
            $content= iconv('GB2312', 'UTF-8', $test);
            //查看版本
            $word_wersion = $word->Version;
            //是否要打开文件，0代表否，1代表是
            $word->Visible = 0;

            //关闭word句柄
            $word->Quit();
            //释放对象
            $word = null;
            return [
                'num'=>$num/2,
                'word_wersion'=>$word_wersion,
                'content'=>$content
            ];
        }
    }
