<?php
  if (isset($_POST["album_name"]))
  {
    require_once("dbtools.inc.php");
    $album_name = $_POST["album_name"];
  	
    //获取登录者账号
    session_start();
    $login_user = $_SESSION["login_user"];

    //建立数据连接
    $link = create_connection();

    //新增相册

    $sql = "SELECT ifnull(max(id), 0) + 1 AS album_id FROM album";
    $result = execute_sql($link, "album", $sql);
    $album_id = mysqli_fetch_object($result)->album_id;

    $sql = "INSERT INTO album(id, name, owner)
      VALUES($album_id, '$album_name', '$login_user')";

    execute_sql($link, "album", $sql);
  	
    //释放内存并关闭数据连接
    mysqli_free_result($result);
    mysqli_close($link);
    
    header("location:showAlbum.php?album_id=$album_id");
  }
?>
<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>电子相册</title>
  </head>
  <body>
    <p align="center"><img src="Title.png"></p>
    <form action="addAlbum.php" method="post">
      <table align="center">
        <tr> 
          <td> 
            相册名称：
          </td>
          <td>
            <input type="text" name="album_name" size="15">
            <input type="submit" value="新增">
          </td>
        </tr>
        <tr>
          <td colspan="3" align="center">
            <br><a href="index.php">回首页</a>
          </td>	
        </tr>
      </table>
    </form>
  </body>
</html>