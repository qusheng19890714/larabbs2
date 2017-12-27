<?php
namespace App\Handlers;

use Image;

class ImageUploaderHandler
{
    //只允许以下后缀名的图片上传
    protected $allowed_ext = ['png', 'jpg', 'gif', 'jpeg'];

    public function save($file, $folder, $file_prefix, $max_width)
    {
        // 构建存储的文件夹规则，值如：uploads/images/avatars/201709/21/
        // 文件夹切割能让查找效率更高。
        $folder_name = "uploads/images/$folder/" . date("Ym", time()) . '/' . date("d", time()) . '/';

        // 文件具体存储的物理路径，`public_path()` 获取的是 `public` 文件夹的物理路径
        $upload_path = public_path() . '/' . $folder_name;

        //获取文件的后缀名, 因图片从剪贴板里黏贴时后缀名为空，所以此处确保后缀一直存在
        $extension = strtolower($file->getClientOriginalExtension()) ?: 'png';

        $filename = $file_prefix . '_' . time() . '_' . str_random(10) . '.' . $extension;

        if (!in_array($extension, $this->allowed_ext)) {

            return false;
        }

        $file->move($upload_path, $filename);

        //如果限制了宽度,就进行裁剪
        if ($max_width && $extension != 'gif') {

            $this->reduceSize($upload_path.$filename, $max_width);
        }

        return [
                'path'=>"/$folder_name/$filename"
            ];
    }

    public function reduceSize($file_path, $max_width)
    {
        //实例化
        $image = Image::make($file_path);

        //进行大小调整
        $image->resize($max_width, null, function($constraint) {

            // 设定宽度是 $max_width，高度等比例双方缩放
            $constraint->aspectRatio();

            // 防止裁图时图片尺寸变大
            $constraint->upsize();

        });

        $image->save();
    }
}