<?php
    include 'dbConnect.php';

    $op ='none';
    if(isset($_GET['op'])) $op = $_GET['op'];

    if($op=='checkLogin')
    {
        checkLogin($_POST['account'],$_POST['pwd']);
    }
    if($op=='logout')
    {
        logout();
    }
    if($op=='addCart')
    {
        session_start();
        addCart($_SESSION['member_account'],$_GET['game_ID']);
    }
    if($op=='removeOneCart')
    {
        session_start();
        removeOneCart($_SESSION['member_account'],$_GET['game_ID']);
    }
    if($op=='removeAllCart')
    {
        session_start();
        removeAllCart($_SESSION['member_account'],$_GET['game_ID']);
    }
    if($op=='signUp')
    {
        SingUp($_POST['account'],$_POST['pwd'],$_POST['email'],$_POST['name'],$_POST['nickname'],$_POST['birthday'],$_POST['phone'],$_POST['sexRadio']);
    }

    function isStaff()
    {
        return isset($_SESSION['account']);
    }
    function logout()
    {
        session_start();
        session_destroy();
        header("Location:index.php");
    }
    function checkLogin($account, $password)
    {
        global $link;
        $memberQ = mysqli_query($link, "SELECT * FROM member_info WHERE member_account='".$account."'");

        $member = mysqli_fetch_assoc($memberQ);

        if($account == $member['member_account'] && password_verify($password,$member['member_password']) )
        {       
            session_start();
            $_SESSION['member_account'] = $account;

            header("Location:index.php");
        }
        else
        {
            header("Location:login.php");
        }
       
    }

    function addCart($account,$game_ID)
    {
        global $link;
        if(isset($account))
        {
            $sql = "insert into member_cart values ('" . $account . "','" . $game_ID ."')";

            if ($result = mysqli_query($link, "SELECT * FROM member_cart")) {
                while ($row = mysqli_fetch_assoc($result)) {
                    if($row["member_account"] == $account && $row["game_ID"] == $game_ID){
                        header("Location:game-details.php?game_ID=".$game_ID);
                    }
                }
                mysqli_query($link, $sql);
                mysqli_free_result($result); // 釋放佔用的記憶體

                header("Location:game-details.php?game_ID=".$game_ID);
            }
        }
        else{
            header("Location:login.php");
        }
    }

    function removeOneCart($account,$game_ID)
    {
        global $link;
        $sql = "delete from member_cart where game_ID = '". $game_ID ."' and member_account = '" . $account . "'";

        mysqli_query($link, $sql);

        header("Location:cart.php");
    }

    function removeAllCart($account,$game_ID)
    {
        global $link;
        $sql = "delete from member_cart where member_account = '" . $account . "'";

        mysqli_query($link, $sql);

        header("Location:cart.php");
    }

    function SingUp($account,$pwd,$email,$name,$nickname,$birthday,$phone,$sexRadio)
    {
        global $link;

        if ($result = mysqli_query($link, "SELECT member_account FROM member_info")) {
            while ($row = mysqli_fetch_assoc($result)) {
                if($row["member_account"] == $account){
                    header("Location:signup.php");
                }
            }
            mysqli_free_result($result); // 釋放佔用的記憶體
        }

        $today = date("Y-n-j");     //今天日期
        if( $sexRadio == "1")      //選radio button
            $sex = "男性";
        else    
            $sex = "女性";
        
        $password = password_hash($pwd, PASSWORD_BCRYPT);   //加密

        $sql = "insert into member_info values ('" . $account . "','" . $password ."','" . $email ."','" . $name ."','" . $nickname ."','" . $birthday ."','" . $phone ."','" . $today ."','" . $sex ."')";
        mysqli_query($link, $sql);
        header("Location:login.php");
    }


?>