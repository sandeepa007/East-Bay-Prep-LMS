<?php
require_once dirname(dirname(dirname(dirname(__DIR__)))).'/wp-load.php';
$csv_file = $_FILES['wdm_user_csv']['tmp_name'];
//echo $csv_file;

$requiredHeaders = array('First Name', 'Last Name', 'Email');
$f = fopen($csv_file, 'r');
$firstLine = fgets($f); //get first line of csv file
fclose($f);
$foundHeaders = str_getcsv(trim($firstLine), ',', '"'); //parse to array
//check the headers of file


if ($foundHeaders !== $requiredHeaders) {
    wp_send_json_error(
        __(
            'Invalid CSV file data please upload correct file or check the sample CSV for correct format',
            'ebbp-textdomain'
        )
    );
}

if (false !== ($getfile = fopen($csv_file, 'r'))) {
    $data = fgetcsv($getfile, 1000, ',');
    // v1.1.1
    // Check if the number of record is more than the products available
    $cohortId = $_POST['mdl_cohort_id'];

    global $wpdb;
    $tableName = $wpdb->prefix.'bp_cohort_info';
    $query = $wpdb->prepare("SELECT PRODUCTS FROM $tableName WHERE MDL_COHORT_ID = %d", $cohortId);
    $result = $wpdb->get_var($query);
    $products = unserialize($result);

    $minQuantity = @min($products);
    $cuser_id = get_current_user_id();
    $tbl_name = $wpdb->prefix.'moodle_enrollment';
    $query = $wpdb->prepare(
        "SELECT DISTINCT `user_id`,`time` FROM `{$tbl_name}` WHERE `enrolled_by` = %d AND `MDL_COHORT_ID` = '%d'",
        $cuser_id,
        $cohortId
    );
    $enrolled_users = $wpdb->get_results($query);

    $fp = file($_FILES['wdm_user_csv']['tmp_name'], FILE_SKIP_EMPTY_LINES);
    $recordsCount = count($fp) - 1;

    if ($recordsCount > $minQuantity) {
        wp_send_json_error(__('Insufficient quantity avaiable please add more quantity or reduse CSV records', 'ebbp-textdomain'));
    }
    ob_start();

    while (false !== ($data = fgetcsv($getfile, 1000, ','))) {
        $num = count($data);
        $result = $data;
        $str = implode(',', $result);
        $slice = explode(',', $str);

        // Validate data
        do_action('get_user_data');
        ?>
        <div id = 'wdm_csv_error_message'></div>
        <ul class='wdm_new_user'>
            <li>
                <i class='fa fa-times wdm_remove_user'></i>
            </li>
            <li>
                <label  class='lbl_first_name'>
                    <?php _e('Enter First Name : * ', 'ebbp-textdomain');
        ?>
                </label>
                <input type=text class='txt_fname' name='firstname[]' value="<?php echo $slice[0];
        ?>">
            </li>
            <li>
                <label  class='lbl_last_name'>
                    <?php _e('Enter Last name : * ', 'ebbp-textdomain');
        ?>
                </label>
                <input type=text class='txt_lname' name='lastname[]' value="<?php echo $slice[1];
        ?>">
            </li>
            <li>
                <label  class='lbl_email'>
                    <?php _e('Enter E-mail ID : * ', 'ebbp-textdomain');
        ?>
                </label>
                <input type=text class='txt_email' name='email[]' value="<?php echo $slice[2];
        ?>">
            </li>
        </ul>
        <?php
        do_action('display_user_data');
    }
    $data = ob_get_clean();
    wp_send_json_success($data);
}
