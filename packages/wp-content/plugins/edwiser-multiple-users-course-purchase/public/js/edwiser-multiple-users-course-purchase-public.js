(function ($) {
    var dlgOverlyOpct = 0.8;
    var dlgOverlyColor = "black";
    jQuery(document).ready(function () {
        var div = '<div id="eb-lading-parent" class="eb-lading-parent-wrap"><div class="eb-loader-progsessing-anim"></div></div>';
        $("body").append(div);
        div = '<div class="eb-background-div"></div>';

        /**
         * Events to dismiss the responce messages on the enrolle student page.
         */
        $(document).on('click', '.wdm_enroll_warning_msg_dismiss', function () {
            $('.wdm_user_list').remove();
            $('#wdm_eb_message_divider').remove();
            $('#wdm_eb_enroll_user_page').css("border", "0px solid white");
        });
        $(document).on('click', '.wdm_error_msg_dismiss', function () {
            $('.wdm_user_list').remove();
            $('#wdm_eb_message_divider').remove();
            $('#wdm_eb_enroll_user_page').css("border", "0px solid white");
        });
        $(document).on('click', '.wdm_success_msg_dismiss', function () {
            $('.wdm_user_list').remove();
            $('#wdm_eb_message_divider').remove();
            $('#wdm_eb_enroll_user_page').css("border", "0px solid white");
        });

        /**
         * removing error msg
         * @since 1.1.0
         */

        $(document).on('click', '.wdm_success_msg_dismiss, .wdm_select_course_msg_dismiss', function () {
            $('.wdm_select_course_msg').css("display", "none");
            $('#wdm_eb_message_divider').remove();
        });

        $(document).on('click', '.wdm_enroll_warning_msg_dismiss', function () {
            $('.wdm_enroll_warning_message').css("display", "none");
            $('#wdm_eb_message_divider').remove();
        });

        $(document).on('click', '.wdm_success_msg_dismiss', function () {
            $('.wdm_success_message').css("display", "none");
            $('#wdm_eb_message_divider').remove();
        });

        $("body").append(div);
        $('#wdm_eb_upload_csv').hide();
        $('#wdm_avaliable_reg').hide();
        $('#enroll-new-user-btn-div').hide();
        var seat_limit = 0;

        $(document).on("change", "#mucp-cart-group-checkbox", function () {
            if ($(this).prop("checked") == true) {
                processGroupPurchaseCheckbox(1);
            } else if ($(this).prop("checked") == false) {
                processGroupPurchaseCheckbox(0);
            }
        });

        function isItemQtyEql() {
            var allQty = [];
            $('.qty').each(function () {
                allQty.push($(this).val());
            })
            var max = allQty[0];
            for (var cnt = 1; cnt < allQty.length; cnt++) {
                if (max != allQty[cnt]) {
                    return false;
                }
            }
            return true;
        }

        /**
         * This will check is the bulk purchase option is present on screen
         * if the checkbox is present on the screen then disable the quantity box.
         */
        if ($("#wdm_edwiser_self_enroll").length) {
            $(".qty").prop('disabled', true);
            $(".qty").attr("value", "1");
        }

        /**
         * Handle the group purchase enable disable events 
         * If the checkbox is enabled then enable the product quantity box 
         * Disable otherwise
         */
        $("#wdm_edwiser_self_enroll").change(function () {
            var ischecked = $(this).is(':checked');
            if (!ischecked) {
                $(".qty").prop('disabled', true);
                $(".qty").attr("value", "1");
            } else {
                $(".qty").prop('disabled', false);
            }
        });

        function processGroupPurchaseCheckbox(isChecked)
        {

            jQuery.ajax({
                type: 'POST',
                url: ebbpPublic.ajax_url,
                dataType: 'json',
                data: {
                    action: 'check_for_different_products',
                    single_group: isChecked,
                },
                success: function (response) {
                    if (response.success == true) {
                        $(".wdm-diff-prod-qty-error").addClass("wdm-hide");
                        $(".wdm-diff-prod-qty-success").removeClass("wdm-hide");
                        $("#wdm-diff-prod-qty-success-msg").text(response.data);
                    } else {
                        $(".wdm-diff-prod-qty-error").removeClass("wdm-hide");
                        $(".wdm-diff-prod-qty-success").addClass("wdm-hide");
                        $("#wdm-diff-prod-qty-error-msg").text(response.data);
                        $("#mucp-cart-group-checkbox").prop("checked", false);
                    }
                },
                error: function (error) {
                    errorMsgDialog(error);
                }
            });

        }

        //v1.1.1
        /**
         * Show Error message.
         */
        function errorMsgDialog(msg) {
            var error = $(document.createElement('div'));
            msg = '<div class="ui-state-error ui-corner-all"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i><p style = "margin-top:5%;">' + msg + '</p></div>';
            error.append(msg);
            error.dialog({
                title: "Error",
                autoOpen: false,
                modal: true,
                resizable: false,
                dialogClass: "wdm-error-message-dialog",
                buttons: [
                    {
                        text: ebbpPublic.ok,
                        class: 'wdm-dialog-ok-button',
                        click: function () {
                            closeDialog(this);
                        }
                    },
                ],
                open: function (event, ui) {
                    $(".ui-widget-overlay").css({
                        opacity: dlgOverlyOpct,
                        backgroundColor: dlgOverlyColor
                    });
                },
                create: function (event, ui) {
                    $(event.target).parent().css('position', 'fixed');
                },
            }).dialog("open");
        }

        /**
         * Show option to enroll user.
         */
        jQuery('#edb_course_product_name').change(
                function () {
                    getEnrolledUsers(0);
                });

        /**
         *  Handling change on input of Add Quantity Popup
         */
        $(document).on('keypress click input', 'input.add-more-quantity', function (event) {
            if (event.which == 45 || event.which == 46 || event.which == 189) {
                event.preventDefault();
            }
            var grand_total = 0;
            var quantity = $(this).val();
            if (quantity == "") {
                quantity = 0;
            }
            var productId = $(this).attr("id");
            var minQuantity = $("#" + productId + "-min-quantity").html();
            var perProductPrice = $("#" + productId + "-per-product-price").html();
            $("#" + productId + "-total-quantity").html(parseInt(minQuantity) + parseInt(quantity));
            $("#" + productId + "-total-price").html(parseFloat(perProductPrice) * parseInt(quantity));
            var totals = jQuery('.wdm-quantity-total');
            totals.each(function (index, value) {
                grand_total += parseFloat($(this).html());
            });
            $('#add-quantity-total-price').html(grand_total);
        });

        /**
         * Functionality to increase the product quantity uniformly in the add 
         * more product/quantity popup box for the each product 
         */
        $(document).on('keypress click input', '#wdm_new_prod_qty', function (event) {
            if (event.which == 45 || event.which == 46 || event.which == 189) {
                event.preventDefault();
            }
            $(".wdm_new_qty_per_prod").html($(this).val());
            $(".wdm_new_qty_per_new_prod").html($(this).val());
            var grandTotal = calculateGrantTotal();
            $("#add-quantity-total-price").html(grandTotal.toFixed(2));
        });

        /**
         * Functionality to caclulate the total price per product
         * in the add more product/quantity popup
         */
        $(document).on('change DOMSubtreeModified', '.wdm_new_qty_per_prod', function (event) {
            var wdmId = $(this).attr('id');
            var priceId = wdmId + "-per-product-price";
            var totalPriceId = wdmId + "-total-price";
            $("#" + totalPriceId).html(($("#" + wdmId).html() * $("#" + priceId).html()).toFixed(2));
        });
        /**
         * Functionality to caclulate the total price per product
         * in the add more product/quantity popup
         */
        $(document).on('change DOMSubtreeModified', '.wdm_new_qty_per_new_prod', function (event) {
            var wdmId = $(this).attr('id');
            var selectedProd = wdmId + "-wdm-sele-prod";
            if ($("#" + selectedProd).prop('checked') == true) {
                var priceId = wdmId + "-per-product-price";
                var totalPriceId = wdmId + "-total-price";
                $("#" + totalPriceId).html(($("#" + wdmId).html() * $("#" + priceId).html()).toFixed(2));
            }
        });

        /**
         * Functionality to update the product quntity on checkbox checked uncheced in the 
         * add more product functions.
         */
        $(document).on('change', '.wdm_selected_products', function (event) {
            //Get the id of current checkbox.
            var selectedItemId = $(this).attr('id');
            var parentRow = $(this).closest("tr");
            //Genrate the id product price id using checkbox id.
            var priceId = selectedItemId.replace("-wdm-sele-prod", "") + "-per-product-price";
            //Genrate the id product qunatity id using checkbox id.
            var qtyId = selectedItemId.replace("-wdm-sele-prod", "");
            //Genrate the id for grand total using checkbox id.
            var totalPriceId = selectedItemId.replace("-wdm-sele-prod", "") + "-total-price";

            /**
             * Check is the product is selected.
             */
            if ($("#" + selectedItemId).prop('checked') == true) {
                $(parentRow).addClass("wdm-tbl-sel-row");
                //calculate the grand total and add update into the total price column.
                $("#" + totalPriceId).html(($("#" + qtyId).html() * $("#" + priceId).html()).toFixed(2));
            } else {
                //set the total price per product on checkbox uncheked.
                $(parentRow).removeClass("wdm-tbl-sel-row");
                $("#" + totalPriceId).html("0");
            }
            // Upadte the grand total.
            var grandTotal = calculateGrantTotal();
            $("#add-quantity-total-price").html(grandTotal.toFixed(2));

        });

        /**
         * Functionality to calculate the grand total in the add more product/quantity popup
         * @returns {Number}
         */
        function calculateGrantTotal() {
            var total = 0;
            $('.wdm-quantity-total').each(function (event) {
                total += parseFloat($(this).text());
            });
            return total;
        }

        //v1.1.1
        /**
         *  Handling change on input of Add Product Popup
         */
        $(document).on('keypress click input', 'input.add-more-product,.wdm_selected_products', function (event) {
            if (event.which == 45 || event.which == 46 || event.which == 189) {
                event.preventDefault();
            }
            var minVal = $(this).attr('min');
            var grand_total = 0;
            var quantity = $(this).val();
            if (quantity == "" || quantity < minVal) {
                quantity = minVal;
            }
            var selectedProducts = jQuery(".wdm_selected_products");
            var productId = $(this).attr("id");
            var perProductPrice = $("#" + productId + "-per-product-price").html();
            var total = parseInt(perProductPrice) * parseInt(quantity);
            $("#" + productId + "-total-price").html(total);
            var totals = jQuery('.wdm-product-total');
            totals.each(function (index, value) {
                if (jQuery(selectedProducts[index]).is(':checked')) {
                    grand_total += parseInt($(this).html());
                }
            });
            $('#add-product-total-price').html(grand_total);
        });


        function getEnrolledUsers(flag) {

            if (!flag) {
                jQuery('.wdm_error_message').remove();
                jQuery('.wdm_success_message').remove();
                jQuery('.wdm_enroll_warning_message').remove();
            }

            jQuery(".wdm_select_course_msg").css("display", "none");
            jQuery('#wdm_eb_message_divider').remove();
            jQuery("#0").remove();
            var mdlCohortId = jQuery("#edb_course_product_name").children(":selected").val();
            jQuery("#eb-lading-parent").show();
            jQuery.ajax({
                type: 'POST',
                url: ebbpPublic.ajax_url,
                dataType: 'json',
                data: {
                    action: 'get_user_bulk_course_details',
                    mdl_cohort_id: mdlCohortId,
                },
                success: function (response) {
                    jQuery("#eb-lading-parent").hide();
                    var data = response.data;
                    if (response.success == true) {
                        seat_limit = data['seats'];
                        jQuery('#wdm_seats > span ').html(' ' + seat_limit);
                        jQuery('#wdm_user_data').empty();
                        jQuery('.wdm_enrolled_users').html(data.html);
                        jQuery('#enroll-user-table').DataTable({
                            responsive: true,
                            order: [2, 'asc'],
                            select: {
                                style: 'os',
                                selector: 'td.select-checkbox'
                            },
                            "language": {
                                "emptyTable": ebbpPublic.emptyTable
                            }
                        });
                        //jQuery('#wdm_user_data').append("<ul class='wdm_button'><li><input type='button' id='btn_add_new' class='button' value='Add New User'><input type='submit' id='btn_enroll' class='button' value='Enroll' disabled></li></ul>");

                        jQuery('#wdm_user_data').append("<ul class='wdm_button'><li><input type='button' id='btn_add_new' class='button' value='" + ebbpPublic.addNewUser + "'><input type='submit' id='btn_enroll' class='button' value='" + ebbpPublic.enroll + "' disabled></li></ul>");
                        jQuery('#wdm_eb_upload_csv').show();
                        jQuery('#wdm_avaliable_reg').show();
                        jQuery('#enroll-new-user-btn-div').show();
                        if (jQuery('#wdm_seats > span ').html() == 0) {
                            $("#enroll-new-user").prop('disabled', true);
                            $("#enroll-new-user").css("cursor", "not-allowed");
                        } else {
                            $("#enroll-new-user").prop('disabled', false);
                            $("#enroll-new-user").css("cursor", "default");
                            $("#enroll-new-user").hover().css("cursor", "pointer");
                        }
                    } else {
                        errorMsgDialog(data);
                    }
                },
                error: function (error) {
                    jQuery("#eb-lading-parent").hide();
                    errorMsgDialog(error);
                }
            });


        }

        $(document).on('keypress', 'input', '#add-quantity-inp', function (event) {

            if (event.which == 45 || event.which == 189) {
                event.preventDefault();
            }

        });
        var editId;
        function mucpGetEnrolUserForm(width, height, title, button, flag, popUp) {
            var opt = {
                autoOpen: false,
                modal: true,
                maxHeight: 500,
                dialogClass: "wdm-enroll-stud-page-dialog",
                open: function (event, ui) {
                    $(".ui-widget-overlay").css({
                        opacity: dlgOverlyOpct,
                        backgroundColor: dlgOverlyColor
                    });
                    jQuery('#wdm_csv_error_message').hide();
                    jQuery('#enroll_user_form-msg').hide();
                },
                close: function (event) {
                    $('.eb-background-div').hide();
                },
                create: function (event, ui) {
                    $(event.target).parent().css('position', 'fixed');
                },
                buttons: [
                    {
                        text: ebbpPublic.enrollUser,
                        class: "wdm-dialog-enroll-button",
                        click: function () {
                            var success = true;
                            if (flag) {
                                success = validateEnrollUserForm();
                            }
                            if (success) {
                                jQuery("#eb-lading-parent").show();
                                var mdlCohortId = jQuery("#edb_course_product_name").children(":selected").val();
                                var firstname = createArrayOfVariables(jQuery("#wdm_enroll_fname"));
                                var lastname = createArrayOfVariables(jQuery("#wdm_enroll_lname"));
                                var email = createArrayOfVariables(jQuery("#wdm_enroll_email"));
                                $('#enroll_user-pop-up').dialog('close');

                                jQuery.ajax({
                                    type: 'POST',
                                    url: ebbpPublic.ajax_url,
                                    dataType: 'json',
                                    data: {
                                        action: 'create_wordpress_user',
                                        mdl_cohort_id: mdlCohortId,
                                        firstname: firstname,
                                        lastname: lastname,
                                        email: email,
                                    },
                                    success: function (response) {
                                        var data = response.data;
                                        if (response.success == true) {
                                            jQuery("#wdm_eb_message").html(data.msg);
                                            jQuery('.eb-background-div').hide();
                                            jQuery("#loding-icon").removeClass("loader");
                                            $(".wdm_success_message").css("display", "block");
                                            $('#edb_course_product_name').find(":selected").text(data.cohort);
                                            // $('#' + popUp).dialog('close');
                                            getEnrolledUsers(1);
                                        } else {
                                            errorMsgDialog(data);
                                        }
                                    },
                                    error: function (error) {
                                        errorMsgDialog(error);
                                        jQuery("#eb-lading-parent").hide();
                                        $('.eb-background-div').hide();
                                        jQuery("#loding-icon").removeClass("loader");
                                        // $("#enroll-user-form-csv").dialog('close');
                                        $('#enroll_user-pop-up').dialog('close');
                                    }
                                });
                            }
                        }
                    },
                    {
                        text: ebbpPublic.cancel,
                        class: "wdm-dialog-cancel-button",
                        click: function () {
                            $(this).dialog("close");
                        }
                    },
                ]
            };
            return opt;
        }

        //v1.1.1
        function csvGetEnrolUserForm(width, height, title, button, flag, popUp) {
            var opt = {
                autoOpen: false,
                modal: true,
                maxHeight: 500,
                dialogClass: "wdm-enroll-stud-page-dialog wdm-enroll-stud-page-csv-dialog",
                open: function (event, ui) {
                    $(".ui-widget-overlay").css({
                        opacity: dlgOverlyOpct,
                        backgroundColor: dlgOverlyColor
                    });
                    jQuery('#enroll_user_form-msg').hide();
                },
                close: function (event) {
                    $('.eb-background-div').hide();
                },
                create: function (event, ui) {
                    $(event.target).parent().css('position', 'fixed');
                },
                buttons: [
                    {
                        text: ebbpPublic.enrollUser,
                        class: "wdm-dialog-enroll-button",
                        click: function () {
                            var success = true;
                            if (flag) {
                                success = validatecsvEnrollUserForm();
                            }
                            if (success) {
                                jQuery("#eb-lading-parent").show();
                                var cohortId = jQuery("#edb_course_product_name").children(":selected").val();
                                var firstname = createArrayOfVariables(jQuery(".txt_fname"));
                                var cohortName = jQuery("#edb_course_product_name").children(":selected").data("cohort-name");
                                var lastname = createArrayOfVariables(jQuery(".txt_lname"));
                                var email = createArrayOfVariables(jQuery(".txt_email"));
                                $("#enroll-user-form-csv").dialog('close');

                                jQuery.ajax({
                                    type: 'POST',
                                    url: ebbpPublic.ajax_url,
                                    dataType: 'json',
                                    data: {
                                        action: 'create_wordpress_user',
                                        mdl_cohort_id: cohortId,
                                        cohortName: cohortName,
                                        firstname: firstname,
                                        lastname: lastname,
                                        email: email,
                                    },
                                    success: function (response) {
                                        var data = response.data;
                                        if (response.success == true) {
                                            jQuery("#wdm_eb_message").html(data.msg);
                                            jQuery('.eb-background-div').hide();
                                            jQuery("#eb-lading-parent").hide();
                                            jQuery("#loding-icon").removeClass("loader");
                                            $(".wdm_success_message").css("display", "block");
                                            $('#edb_course_product_name').find(":selected").text(data.cohort);
                                            getEnrolledUsers(1);
                                        } else {
                                            errorMsgDialog(data);
                                        }
                                    },
                                    error: function (error) {
                                        errorMsgDialog(error);
                                        jQuery("#eb-lading-parent").hide();
                                        $('.eb-background-div').hide();
                                        jQuery("#loding-icon").removeClass("loader");
                                        $("#enroll-user-form-csv").dialog('close');
                                    }
                                });
                            }
                        }
                    },
                    {
                        text: ebbpPublic.cancel,
                        class: "wdm-dialog-cancel-button",
                        click: function () {
                            $(this).dialog("close");
                        }
                    },
                ]
            };
            return opt;
        }



        function createArrayOfVariables(obj) {
            var arr = [];
            obj.each(function () {
                var eachValue = $(this).val();
                arr.push(eachValue)
                // alert(eachValue);
            });
            return arr;
        }


        var addProduct = {
            autoOpen: false,
            modal: true,
            maxHeight: 650,
            dialogClass: "wdm-enroll-stud-page-dialog",
            open: function (event, ui) {
                $(".ui-widget-overlay").css({
                    opacity: dlgOverlyOpct,
                    backgroundColor: dlgOverlyColor
                });
            },
            close: function (event) {
                closeDialog(this);
            },
            create: function (event, ui) {
                $(event.target).parent().css('position', 'fixed');
            },
            buttons: [
                {
                    text: ebbpPublic.proctocheckout,
                    class: "wdm-dialog-checkout-button",
                    click: proceedToCheckOut,
                },
                {
                    text: ebbpPublic.cancel,
                    class: "wdm-dialog-cancel-button",
                    click: function (event) {
                        closeDialog(this);
                    }
                }
            ]

        };

        var addQuantity = {
            autoOpen: false,
            modal: true,
            maxHeight: 600,
            overflow: 'hidden',
            dialogClass: "wdm-enroll-stud-page-dialog",
            open: function (event, ui) {
                $(".ui-widget-overlay").css({
                    opacity: dlgOverlyOpct,
                    backgroundColor: dlgOverlyColor
                });
            },
            close: function (event) {
                closeDialog(this);
            },
            create: function (event, ui) {
                $(event.target).parent().css('position', 'fixed');
            },
            buttons: [
                {
                    text: ebbpPublic.proctocheckout,
                    class: "wdm-dialog-checkout-button",
                    click: proceedToCheckOut,
                },
                {
                    text: ebbpPublic.cancel,
                    class: "wdm-dialog-cancel-button",
                    click: function (event) {
                        closeDialog(this);
                    }
                }
            ],
        };

        function closeDialog(dialogObj) {
            $(dialogObj).dialog('destroy');
        }

        function proceedToCheckOut() {
            //v1.1.1
            // Check if the Add Quantity or Add Product Pop up is open
            var popup = "";
            if (jQuery(".add-more-quantity").length != 0) {
                var Quantity = jQuery(".wdm_new_qty_per_prod");
                popup = "quantity";
            } else {
                var Quantity = jQuery(".wdm_new_qty_per_new_prod");
                var selectedProducts = jQuery(".wdm_selected_products");
                popup = "products";
            }

            var productArray = {};
            // For Add Products
            if (popup == "products") {
                Quantity.each(function (index, value) {
                    if (jQuery(selectedProducts[index]).is(':checked')) {
                        productArray[$(value).attr('id')] = $(value).html();
                    }
                });
            } else if (popup == "quantity") {
                Quantity.each(function (index, value) {
                    if ($(value).html() != 0) {
                        productArray[$(value).attr('id')] = $(value).html();
                    }
                });
            }
            if (jQuery.isEmptyObject(productArray)) {
                jQuery("#add-quantity-msg").html("<p>" + ebbpPublic.enterQuantity + "</p>");
            } else {
                var cohortId = $("#add-quantity-table").data("cohortid");
                jQuery.ajax({
                    type: 'POST',
                    url: ebbpPublic.ajax_url,
                    dataType: 'json',
                    data: {
                        action: 'ebbp_add_to_cart',
                        mdl_cohort_id: cohortId,
                        productQuantity: productArray
                    },
                    success: function (response) {
                        if (response.success == true) {
                            $("body").css("cursor", "default");
                            window.location = response.data;
                        } else {
                            errorMsgDialog(response.data);
                        }
                    },
                    error: function (response) {
                        $("body").css("cursor", "default");
                        $('#add-quantity-popup').dialog('close');
                        errorMsgDialog(response);
                    }
                });
                $(this).dialog("close");
            }
        }

        var viewCourse = {
            autoOpen: false,
            modal: true,
            maxWidth: 400,
            draggable: true,
            resizable: true,
            dialogClass: "wdm-enroll-stud-page-dialog wdm-view-courses-button",
            open: function (event, ui) {
                $(".ui-widget-overlay").css({
                    opacity: dlgOverlyOpct,
                    backgroundColor: dlgOverlyColor
                });
            },
            close: function (event) {
                closeDialog(this);
            },
            create: function (event, ui) {
                $(event.target).parent().css('position', 'fixed');
            },
            buttons: [
                {
                    text: ebbpPublic.ok,
                    class: 'wdm-dialog-ok-button',
                    click: function () {
                        closeDialog(this);
                    }
                },
            ],
        };
        var editUser = {
            autoOpen: false,
            modal: true,
            maxWidth: 600,
            dialogClass: "wdm-enroll-stud-page-dialog",
            open: function (event, ui) {
                $(".ui-widget-overlay").css({
                    opacity: dlgOverlyOpct,
                    backgroundColor: dlgOverlyColor
                });
                jQuery('#wdm_csv_error_message').hide();
                jQuery('#enroll_user_form-msg').hide();
                $('.ui-dialog-buttonpane').find('button:contains(' + ebbpPublic.close + ')').addClass('wdm-dialog-cancel-button');
                $('.ui-dialog-buttonpane').find('button:contains(' + ebbpPublic.saveChanges + ')').addClass('wdm-dialog-edit-usr-button');
            },
            close: function (event) {
                closeDialog(this);
            },
            create: function (event, ui) {
                $(event.target).parent().css('position', 'fixed');
            },
            buttons: [{
                    text: ebbpPublic.saveChanges,
                    click: function () {
                        var success = validateEnrollUserForm();
                        if (success) {
                            jQuery("#eb-lading-parent").show();
                            $("#enroll_user-form").css("opacity", "0.5");
                            var firstName = $("#wdm_enroll_fname").val();
                            var lastName = $("#wdm_enroll_lname").val();
                            var email = $("#wdm_enroll_email").val();
                            // var role=jQuery(".role-drop-down").val();
                            jQuery.ajax({
                                type: 'POST',
                                url: ebbpPublic.ajax_url,
                                dataType: 'json',
                                data: {
                                    action: 'edit_user',
                                    uid: editId,
                                    firstname: firstName,
                                    lastname: lastName,
                                    email: email,
                                },
                                success: function (response) {
                                    var data = response.data;
                                    $("#eb-lading-parent").hide();
                                    if (response.success == true) {
                                        $("#enroll_user-form").css("opacity", "1");
                                        $('.eb-background-div').hide();
                                        $('#enroll_user-pop-up').dialog('close');
                                        $("#wdm_eb_message").html(data);
                                        getEnrolledUsers(0);
                                    } else {
                                        $('#enroll_user-pop-up').dialog('close');
                                        $("#wdm_eb_message").html(data);
                                    }
                                },
                                error: function (error) {
                                    $("#eb-lading-parent").hide();
                                    $('#enroll_user-pop-up').dialog('close');
                                    $("#enroll_user-form").css("opacity", "1");
                                    $('.eb-background-div').hide();
                                    $('#enroll_user-pop-up').dialog('close');
                                    errorMsgDialog(error);
                                }
                            });
                        }
                    }},
                {
                    text: ebbpPublic.close,
                    click: function (event) {
                        closeDialog(this);
                    }
                }]
        };

        $(document).on("click", "#enroll-new-user", function (event) {
            event.preventDefault();
            $('#enroll_user-pop-up').prop('title', ebbpPublic.enrollNewUser);
            setFormValues();
            var opt = mucpGetEnrolUserForm(300, 400, ebbpPublic.enrollUser, ebbpPublic.enrollUser, 1, "enroll_user-pop-up");
            jQuery("#enroll_user-pop-up").dialog(opt).dialog("open");
            var cid = jQuery("#edb_course_product_name").val();
            jQuery("#enroll_user_course").val(cid);

        });

        // v1.1.1
        // On Add Quantity Button Click
        $(document).on("click", "#add-quantity-button", function (event) {
            event.preventDefault();
            var mdlCohortId = jQuery("#edb_course_product_name").children(":selected").val();
            if (mdlCohortId != "0") {
                //Changes
                jQuery.ajax({
                    type: 'POST',
                    url: ebbpPublic.ajax_url,
                    dataType: 'json',
                    data: {
                        action: 'ebbp_add_quantity',
                        mdl_cohort_id: mdlCohortId,
                    },
                    success: function (response) {
                        if (response.success == true) {
                            $("#add-quantity-popup").empty();
                            $("#add-quantity-popup").html(response.data);
                            $('#add-quantity-popup').prop('title', ebbpPublic.addQuantity);
                            $('.eb-background-div').show();
                            $("#add-quantity-popup").dialog(addQuantity).dialog("open");
                        } else {
                            errorMsgDialog(response.data);
                        }
                    },
                    error: function (response) {
                        errorMsgDialog(response);
                    }
                });
            } else {
                jQuery(".wdm_select_course_msg").css("display", "block");
            }
        });

        // v1.1.1
        // On Add Course Button Click
        $(document).on("click", "#add-product-button", function (event) {
            event.preventDefault();
            var mdlCohortId = jQuery("#edb_course_product_name").children(":selected").val();
            var cohortName = jQuery("#edb_course_product_name").children(":selected").html();
            if (mdlCohortId != "0") {
                jQuery.ajax({
                    type: 'POST',
                    url: ebbpPublic.ajax_url,
                    dataType: 'json',
                    data: {
                        action: 'ebbp_add_new_product',
                        mdl_cohort_id: mdlCohortId,
                    },
                    success: function (response) {
                        if (response.success == true) {
                            var respCont = response.data;
                            $("#add-quantity-popup").empty();
                            $("#add-quantity-popup").html(respCont.data);
                            $('#add-quantity-popup').prop('title', ebbpPublic.addNewProductsIn + respCont.cohort + ' group');
                            $('.eb-background-div').show();
                            $("#add-quantity-popup").dialog(addProduct).dialog("open");
                        } else {
                            errorMsgDialog(response['data']);
                        }
                    },
                    error: function (response) {
                        errorMsgDialog(response);
                    }
                });
            } else {
                jQuery(".wdm_select_course_msg").css("display", "block");
            }
        });

        $(document).on("click", "#view-associated-button", function (event) {
            event.preventDefault();
            var mdlCohortId = jQuery("#edb_course_product_name").children(":selected").val();
            if (mdlCohortId != 0) {
                $('.eb-background-div').show();
                jQuery.ajax({
                    type: 'POST',
                    url: ebbpPublic.ajax_url,
                    dataType: 'json',
                    data: {
                        action: 'get_enrol_user_course',
                        mdl_cohort_id: mdlCohortId,
                    },
                    success: function (response) {
                        var data=response.data;
                        if (response.success == true) {
                            jQuery("#eb-lading-parent").hide();
                            jQuery("#enroll-user-form-pop-up").html(data.html);
                            $('#enroll-user-form-pop-up').prop('title', data.cohortName + " " + ebbpPublic.associatedCourse);
                            jQuery("#enroll-user-form-pop-up").dialog(viewCourse).dialog("open");
                        }else{
                            errorMsgDialog(response.data);
                        }
                    },
                    error: function (error) {
                        errorMsgDialog(error);
                    }
                });
            } else {
                jQuery(".wdm_select_course_msg").css("display", "block");
            }
        });

        $(document).on("click", ".edit-enrolled-user", function (event) {
            event.preventDefault();
            editId = jQuery(this).attr("id");

            jQuery.ajax({
                type: 'POST',
                url: ebbpPublic.ajax_url,
                dataType: 'json',
                data: {
                    action: 'get_enrol_user_details',
                    uid: editId
                },
                success: function (response) {
                    setFormValues(response.FirstName, response.lastname, response.email);
                },
                error: function () {
                    alert("failed");
                }
            });
            $('#enroll_user-pop-up').prop('title', 'Edit User');
            jQuery("#enroll_user-pop-up").dialog(editUser).dialog("open");
        });




        function setFormValues(FirstName = '', lastname = '', email = ''){
            jQuery("#wdm_enroll_fname").val(FirstName);
            jQuery("#wdm_enroll_lname").val(lastname);
            jQuery("#wdm_enroll_email").val(email);

        }

        /**
         * create fields for new user.
         */
        jQuery('#wdm_user_data').delegate(
                '#btn_add_new', 'click', function () {
                    var numItems = jQuery('#wdm_enroll_fname').length;
                    if (numItems < seat_limit) {
                        jQuery('.wdm_button').before(
                                "<ul class='wdm_new_user'>\n\
                                        <li>\n\
                                            <i class='fa fa-times-circle wdm_remove_user'></i>\n\
                                        </li>\n\
                                        <li>\n\
                                            <label for='lbl_first_name'>" + ebbpPublic.enterFirstName + "</label>\n\
                                            <input type=text class='txt_fname' name='firstname[]' required>\n\
                                        </li>\n\
                                        <li>\n\
                                            <label class='lbl_last_name'>" + ebbpPublic.enterLastName + "</label>\n\
                                            <input type=text class='txt_lname' name='lastname[]' required>\n\
                                        </li>\n\
                                        <li>\n\
                                            <label class='lbl_email'>" + ebbpPublic.enterEmailName + "</label>\n\
                                            <input type='email' class='txt_email' name='email[]' ' required>\n\
                                        </li>\n\
                                        </ul>");
                        jQuery('#btn_enroll').removeAttr('disabled');
                        $(this).toggleClass("active");
                    }
                    if (numItems + 1 == seat_limit) {
                        jQuery(this).attr('disabled', 'disabled');
                    }
                }
        );

        function validateEnrollUserForm() {

            var numItems = jQuery('#wdm_enroll_fname').length;
            var empty_flag = 0;
            var username_flag = 0;
            var email_flag = 0;
            jQuery('#wdm_enroll_fname').each(function () {
                if (jQuery(this).val() == '') {
                    empty_flag = 1;
                    return false;
                }
            });
            jQuery('#wdm_enroll_lname').each(function () {
                if (jQuery(this).val() == '') {
                    empty_flag = 1;
                    return false;
                }
            });
            jQuery('.txt_uname').each(function () {
                if (jQuery(this).val() == '') {
                    empty_flag = 1;
                    return false;
                }
            });
            var emailReg = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            jQuery('#wdm_enroll_email').each(function () {
                if (jQuery(this).val() == '') {
                    empty_flag = 1;
                    return false;
                } else {
                    if (!emailReg.test(jQuery(this).val())) {
                        //jQuery('#wdm_eb_message').append("<p>" + jQuery(this).val() + " is not a valid email id </p>");

                        //jQuery('#enroll_user_form-msg').html("<p>" + ebbpPublic.invalidEmailId + " " + jQuery(this).val() + "</p>");
                        jQuery('#enroll_user_form-msg').html('<div class="wdm_select_course_msg" style = "display:block"><i class="fa fa-times-circle wdm_select_course_msg_dismiss"></i><label class="wdm_enroll_warning_message_label">' + ebbpPublic.invalidEmailId + '</div>');
                        // jQuery('#enroll_user_form-msg p').addClass('wdm_error_message');
                        email_flag = 1;
                    }
                }
            });
            if (empty_flag == 1 || email_flag == 1 || username_flag == 1) {
                if (empty_flag == 1) {
                    //jQuery('#enroll_user_form-msg').html("<p>" + ebbpPublic.mandatoryMsg + "</p>");
                    jQuery('#enroll_user_form-msg').html('<div class="wdm_select_course_msg" style = "display:block"><i class="fa fa-times-circle wdm_select_course_msg_dismiss"></i><label class="wdm_enroll_warning_message_label">' + ebbpPublic.mandatoryMsg + '</div>');
                }
                jQuery('#enroll_user_form-msg p').addClass('wdm_error_message');
                jQuery('#enroll_user_form-msg').show();
                /*jQuery('html,body').animate({scrollTop: 0}, '500', 'swing');*/
                return false;
            } else {
                return true;
            }
        }

        $(document).on('click', 'input', '.txt_fname, .txt_lname, .txt_email', function (event) {
            jQuery(this).css('border', 'solid 1px #000');
        });

        function validatecsvEnrollUserForm() {

            var numItems = jQuery('.wdm_new_user').length;
            var empty_flag = 0;
            var username_flag = 0;
            var email_flag = 0;
            jQuery('.txt_fname').each(function () {
                if (jQuery(this).val() == '') {
                    empty_flag = 1;
                    jQuery(this).css('border', '1px solid red');
                    return false;
                }
            });
            jQuery('.txt_lname').each(function () {
                if (jQuery(this).val() == '') {
                    empty_flag = 1;
                    jQuery(this).css('border', '1px solid red');
                    return false;
                }
            });

            var emailReg = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            jQuery('.txt_email').each(function () {
                if (jQuery(this).val() == '') {
                    empty_flag = 1;
                    jQuery(this).css('border', '1px solid red');
                    return false;
                } else {
                    if (!emailReg.test(jQuery(this).val())) {
                        //jQuery('#wdm_eb_message').append("<p>" + jQuery(this).val() + " is not a valid email id </p>");

                        //jQuery('#enroll_user_form-msg').html("<p>" + ebbpPublic.invalidEmailId + " " + jQuery(this).val() + "</p>");
                        jQuery('#wdm_csv_error_message').html('<div class="wdm_select_course_msg" style = "display:block"><i class="fa fa-times-circle wdm_select_course_msg_dismiss"></i><label class="wdm_enroll_warning_message_label">' + ebbpPublic.invalidEmailId + '</div>');
                        // jQuery('#enroll_user_form-msg p').addClass('wdm_error_message');
                        email_flag = 1;
                        jQuery(this).css('border', '1px solid red');
                    }
                }
            });
            if (empty_flag == 1 || email_flag == 1 || username_flag == 1) {
                if (empty_flag == 1) {
                    //jQuery('#enroll_user_form-msg').html("<p>" + ebbpPublic.mandatoryMsg + "</p>");
                    jQuery('#wdm_csv_error_message').html('<div class="wdm_select_course_msg" style = "display:block"><i class="fa fa-times-circle wdm_select_course_msg_dismiss"></i><label class="wdm_enroll_warning_message_label">' + ebbpPublic.mandatoryMsg + '</div>');
                }
                //jQuery('#enroll_user_form-msg p').addClass('wdm_error_message');
                jQuery('#wdm_csv_error_message').show();
                /*jQuery('html,body').animate({scrollTop: 0}, '500', 'swing');*/
                return false;
            } else {
                return true;
            }
        }



        /**
         * Trigger Custom event for uploading CSV
         */
        jQuery('#wdm_user_csv_upload').click(
                function () {
                    jQuery('#wdm_user_csv').trigger('EnrollUsersEvent');
                }
        );
        /**
         * Upload user details using CSV.
         */
        jQuery('#wdm_user_csv').on(
                "EnrollUsersEvent", function () {
                    var formdata = new FormData();
                    var id = jQuery("#edb_course_product_name").children(":selected").val();
                    var cohortName = jQuery("#edb_course_product_name").children(":selected").data("cohort-name");
                    var i = 0, file;
                    var len = this.files.length;
                    if (len > 0) {
                        for (; i < len; i++) {
                            file = this.files[i];
                            if (formdata) {
                                formdata.append("wdm_user_csv", file);
                                formdata.append("mdl_cohort_id", id);
                            }
                        }
                    } else
                    {
                        formdata = false;
                        alert(ebbpPublic.uploadFileFirst);
                    }
                    if (formdata) {
                        var url;
                        url = ebbpPublic.wdm_user_import_file;
                        jQuery.ajax(
                                {
                                    type: 'POST',
                                    url: url,
                                    data: formdata,
                                    processData: false,
                                    contentType: false,
                                    success: function (response) {//response is value returned from php
                                        //v1.1.1
                                        var data=response.data;
                                        if(response.success==true){
                                            jQuery("#enroll-user-form-csv").html(data);
                                            var opt = csvGetEnrolUserForm(400, 500, "title", "button", 1, "enroll-user-form-csv");
                                            jQuery("#enroll-user-form-csv").dialog(opt).dialog('open');
                                            jQuery('#btn_enroll').removeAttr('disabled');
                                        }else{
                                            errorMsgDialog(data);
                                        }
                                    },
                                    error:function(error){
                                        errorMsgDialog(error);
                                    }
                                }
                        );
                    }
                }
        );

        /**
         * Remove extra user fields
         */
        jQuery('#enroll-user-form-csv').delegate(
                '.wdm_remove_user', 'click', function () {
                    jQuery(this).parent().parent().remove();

                    if (jQuery('.wdm_remove_user').length <= 0) {
                        jQuery('#enroll-user-form-csv').dialog('close');
                        jQuery('#btn_enroll').attr('disabled', 'disabled');
                    }
                    jQuery("#btn_add_new").removeAttr('disabled');

                }
        );
    }
    );
})(jQuery);
