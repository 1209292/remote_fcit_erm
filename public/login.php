<?php

require_once ("../includes/database.php");
require_once ("../includes/member.php");
require_once ("../includes/session.php");
require_once ("../includes/functions.php");
require_once ("../includes/admin.php");
require_once ("../includes/search_thread.php");

if($session->is_logged_in('super_user')){redirect_to("super_user/dashboard.php"); }
if($session->is_logged_in('admin')){redirect_to("admin/manage_content.php"); }
if($session->is_logged_in('member')){redirect_to("member/index.php"); }

if(isset($_POST["submit"])){ // form has been submitted
    $id = trim($_POST['id']);
    $password = trim($_POST['password']);
    // check database to see if username/password exists
    $found_admin = Admin::authenticate($id, $password);
    $found_member = Member::authenticate($id, $password);
    $found_super_user = SuperUser::authenticate($id, $password);

    if($found_admin) {
        $session->login($found_admin);
        redirect_to("admin/manage_content.php");
    }elseif($found_member){
        $session->login($found_member);
//        $thread = new SearchThread($found_member->full_name, $found_member->id);
//        $thread->start();
        redirect_to("member/index.php");
    }elseif($found_super_user) {
        $session->login($found_super_user);
        redirect_to("super_user/dashboard.php");
    }else{
        // username/password combo was not found in the database
        $message = "ID/Password combination incorrect";
    }
} else { // form hasn't been submitted
    $id = "";
    $password = "";
    $message = "";
}

?>
<?php include("layouts/header.php"); ?>
<div id="main">
    <div id="navigation">
        <?php include ("../includes/public_navigation.php") ?>
    </div>
    <h2>Staff Login</h2>
    <?php echo output_message($message); ?>
    <form action="login.php" method="post">
        <table>
            <tr>
                <td>ID:</td>
                <td>
                    <input type="text" name="id" maxlength="30"
                           value="<?php echo htmlentities($id); ?>" />
                </td>
            </tr>
            <tr>
                <td>Password:</td>
                <td>
                    <input type="password" name="password" maxlength="30"
                           value="<?php echo htmlentities($password); ?>" />
                </td>
            </tr>
            <tr>

                <td colspan="2">
                    <input type="submit" name="submit" value="login" />
                </td>
            </tr>
        </table>

    </form>

    <?php include ("layouts/footer.php");?>
