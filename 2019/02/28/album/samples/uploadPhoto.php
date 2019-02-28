<?php
  require_once("dbtools.inc.php");
  
  //建立数据连接
  $link = create_connection();
	  
  if (!isset($_POST["album_id"]))
  {
    $album_id = $_GET["album_id"];
	           
    //获取相册名称及相册的主人
    $sql = "SELECT name, owner FROM album WHERE id = $album_id";
    $result = execute_sql($link, "album", $sql);
    $row = mysqli_fetch_object($result);
    $album_name = $row->name;
    $album_owner = $row->owner;
	  
    //释放 $result 占用的内存	
    mysqli_free_result($result);
  }
  else
  {
  	$album_id = $_POST["album_id"];
  	$album_owner = $_POST["album_owner"];
  	
    //获取登录者账号
  	session_start();
    $login_user = $_SESSION["login_user"];

    if (isset($login_user) && $album_owner == $login_user)	
    {  	
      for ($i = 0; $i <= 3; $i++)
      {		
        //若文件名不是空字符串，表示上传成功，将临时文件移至指定的目录
        if ($_FILES["myfile"]["name"][$i] != "")
        {
          $src_file = $_FILES["myfile"]["tmp_name"][$i];
          $src_file_name = $_FILES["myfile"]["name"][$i];
          $src_ext = strtolower(strrchr($_FILES["myfile"]["name"][$i], "."));
          $desc_file_name = uniqid() . ".jpg";
	
          $photo_file_name = "./Photo/$desc_file_name";
          $thumbnail_file_name = "./Thumbnail/$desc_file_name";
	
          resize_photo($src_file, $src_ext, $photo_file_name, 600);
          resize_photo($src_file, $src_ext, $thumbnail_file_name, 150);
	        
          $sql = "insert into photo(name, filename, album_id) values('$src_file_name', '$desc_file_name', $album_id)";
          execute_sql($link, "album", $sql);
        }
      }
    }

    //关闭数据连接	
    mysqli_close($link);
  
    header("location:showAlbum.php?album_id=$album_id");
  }
  
  function resize_photo($src_file, $src_ext, $dest_name, $max_size)
  {
  	switch ($src_ext)
  	{
  	  case ".jpg":
  	    $src = imagecreatefromjpeg($src_file);
  	    break;
  	  case ".png":
  	    $src = imagecreatefrompng($src_file);
  	    break;
  	  case ".gif":
  	    $src = imagecreatefromgif($src_file);
  	    break;
  	}

    $src_w = imagesx($src);
    $src_h = imagesy($src);
	  
    //建立新的空图片
    if($src_w > $src_h)
    {
      $thumb_w = $max_size;
      $thumb_h = intval($src_h / $src_w * $thumb_w);
    }
    else
    {
      $thumb_h = $max_size;
      $thumb_w = intval($src_w / $src_h * $thumb_h);
    }
	  
    $thumb = imagecreatetruecolor($thumb_w, $thumb_h);
	
    //进行复制并缩图
    imagecopyresized($thumb, $src, 0, 0, 0, 0, $thumb_w, $thumb_h, $src_w, $src_h);
	   
    //存储照片
    imagejpeg($thumb, $dest_name, 100);

    //释放图像占用的内存
    imagedestroy($src);
    imagedestroy($thumb); 
  }
?>
<!doctype html>
<html>
  <head>
    <title>电子相册</title>
    <meta charset="utf-8">
  </head>
  <body>
    <p align="center"><img src="Title.png"></p>
    <p align="center">
      <?php echo $album_name ?>
        <form method="post" action="uploadPhoto.php" enctype="multipart/form-data">
          <input type="file" name="myfile[]" size="50"><br>
	      <input type="file" name="myfile[]" size="50"><br>
	      <input type="file" name="myfile[]" size="50"><br>
	      <input type="file" name="myfile[]" size="50"><br><br>
	      <input type="hidden" name="album_id" value="<?php echo $album_id ?>">
	      <input type="hidden" name="album_owner" value="<?php echo $album_owner ?>">
	      <input type="submit" value="上传">
	      <input type="reset" value="重新填写">
	    </form>
      <a href="showAlbum.php?album_id=<?php echo $album_id ?>">
        回【<?php echo $album_name ?>】相册</a>
    </p>
  </body>
</html>