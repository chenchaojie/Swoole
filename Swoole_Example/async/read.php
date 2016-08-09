<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/5
 * Time: 18:33
 */

$size = 20;

$offset = 0;



$is_end = false;

    swoole_async_read('./read.log', function($filename, $content){

        echo "filename:{$filename} ,content:{$content}\n";

        swoole_async_write('./write.log', $content, -1, function($file, $content){
            return true;
        });

        if (empty($content)) {
            return false;
        }

        return true;


    }, $size, $offset);

echo '继续干活喽';
//如果客户端阻塞 也会阻塞异步文件读写
sleep(10);
echo 111;