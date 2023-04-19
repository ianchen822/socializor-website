<!DOCTYPE html>
<?php
    //啟動session
    session_start();
    if(!isset($_SESSION['user'])){
        header("Location: ./home.php");
    }
    $user = $_SESSION['user'];
    //與資料庫連線
    include("connection.php");
    //設定字元集與連線校對
    mysqli_set_charset($db_link,'utf8');
    //選擇資料庫
    if(!@mysqli_select_db($db_link,"socializor")) die("資料庫選擇失敗!");
    

    
    //選擇指定id資料
    $query = "SELECT * FROM member WHERE mId = '$user' ";
    $result = mysqli_query($db_link, $query); 
    //取得其陣列
    $row_result = mysqli_fetch_assoc($result);
?>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>我的留言 | Socializor</title>
  <link rel="stylesheet" type="text/css" href="web.css">
  <script type="text/javascript">
    function openuserinfo(){
      if (document.getElementById("userinfo").style.display == "block") {
        document.getElementById("userinfo").style.display = "none";
      }
      else{
        document.getElementById("userinfo").style.display = "block";
      }
    }
  </script>
</head>
<body>
    <div class="header">
        <div class="headerlogo">
            <a href="user.php">Socializor</a>
        </div>
        <form class="headersearch">
            <input class="search" type="text" name="search" placeholder="Search...">
            <input class="submit" type="submit" value="Search">
        </form>
        <div class="headerlogined" >
            <ul>
                <li><a href="writepost.php">貼文</a></li>
                <li><a href="chatroom.php">聊天室</a></li>
                <li id="username" onclick="openuserinfo()"><?php echo $row_result['name'];?></li>
            </ul>
        </div>
    </div>
    <div class="userupdate" id="userupdate">
        <h2>我的留言</h2>
        <hr>
        <?php
            //選擇指定id資料
            $query1 = "SELECT * FROM comment WHERE maker = '$user' ";
            $comments = mysqli_query($db_link, $query1); 
                
            while ($comment = mysqli_fetch_assoc($comments)) {
                echo "<div class='mycomment' onclick='createmodal(".$comment['cId'].")'>";
                    echo "<table class='commenttable'>";
                        echo "<tr>";
                            echo "<td rowspan='2'></td>";
                            echo "<td>".$comment['cident']."</td>";
                        echo "</tr>";
                        echo "<tr>";
                            echo "<td>".$comment['cTime']."</td>";
                        echo "</tr>";
                    echo "</table>";
                    echo "<p>".$comment['ccontent']."<p>";
                echo "</div>";
            }
        ?>
    </div>
    <div class="postmodal" id="postmodal">
        <?php
            //選擇指定id資料
            $query1 = "SELECT * FROM comment WHERE maker = '$user' ";
            $comments = mysqli_query($db_link, $query1); 
                            
            while ($comment = mysqli_fetch_assoc($comments)) {
                echo "<div class='modal-content' id=".$comment['cId'].">";
                    echo "<form action='./updatecomment.php' method='POST'>";
                        //updatepost
                        echo "<select name='cident' class='selectforwrite'>";
                            if ($comment['cident'] == $row_result['dep']) {
                                echo "<option value=".$row_result['dep']." selected>".$row_result['dep']."</option>";
                                echo "<option value=".$row_result['nickname'].">".$row_result['nickname']."</option>";
                                echo "<option value='匿名'>匿名</option>";
                            }
                            else if ($comment['cident'] == $row_result['nickname']) {
                                echo "<option value=".$row_result['dep'].">".$row_result['dep']."</option>";
                                echo "<option value=".$row_result['nickname']." selected>".$row_result['nickname']."</option>";
                                echo "<option value='匿名'>匿名</option>";
                            }
                            else if ($comment['cident'] == '匿名') {
                                echo "<option value=".$row_result['dep'].">".$row_result['dep']."</option>";
                                echo "<option value=".$row_result['nickname'].">".$row_result['nickname']."</option>";
                                echo "<option value='匿名' selected>匿名</option>";
                            }
                        echo "</select>";
                        
                        echo "<textarea class='postarea' name='ccontent'>".$comment['ccontent'];
                        echo "</textarea>";
                        $cId=$comment['cId'];?>
                        <a href='./deletecomment.php?cId=<?php echo $cId;?>'>
                            <input class='sendbtn' type='button' name='commentsubmit' value='刪除'>
                        </a>
                        <?php echo "<input class='sendbtn' type='reset' name='commentreset' value='重填'>";
                        echo "<input class='sendbtn' type='submit' name='commentdelete' value='修改'>";
                    echo "</form>";
                echo "</div>";
            }
        ?>
    </div>
    <div class="userbar" id="userinfo">
        <h2 id="sayhi"><?php echo 'Hi!&nbsp'. $row_result['nickname'];?></h2>
        <hr>
        <a href="./userinfo.php" class="forblock">
            <div class="useroption">會員資訊</div>
        </a>
        <a href="./myposts.php" class="forblock">
            <div class="useroption">我的貼文</div>
        </a>
        <a href="./mycomments.php" class="forblock">
            <div class="useroption">我的留言</div>
        </a>
        <div class="useroption" onclick="setbackgroud()" id="backgroundmode">夜間模式(off)</div>
        <a class="useroption" id="logout" href="./logout.php">登出</a>
    </div>
    
    <script type="text/javascript">
        function openuserinfo(){
            if (document.getElementById("userinfo").style.display == "block") {
                document.getElementById("userinfo").style.display = "none";
            }
            else{
                document.getElementById("userinfo").style.display = "block";
            }
        }       
        function createmodal(specific){
            // var posts = document.getElementsByClassName('post');
            var postmodal = document.getElementById('postmodal');
            var postcontent = document.getElementsByClassName('modal-content');
            
            for (var i = 0; i < postcontent.length; i++) {
                if (postcontent[i].id == specific) {
                    
                    postmodal.style.display = "block";
                    postcontent[i].style.display = "block";

                }
                else{
                    postcontent[i].style.display = "none";
                }
            }
        }
        function closemodal(){
            var postmodal = document.getElementById('postmodal');
            postmodal.style.display = 'none';
        }
        window.onclick = function(event) {
            if (event.target == postmodal) {
                postmodal.style.display = "none";
            }
        }
        function setbackgroud(){
            if (document.body.style.backgroundColor == "white") {
                document.body.style.backgroundColor = "#343132";
                document.getElementById("backgroundmode").innerHTML = '夜間模式(on)';
                localStorage.setItem('mode','1');
            }
            else 
            {
                document.body.style.backgroundColor = "white";
                document.getElementById("backgroundmode").innerHTML = '夜間模式(off)';
                localStorage.setItem('mode','0');
            }
                
        }
        //check if it is night mode
        if (localStorage.getItem('mode') == 1) {
            document.body.style.backgroundColor = "#343132";
            document.getElementById("backgroundmode").innerHTML = '夜間模式(on)';
        }
        else
        {
            document.body.style.backgroundColor = "white";
            document.getElementById("backgroundmode").innerHTML = '夜間模式(off)';
        }
    </script>
</body>
</html>