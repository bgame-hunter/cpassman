<?php

/**
 *
 *
 * @version $Id$
 * @copyright 2011
 */

if (!isset($_SESSION['CPM'] ) || $_SESSION['CPM'] != 1)
	die('Hacking attempt...');


?>

<script type="text/javascript">

function aes_encrypt(text) {
    return Aes.Ctr.encrypt(text, "<?php echo $_SESSION['key'];?>", 256);
}

//Function opening
	function openKB(id){
		var data = "type=open_kb&"+
		    "&id="+id;
		httpRequest("sources/kb.queries.php",data);
	}

	//Function deleting
	function deleteKB(id){
		$("#kb_id").val(id);
		$("#div_kb_delete").dialog("open");
	}

	//encrypt
	function aes_encrypt(text) {
	    return Aes.Ctr.encrypt(text, "<?php echo $_SESSION['key'];?>", 256);
	}


	$(function() {
		//buttons
		$("#button_new_kb").button();

	    //Launch the datatables pluggin
	    $("#t_kb").dataTable({
	        "aaSorting": [[ 1, "asc" ]],
	        "sPaginationType": "full_numbers",
	        "bProcessing": true,
	        "bServerSide": true,
	        "sAjaxSource": "sources/kb.queries.table.php",
	        "bJQueryUI": true,
	        "oLanguage": {
	            "sUrl": "includes/language/datatables.<?php echo $_SESSION['user_language'];?>.txt"
	        }
	    });

	    //Dialogbox for deleting KB
	    $("#div_kb_delete").dialog({
	    	bgiframe: true,
			modal: true,
			autoOpen: false,
			width: 300,
			height: 150,
			title: "<?php echo $txt['item_menu_del_elem'];?>",
			buttons: {
				"<?php echo $txt['del_button'];?>": function() {
					$.post(
						"sources/kb.queries.php",
						"type=delete_kb&"+
					    "&id="+$("#kb_id").val(),
					    function(data){
							$("#div_kb_delete").dialog("close");
							oTable = $("#t_kb").dataTable();
							oTable.fnDraw();
						}
					)
	            },
	            "<?php echo $txt['cancel_button'];?>": function() {
	                $(this).dialog("close");
	            }
			}
	    });

	    //Dialogbox for new KB
	    $("#kb_form").dialog({
			bgiframe: true,
			modal: true,
			autoOpen: false,
			width: 900,
			height: 600,
			title: "<?php echo $txt['kb_form'];?>",
			buttons: {
				"<?php echo $txt['save_button'];?>": function() {
					if($("#kb_label").val() == "") {
						$("#kb_label").addClass( "ui-state-error" );
					}else if($("#kb_category").val() == "") {
						$("#kb_category").addClass( "ui-state-error" );
					}else if($("#kb_description").val() == "") {
						$("#kb_description").addClass( "ui-state-error" );
					}else{
                        //selected items associated to KB
                        var itemsvalues = [];
                        $("#kb_associated_to :selected").each(function(i, selected) {
                            itemsvalues[i] = $(selected).val();
                        });

                     	var data = '{"label":"'+protectString($("#kb_label").val())+'","category":"'+protectString($("#kb_category").val())+
                     		'","anyone_can_modify":"'+$("input[name=modify_kb]:checked").val()+'","id":"'+$("#kb_id").val()+
                     		'","kb_associated_to":"'+itemsvalues+'","description":"'+protectString(CKEDITOR.instances["kb_description"].getData())+'"}';

                     	$.post("sources/kb.queries.php",
					      	{
					      		type 	: "kb_in_db",
					      		data : aes_encrypt(data)
					      	},
			                function(data){
			                	if (data[0].status == "done") {
			                		oTable = $("#t_kb").dataTable();
			                		oTable.fnDraw();
			                	}
			                	$("#kb_form").dialog("close");
			                },
                			"json"
		                );
					}
				},
				"<?php echo $txt['cancel_button'];?>": function() {
					$(this).dialog("close");
				}
			},
			open:function(event, ui) {
				$("#kb_label, #kb_description, #kb_category").removeClass( "ui-state-error" );
				$("#kb_associated_to").multiselect();
				var instance = CKEDITOR.instances["kb_description"];
			    if(instance)
			    {
			    	CKEDITOR.replace("kb_description",{toolbar:"Full", height: 250,language: "<?php echo $k['langs'][$_SESSION['user_language']];?>"});
			    }else{
					$("#kb_description").ckeditor({toolbar:"Full", height: 250,language: "<?php echo $k['langs'][$_SESSION['user_language']];?>"});
			    }
			},
	        close: function(event,ui) {
	        	if(CKEDITOR.instances["kb_description"]){
	        		CKEDITOR.instances["kb_description"].destroy();
	        	}
	        	$("#kb_id,#kb_label, #kb_description, #kb_category, #full_list_items_associated").val("");
	        }
		});

		//category listing
		$( "#kb_category" ).autocomplete({
			source: "sources/kb.queries.categories.php",
			minLength: 1
		}).focus(function(){
			if (this.value == "")
				$(this).trigger("keydown.autocomplete");
		});
	});

</script>