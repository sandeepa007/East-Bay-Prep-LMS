<?php
if (is_user_logged_in()) {
    //    wp_enqueue_style('wdm_bootstrap_css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css');
    global $wpdb;
    $user = wp_get_current_user();
    $tableName = $wpdb->prefix . "bp_cohort_info";
    $query = $wpdb->prepare("SELECT ID, COHORT_NAME, MDL_COHORT_ID, PRODUCTS FROM $tableName WHERE COHORT_MANAGER = %d AND SYNC='1'", $user->ID);
    $result = $wpdb->get_results($query, ARRAY_A);
    /*    $group_products = get_user_meta(get_current_user_id(), 'group_products', true);
      if (!is_array($group_products)) {
      $group_products = array();
      } */
    ?>
    <div id="wdm_eb_enroll_user_page">
        <div id='wdm_eb_message'>
            <div class="wdm_select_course_msg">
                <i class='fa fa-times-circle wdm_select_course_msg_dismiss'></i>
                <lable class='wdm_enroll_warning_message_lable'>
                    <?php _e('Please select Group', 'ebbp-textdomain');
                    ?>
            </div>
        </div>
        <div id="wdm-eb-enroll-msg"></div>
        <form name="wdm_eb_enroll_user" id ="wdm_eb_enroll_user" method="POST" enctype="multipart/form-data">
            <span class="wdm_eb_lable"><?php _e('Select Group:', 'ebbp-textdomain');
                    ?></span>
            <div class="course-select">
                <ul id="wdm-course-button">
                    <li class="enroll-button-grid"><div>
                            <select id="edb_course_product_name" name="edb_course_product_name">
                                <option value="0">
                                    <?php _e('Select Group', 'ebbp-textdomain'); ?>
                                </option>
                                <?php
                                foreach ($result as $row) {
                                    ?>
                                    <option value="<?php echo $row['MDL_COHORT_ID']; ?>">
                                        <?php
                                        $productsQuantity = unserialize($row['PRODUCTS']);
                                        $productsQuantity = array_values($productsQuantity);
                                        $productsQuantity = min($productsQuantity);
                                        echo str_replace($user->user_login . "_", "", $row['COHORT_NAME']) . "<span> (" . $productsQuantity . ") </span>";
                                        ?>
                                    </option>
                                    <?php
                                }
                                ?>
                            </select>
                            <div id="loding-icon"></div>
                        </div>
                    </li>
                    <?php
                    /**
                     * add quatity and add view associated course.
                     * @since 1.1.0
                     */
                    ?>
                    <li class="enroll-button-grid">
                        <div>
                            <button class="enroll-student-page-button" id="view-associated-button"><?php _e('Associated Courses', 'ebbp-textdomain');
                    ?></button>
                        </div>
                    </li>
                    <li class="enroll-button-grid">
                        <div>
                            <button class="enroll-student-page-button" id="add-product-button"><?php _e('Add Product', 'ebbp-textdomain');
                    ?></button>
                        </div>
                    </li>
                    <li class="enroll-button-grid">
                        <div>
                            <button class="enroll-student-page-button" id="add-quantity-button"><?php _e('Add Quantity', 'ebbp-textdomain');
                    ?></button>
                        </div>
                    </li>


                    <div id="add-quantity-popup"></div>

            </div>
            <div id='wdm_avaliable_reg'>
                <label id="wdm_seats">
                    <?php _e('Number of Seats Available :', 'ebbp-textdomain');
                    ?> <span> 0 </span>
                </label>
            </div>
            <div id="enroll-new-user-btn-div">
                <button id='enroll-new-user'><?php _e('Enroll New User', 'ebbp-textdomain');
                    ?> </button>
                <div title='Enroll User' id='enroll-user-form-pop-up'></div>
            </div>
            <div class = 'wdm_enrolled_users'> </div>

            <div id="wdm_eb_upload_csv">
                <ul>
                    <li>
                        <input type="file" name="wdm_user_csv" id="wdm_user_csv" class="file" accept=".csv" data-show-preview="false" data-show-upload="false">
                    </li>
                    <li>
                        <a id="wdm_csv_link" href="<?php echo plugin_dir_url(dirname(__FILE__)) . 'upload_users_sample.csv' ?>">
                            <?php _e('Download Sample CSV', 'ebbp-textdomain'); ?>
                        </a>
                    </li>
                    <li>
                        <input type='button' id="wdm_user_csv_upload" class='button' value='<?php _e('Upload CSV', 'ebbp-textdomain');
                            ?>'>
                    </li>
                </ul>
            </div>
        </form>
        <div title="Enroll Users" id="enroll-user-form-csv"></div>
        <div id="enroll-user-form-pop-up">
            <div id="enroll_user-pop-up">
                <div id="enroll_user_form-msg"></div>
                <form  id="enroll_user-form" method="POST" enctype="multipart/form-data">
                    <div class="enroll_user-row">
                        <label><?php _e('First name *', 'ebbp-textdomain'); ?></label>
                        <input class="wdm-enrol-form-input" id="wdm_enroll_fname" type='text' name='firstname[]' placeholder='Enter first name' value="" />
                    </div>
                    <div class="enroll_user-row">
                        <label><?php _e('Last name *', 'ebbp-textdomain'); ?></label>
                        <input class="wdm-enrol-form-input" id="wdm_enroll_lname" type='text' name='lastname[]' placeholder='Enter Last name' value="" />
                    </div>
                    <div class="enroll_user-row">
                        <label><?php _e('Email Address *', 'ebbp-textdomain'); ?></label>
                        <input class="wdm-enrol-form-input" id="wdm_enroll_email" type='email' name='email[]' placeholder='Enter Email Address' value="" />
                    </div>
                    <input  id='enroll_user_course' name='edb_course_product_name' type='hidden'/>
                </form>
                <div id="popup-loding-icon" class="loader pop-up-loader"></div>
            </div>
        </div>
        <!-- <form id="enroll-user-form"></form> -->
    </div>
    </div>
    <?php
} else {
    /*
     * Show Login Request Message if user is not logged in.
     * @author Pandurang
     * @since 1.0.1
     */
    ?>
    <div class="wdmebbp-wrapper-login-req alert alert-warning">
        <span><?php _e('Login required to enroll users!', 'ebbp-textdomain');
    ?></span>
        <a class="btn btn-info" href="<?php echo wp_login_url(get_permalink());
    ?>"><?php _e('Sign in', 'ebbp-textdomain');
    ?></a>
    </div>
    <?php
}
