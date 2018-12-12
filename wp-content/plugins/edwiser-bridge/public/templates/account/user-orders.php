<div class="eb-cph-wrapper">
    <div class="wdm-transaction-header">
        <h4 style=""><?php _e('Course and Semi Session Purchase History', 'eb-textdomain');
?></h4>
    </div>
    <table id="wdm_user_order_history" class="display">
        <thead>
            <tr>
                <th><?php _e('Order ID', 'eb-textdomain'); ?></th>
                <th><?php _e('Ordered Items', 'eb-textdomain'); ?></th>
                <th><?php _e('Order Date', 'eb-textdomain'); ?></th>
                <th><?php _e('Status', 'eb-textdomain'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($user_orders as $order) {
                    $row = "";
                ?>
                <tr>
                    <td><strong>#<?php echo $order['order_id']; ?></strong></td>
                    <?php
                    
                    if (is_array($order['ordered_item'])) {
                        $ordItems = $order['ordered_item'];
                    } else {
                        $ordItems = array($order['ordered_item']);
                    }

                    if(count($ordItems) == 0)
                    {
                        $ww_order = wc_get_order( $order['order_id'] );
                        $row .= "<ul class='eb-user-order-courses'>";
                        // Iterating through each WC_Order_Item_Product objects
                        foreach ($ww_order->get_items() as $item_key => $item_values){


                            ## Using WC_Order_Item methods ##

                            // Item ID is directly accessible from the $item_key in the foreach loop or
                            $item_id = $item_values->get_id();

                            ## Using WC_Order_Item_Product methods ##

                            $item_name = $item_values->get_name(); // Name of the product
                            
                            $item_type = $item_values->get_type(); // Type of the order item ("line_item")

                            $product = $item_values->get_product(); // the WC_Product object

                            ## Access Order Items data properties (in an array of values) ##
                            $item_data = $item_values->get_data();

                            $product_name = $item_data['name'];
                            /*$product_id = $item_data['product_id'];
                            $variation_id = $item_data['variation_id'];
                            $quantity = $item_data['quantity'];
                            $tax_class = $item_data['tax_class'];
                            $line_subtotal = $item_data['subtotal'];
                            $line_subtotal_tax = $item_data['subtotal_tax'];
                            $line_total = $item_data['total'];
                            $line_total_tax = $item_data['total_tax'];*/
                            /*if ($product_name == '') {
                                $title = __('Not Available', 'eb-textdomain');
                            } else {
                                $title = "<a href='" . get_permalink( $item_id ) . "'/>" .$product_name . "</a>";
                            }*/
                            $row .= "<li>$product_name</li>";

                       }
                         $row .= "</ul>";

                    }else{
                        $row .= "<ul class='eb-user-order-courses'>";
                        foreach ($ordItems as $item) {
                            if (get_the_title($item) == '') {
                                $title = __('Not Available', 'eb-textdomain');
                            } else {
                                $title = "<a href='" . get_permalink($item) . "'/>" . get_the_title($item) . "</a>";
                            }
                            $row .= "<li>$title</li>";
                        }
                        $row .= "</ul>";
                    }

                    
                    ?>
                    <td><?php echo $row; ?></td>
                    <td><?php echo $order['date']; ?> </td>
                    <td><?php _e(ucfirst($order['status']), 'eb-textdomain'); ?></td>
                </tr>
                <?php
            }
            do_action('eb_after_order_history');
            ?>
        </tbody>
    </table>
</div>
