/*!
 * remark (http://getbootstrapadmin.com/remark)
 * Copyright 2018 amazingsurge
 * Licensed under the Themeforest Standard Licenses
 */

define(["jquery","./tether","./babel-external-helpers","./breakpoints"],function(jQuery,Tether,babelHelpers,breakpoints){function getRotation(){return 1==rotate?(rotate=0,90):(rotate=1,0)}window.jQuery=jQuery,window.Tether=Tether,window.babelHelpers=babelHelpers,require(["theme_remui/bootstrap","theme_remui/jquery-mousewheel","theme_remui/jquery-asScrollbar","theme_remui/jquery-asScrollable","theme_remui/jquery-asHoverScroll","theme_remui/screenfull","theme_remui/jquery-slidePanel","theme_remui/State","theme_remui/Base","theme_remui/Plugin","theme_remui/Config","theme_remui/Menubar","theme_remui/Sidebar","theme_remui/PageAside","theme_remui/menu","theme_remui/asscrollable","theme_remui/slidepanel","theme_remui/skintools","theme_remui/RemUI"],function(BS,mw,asb,asbl,ahs,sfl,spnl,State,Base,Plugin,Config,Menubar,Sidebar,PageAside,menu,Scrollable,SlidePanel,skintools,RemUI){function saveGridPositions(){var serializedData=_.map(jQuery("#block-region-content > .grid-stack-item:visible"),function(el){var node=(el=jQuery(el)).data("_gridstack_node");return{id:el.data("block"),sn:el.data("subname"),x:node.x,y:node.y,wd:node.width,ht:node.height}});M.util.set_user_preference("remuiblck_pos_state",JSON.stringify(serializedData))}function toggleAsideOnLgScreen(){jQuery("body").hasClass("overrideaside")?(jQuery("body").removeClass("overrideaside"),M.util.set_user_preference("aside_right_state","")):(jQuery("body").addClass("overrideaside"),M.util.set_user_preference("aside_right_state","overrideaside")),jQuery(window).trigger("resize")}function chatMessageAjax(otheruserid){jQuery.ajax({type:"GET",async:!0,url:M.cfg.wwwroot+"/theme/remui/request_handler.php?action=get_data_for_messagearea_messages_ajax&otheruserid="+otheruserid,success:function(response){1==response.isonline?jQuery("div#conversation div.conversation-header a.conversation-more i").addClass("green-600"):jQuery("div#conversation div.conversation-header a.conversation-more i").removeClass("green-600"),jQuery("div#conversation div.conversation-header div.conversation-title").html(response.otheruserfullname),jQuery("div#conversation div.conversation-header div.conversation-title").data("id",otheruserid),jQuery("div#conversation div.chats").empty();var child='<small class="d-block text-center sticky-top  py-5"><a href="'+M.cfg.wwwroot+"/message/index.php?user="+response.currentuserid+"&id="+otheruserid+'" class="text-muted">View All Messages</a></small>';jQuery.each(response.messages,function(index,value){"right"==value.position?child+='<div class="chat"><div class="chat-body mr-0"><div class="chat-content mr-0">':child+='<div class="chat chat-left"><div class="chat-body ml-0"><div class="chat-content ml-0">',child+=value.text+'<time class="chat-time" datetime="'+Date(value.timecreated)+'">'+value.timesent+"</time></div></div></div>"}),jQuery("div#conversation div.chats").append(child)},error:function(xhr,status,error){}})}function sendMessageNotification(x){jQuery("div#conversation div.conversation-reply input").val(""),"success"!=x&&(jQuery("div#conversation #message_response").removeClass("alert-success"),jQuery("div#conversation #message_response").addClass("alert-danger"),jQuery("div#conversation #message_response").html(jQuery("div#conversation input#messagenotsent").val())),jQuery("div#conversation #message_response").show(),jQuery("div#conversation #message_response").delay(3e3).fadeOut("slow")}RemUI.run(),jQuery("#block-region-content").on("change",function(event,items){saveGridPositions()}),jQuery(".page-aside-switch-lg").click(function(){toggleAsideOnLgScreen()}),jQuery(".page-login-main .fcontainer .form-group").each(function(index){var label=jQuery.trim(jQuery(".col-form-label",this).text());jQuery(".felement input",this).attr("placeholder",label)}),jQuery(document).on("click","div.site-sidebar-tab-content #sidebar-userlist a.list-group-item",function(){chatMessageAjax(jQuery(this).data("otheruserid"))}),jQuery(document).on("click","div#conversation div.conversation-reply a.sidebar-send-message",function(){event.preventDefault();var contactid=jQuery("div#conversation div.conversation-header div.conversation-title").data("id"),message=jQuery.trim(jQuery("div#conversation div.conversation-reply input").val());jQuery.ajax({type:"GET",async:!0,url:M.cfg.wwwroot+"/theme/remui/request_handler.php?action=send_quickmessage_ajax&contactid="+contactid+"&message="+message,success:function(response){"success"===jQuery.trim(response)?(sendMessageNotification("success"),chatMessageAjax(contactid)):sendMessageNotification("fail")},error:function(xhr,status,error){sendMessageNotification("error")}})}),jQuery(document).on("click","button.navbar-toggler.collapsed",function(){window.dispatchEvent(new Event("resize"))})}),jQuery(document.body).click(function(evt){if(0===evt.button&&jQuery(document.documentElement).hasClass("slidePanel-html")){var target=evt.target;target!==evt.currentTarget&&jQuery(target).closest(".slidePanel, .modal, .alertify, .-handled-lick").length||jQuery.slidePanel.hide()}}),jQuery("#id_s_theme_remui_courseperpage").change(function(){this.form.submit(),window.onbeforeunload=null}),jQuery("#id_s_theme_remui_logoorsitename").change(function(){this.form.submit(),window.onbeforeunload=null}),jQuery("#id_s_theme_remui_fontselect").change(function(){this.form.submit(),window.onbeforeunload=null}),jQuery("#id_s_theme_remui_frontpageimagecontent").change(function(){this.form.submit(),window.onbeforeunload=null}),jQuery("#id_s_theme_remui_contenttype").change(function(){this.form.submit(),window.onbeforeunload=null}),jQuery("#id_s_theme_remui_sliderautoplay").change(function(){this.form.submit(),window.onbeforeunload=null}),jQuery("#id_s_theme_remui_slidercount").change(function(){this.form.submit(),window.onbeforeunload=null}),jQuery("#id_s_theme_remui_enablefrontpagecourseimg").change(function(){this.form.submit(),window.onbeforeunload=null}),jQuery("#id_s_theme_remui_enablesectionbutton").change(function(){this.form.submit(),window.onbeforeunload=null}),jQuery("#id_s_theme_remui_enablefrontpageaboutus").change(function(){this.form.submit(),window.onbeforeunload=null}),jQuery("#id_s_theme_remui_poweredbyedwiser").change(function(){this.form.submit(),window.onbeforeunload=null}),jQuery("#id_s_theme_remui_navlogin_popup").change(function(){this.form.submit(),window.onbeforeunload=null});var rotate=1;jQuery(".site-menu-toggle").click(function(event){return event.preventDefault(),jQuery(".collapse").toggleClass("show"),jQuery(".site-menu-toggle i").animate({borderSpacing:getRotation()},{step:function(now){jQuery(this).css("-webkit-transform","rotate("+now+"deg)"),jQuery(this).css("-moz-transform","rotate("+now+"deg)"),jQuery(this).css("transform","rotate("+now+"deg)")},duration:200}),!1}),jQuery("body").on("click","#toggleMenubar a",function(){jQuery(".hidable").hasClass("show")&&jQuery(".hidable").toggleClass("d-none")}),jQuery("#gotop").click(function(){return jQuery("html, body").animate({scrollTop:0},jQuery(window).scrollTop()/6),!1}),jQuery(window).scroll(function(){jQuery(this).scrollTop()>300?jQuery(".to-top").css("display","flex"):jQuery(".to-top").css("display","none")}),jQuery("#page-admin-tool-lp-editcompetencyframework #id_scaleconfigbutton").click(function(e){e.preventDefault()})});