<?php
/**
 * Created by PhpStorm.
 * User: windows
 * Date: 04/05/2016
 * Time: 03:14 am
 */

require_once ("../includes/database.php");
require_once ("../includes/member.php");
require_once ("../includes/session.php");
require_once ("../includes/functions.php");
require_once ("../includes/admin.php");
$page = !empty($_GET['page']) ? (int) $_GET['page'] : 1;
$per_page = 20;
$total_count = Publication::count_all();
$pagination = new Pagination($page, $per_page, $total_count);
$publications = Publication::find_all($per_page, $pagination->offset());
?>

<?php include("layouts/header.php"); ?>

    <div id="main">

        <div id="navigation">
            <?php  include("../includes/public_navigation.php"); ?>
        </div>

        <div id="page">
            <br />
            <?php
            foreach($publications as $publication){
                echo "<table>";
                    echo "<tbody>";
                        echo "<tr>";
                            echo "<td>";
                                if($publication->url != 'None')
                                    echo "<a id='publc_url' href='{$publication->url}'>{$publication->title}</a>";
                                else
                                    echo "<p>{$publication->title}</p>";
                            echo "</td>";
                        echo "</tr>";
                    echo "</tbody>";
                echo "</table>";
            }?>

            <!-- ********** Pagination Part *******-->
            <p>
            <div id="pagination" style="clear: both;">
                <?php

                if($pagination->total_pages() > 1){
                    if($pagination->has_previous_page()){
                        echo "<a href=\"browse_publc.php?page=";
                        echo $pagination->previous_page();
                        echo "\">&laquo Previous</a>";
                    }

                    for($i = 1; $i <= $pagination->total_pages(); $i++){
                        if($i == $page){
                            echo "<span class='selected'>{$i}</span>";
                        }else {
                            echo " <a href='browse_publc.php?page={$i}'>{$i}</a> ";
                        }
                    }

                    if($pagination->has_next_page()){
                        echo "<a href=\"browse_publc.php?page=";
                        echo $pagination->next_page();
                        echo "\">Next &raquo</a>";
                    }

                }
                ?>
            </div></p>
        </div>

    </div>
    </div>
    <script type="text/javascript">
        document.getElementById("publc_url").onclick = function(){
            var nav = document.getElementById('navigation');
            var request = new XMLHttpRequest();
            request.open("GET", "increment_hits.php?publc_id=<?php echo $publication->id;?>", false);
            request.send();
            if(request.response === 200){
                console.log(request.response);
            }
        };

    </script>

<?php include("layouts/footer.php"); ?>