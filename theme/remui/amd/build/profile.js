/*!
 * remark (http://getbootstrapadmin.com/remark)
 * Copyright 2018 amazingsurge
 * Licensed under the Themeforest Standard Licenses
 */

define(["jqueryui","theme_remui/jquery-asPieProgress","theme_remui/aspieprogress"],function(jqui,pieprogress,PieProgress){jQuery("#editprofile .form-horizontal #btn-save-changes").click(function(){jQuery("div#error-message").show(),jQuery("div#error-message").removeClass("alert-danger").addClass("alert-success"),jQuery("div#error-message p").html("Saving...");var fname=jQuery("#first_name").val(),lname=jQuery("#surname").val(),description=jQuery.trim(jQuery("#description").val()),city=jQuery.trim(jQuery("#city").val()),country=jQuery("#editprofile .form-horizontal #country option:selected").val();return""===fname?(jQuery("div#error-message").show(),jQuery("div#error-message").removeClass("alert-success").addClass("alert-danger"),jQuery("div#error-message p").html(M.util.get_string("enterfirstname","theme_remui")),jQuery("#first_name").focus(),!1):""===lname?(jQuery("div#error-message").show(),jQuery("div#error-message").removeClass("alert-success").addClass("alert-danger"),jQuery("div#error-message p").html(M.util.get_string("enterlastname","theme_remui")),jQuery("#surname").focus(),!1):void jQuery.ajax({type:"GET",async:!0,url:M.cfg.wwwroot+"/theme/remui/request_handler.php?action=save_user_profile_settings_ajax&fname="+fname+"&lname="+lname+"&description="+description+"&city="+city+"&country="+country,success:function(data){jQuery("div#error-message").show(),jQuery("div#error-message").removeClass("alert-danger").addClass("alert-success"),jQuery("div#error-message p").css("margin","0").html(M.util.get_string("detailssavedsuccessfully","theme_remui")),jQuery(".profile-user").text(fname+" "+lname),jQuery(".usermenu a.navbar-avatar span.username").text(fname+" "+lname),jQuery("#user-description").text(description)},error:function(requestObject,error,errorThrown){jQuery("div#error-message").removeClass("alert-success").addClass("alert-danger"),jQuery("div#error-message p").css("margin","0").html(error+" : "+errorThrown+", "+M.util.get_string("actioncouldnotbeperformed","theme_remui"))}})}),jQuery(".remui-course-progress").asPieProgress({namespace:"asPieProgress"})});