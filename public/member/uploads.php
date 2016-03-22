<?php
/**
 * Created by PhpStorm.
 * User: windows
 * Date: 14/03/2016
 * Time: 02:57 pm
 */
?>

<?php
require_once ("../../includes/database.php");
require_once ("../../includes/member.php");
require_once ("../../includes/session.php");
require_once ("../../includes/functions.php");
require_once ("../../includes/upload.php");
?>

<?php include("../layouts/member_header.php"); ?>
<?php  if(!$session->is_logged_in()){redirect_to("../login.php");} ?>
<?php  $member = Member::find_by_id($session->user_id);  ?>
<?php  $uploads = Upload::find_uploads_by_member_id($member->id); ?>
<div id="navigation">
    <?php include("../../includes/member_navigation.php");?>
</div>
<div id="page">
    <?php echo output_message($message); ?>
    <h2>Welcome <?php echo $member->first_name ." ".  $member->last_name ?></h2>
    <p> <img src="../images/<?php echo $member->image_file; ?>" alt="NO IMAGE"
             width="150"/> </p>
            <?php if($uploads != false){ ?>
<?php foreach($uploads as $upload): ?>
<?php       if(pathinfo($upload->filename, PATHINFO_EXTENSION) == "pdf") { ?>
                <p title="file_name=<?php echo $upload->filename; ?>"><a href="uploads/pdf_viewer.php?file_name=<?php
                echo htmlentities($upload->filename); ?>"> <?php  echo $upload->filename; ?></a></p>
                <p><?php echo pathinfo($upload->filename, PATHINFO_EXTENSION); ?></p>

<?php       }elseif(pathinfo($upload->filename, PATHINFO_EXTENSION) == "doc"){ ?>
                <p title="file_name=<?php echo $upload->filename; ?>"><a href="uploads/doc_viewer.php?file_name=<?php
                    echo htmlentities($upload->filename); ?>"> <?php  echo $upload->filename; ?></a></p>
                <p><?php echo pathinfo($upload->filename, PATHINFO_EXTENSION); ?></p>

<?php       }elseif(pathinfo($upload->filename, PATHINFO_EXTENSION) == "docx"){ ?>
                <p title="file_name=<?php echo $upload->filename; ?>"><a href="uploads/docx_viewer.php?file_name=<?php
                    echo htmlentities($upload->filename); ?>"> <?php  echo $upload->filename; ?></a></p>
                <p><?php echo pathinfo($upload->filename, PATHINFO_EXTENSION); ?></p>

<?php       }elseif(pathinfo($upload->filename, PATHINFO_EXTENSION) == "pptx"){?>
                <p title="file_name=<?php echo $upload->filename; ?>"><a href="uploads/pptx_viewer.php?file_name=<?php
                echo htmlentities($upload->filename); ?>"> <?php  echo $upload->filename; ?></a></p>
                <p><?php echo pathinfo($upload->filename, PATHINFO_EXTENSION); ?></p>

<?php       }elseif(pathinfo($upload->filename, PATHINFO_EXTENSION) == "ppt") { ?>
                <p title="file_name=<?php echo $upload->filename; ?>"><a href="uploads/ppt_viewer.php?file_name=<?php
                    echo htmlentities($upload->filename); ?>"> <?php  echo $upload->filename; ?></a></p>
                <p><?php echo pathinfo($upload->filename, PATHINFO_EXTENSION); ?></p>

<?php        }elseif(pathinfo($upload->filename, PATHINFO_EXTENSION) == "zip") {?><!-- zip upload not completed-->
                <p title="file_name=<?php echo $upload->filename; ?>"><a href="uploads/zip_viewer.php?file_name=<?php
                    echo htmlentities($upload->filename); ?>"> <?php  echo $upload->filename; ?></a></p>
                <p><?php echo pathinfo($upload->filename, PATHINFO_EXTENSION); ?></p>
<?php         }else{
                    redirect_to("http://google.com");
            }
?>
<?php endforeach; ?>
    <?php } // if carly braces ?>

    <p><a href="file_upload.php">upload file</a></p>

</div>
<?php include("../layouts/member_footer.php"); ?>

<?php
 // sql statement to create uploads table

/*mysql> create table uploads (id int(11) not null auto_increment,
    -> filename varchar(255) not null,
    -> type varchar(100) not null,
    -> size int(11) not null,
    -> caption varchar(255),
    -> member_id int(11) not null,
    -> primary key (id),
    -> foreign key (member_id) REFERENCES members (id));

    index the foreing key for faster retrieval
    alter table uploads add index (member_id);
*/
?>