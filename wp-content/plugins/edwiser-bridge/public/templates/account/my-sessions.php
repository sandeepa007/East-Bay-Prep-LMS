<div class="eb-cph-wrapper">
    <div class="eb-my-courses-wrapper">
        <h2 style=""><?php _e('Semi-Private Sessions Enrollment History', 'eb-textdomain');
?></h2>
    </div>
    <table id="wdm_user_order_history" class="display">
        <thead>
            <tr>
                <th><?php _e('Session Title', 'eb-textdomain'); ?></th>
                <th><?php _e('Session Organizer', 'eb-textdomain'); ?></th>
                <th><?php _e('Session Status', 'eb-textdomain'); ?></th>
                <th><?php _e('Enrollment Date', 'eb-textdomain'); ?></th>
                <th><?php _e('Session Date and Time', 'eb-textdomain'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($user_orders as $order) {
                    $row = "";
                     if (is_array($order['ordered_item'])) {
                        $ordItems = $order['ordered_item'];
                    } else {
                        $ordItems = array($order['ordered_item']);
                    }
                     if(count($ordItems) == 0)
                    {
                ?>
                <tr>                    
                    <?php
                        $ww_order = wc_get_order( $order['order_id'] );
                        $row .= "<ul class='eb-user-order-courses'>";
                        // Iterating through each WC_Order_Item_Product objects
                        foreach ($ww_order->get_items() as $item_key => $item_values){
                            ## Using WC_Order_Item methods ##
                            // Item ID is directly accessible from the $item_key in the foreach loop or
                            $item_id = $item_values->get_id();
                            //var_dump($item_id);
                            global $wpdb;
$results = $wpdb->get_results( "select post_id, meta_value from $wpdb->postmeta where meta_key = '_mep_atnd_".$order['order_id']."'", ARRAY_A );

                            $veb_post = wp_get_single_post($results[0]['post_id']);

                            ## Using WC_Order_Item_Product methods ##
                            $item_name = $veb_post->post_title; // Name of the product
                            $mep_event_start_date = get_post_meta($results[0]['post_id'],'mep_event_start_date');
                            $mep_event_end_date = get_post_meta($results[0]['post_id'],'mep_event_end_date');
                            foreach (get_the_terms($results[0]['post_id'], 'mep_org') as $cat) {
                               $organizer = $cat->name;
                            }


                            ## Access Order Items data properties (in an array of values) ##
                            $item_data = $item_values->get_data();
                            $product_name = $item_data['name'];
                            $row .= "<li>$product_name</li>";
                       }
                         $row .= "</ul>";
                         if(strtotime($mep_event_start_date[0]) > time() )
                         {
                            $status = "Active";
                         }else{
                            $status = "Expired";
                         }
                    ?>
                    <td><?php echo $item_name; ?></td>
                    <td><?php echo $organizer; ?></td>
                    <td><?php echo $status; ?></td>
                    <td><?php echo $order['date']; ?> </td>
                    <td><?php 
                                 $row2 = "<ul class='eb-user-order-courses'>";
                                 $row2 .= "<li>Start : ".$mep_event_start_date[0]."</li>";
                                 $row2 .= "<li>End : ".$mep_event_end_date[0]."</li>";
                                 $row2 .= "</ul>";
                                 echo $row2;
                     ?></td>
                </tr>
                <?php
                }
            }
            do_action('eb_after_order_history');
            ?>
        </tbody>
    </table>
</div>
