<?php
####################################################################################################
## File : admin.php
## Author : Nils Laumaillé
## Description : Admin page
## 
## DON'T CHANGE !!!
## 
####################################################################################################
echo '
        <h2 style="margin-top:15px;">'.$txt['admin'].'</h2>

        <div style="width:900px;margin-left:50px; line-height:25px;height:100%;overflow:auto;">    
            <div id="CPM_infos" style="float:left;margin-top:10px;margin-left:15px;width:500px;"></div>    
            
            <div style="float:right;width:300px;padding:10px;" class="ui-state-highlight ui-corner-all">
                <span class="ui-icon ui-icon-comment" style="float: left; margin-right: .3em;">&nbsp;</span>For any support, please use the <a href="http://cpassman.net23.net/forum" target="_blank">Forum</a>.
                <br />
                <span class="ui-icon ui-icon-wrench" style="float: left; margin-right: .3em;">&nbsp;</span>If you discover a bug, you can directly post it in <a href="http://code.google.com/p/cpassman/issues/list" target="_blank">GoogleCode</a>.
                <br /><br />
                <center>
                <script type="text/javascript" src="http://www.ohloh.net/p/468653/widgets/project_thin_badge.js"></script>
                <br /><br />
                <a href="http://sourceforge.net/donate/index.php?group_id=280505"><img src="http://images.sourceforge.net/images/project-support.jpg" width="88" height="32" border="0" alt="Support This Project" /> </a>
                <br /><br />
                '.$txt['thku'].'
                </center
            </div>
        </div>
        
        <script type="text/javascript">
            //Function loads informations from cpassman FTP
            function LoadCPMInfo(){
                var data = "type=cpm_status";
                httpRequest("sources/admin.queries.php",data);
            }
            //Load function on page load
            $(function() {
                LoadCPMInfo();
            });
        </script>';
?>