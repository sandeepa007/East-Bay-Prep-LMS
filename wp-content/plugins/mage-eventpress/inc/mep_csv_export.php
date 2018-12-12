<?php

add_action( 'admin_notices', 'export_btn' );
function export_btn() {
    global $typenow;
    if ($typenow == 'mep_events_attendees') {
        ?>

        <div class="wrap alignright">
            <form method='get' action="edit.php">
                <input type="hidden" name='post_type' value="mep_events_attendees"/>
                <input type="hidden" name='noheader' value="1"/>
                <?php 
                  if ( isset( $_GET['meta_value'] )) {
                ?>
              <input type="hidden" name='meta_value' value="<?php echo $_GET['meta_value']; ?>"/>
              <?php } ?>                
                <input style="display:none" type="radio" name='format' id="formatCSV" value="csv" checked="checked"/>
                <input type="submit" name='export' id="csvExport" value="<?php _e('Export to CSV','mage-eventpress'); ?>"/>
            </form>
        </div>

        <?php
    }
}


spee_dashboard();
function spee_dashboard() {
  global $wpdb;
  if ( isset( $_GET['export'] )) {
    
      
   
      
      // Create new PHPExcel object
      $objPHPExcel = new PHPExcel();
      
      // Set document properties
      
      // Add some data
      $objPHPExcel->setActiveSheetIndex(0);
      $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Unique ID');
      $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Full Name');
      $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Email');
      $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Phone');
      $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Addresss');
      $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Tee Size');
      $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Ticket');
      $objPHPExcel->getActiveSheet()->setCellValue('H1', 'Event');
      
      $objPHPExcel->getActiveSheet()->getStyle('A1:H1')->getFont()->setBold(true);
      $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn('A:H')->setAutoSize(true);



if(isset($_GET['meta_value'])){
$meta = $_GET['meta_value'];
      $query = "SELECT post_id
        FROM {$wpdb->prefix}postmeta
        WHERE meta_value = $meta
        ORDER BY meta_id ASC";
}else{
  $query = "SELECT ID
        FROM {$wpdb->prefix}posts
        WHERE post_type ='mep_events_attendees'
        ORDER BY ID ASC";
}

      $posts   = $wpdb->get_results($query);
      if ( $posts ) {
        foreach ( $posts as $i=>$post ) {
          if(isset($_GET['meta_value'])){
          $post_id = $post->post_id;
        }else{
          $post_id = $post->ID;
        }

          $objPHPExcel->getActiveSheet()->setCellValue('A'.($i+2), get_post_meta( $post_id, 'ea_user_id', true ).get_post_meta( $post_id, 'ea_order_id', true ).$post_id);
          $objPHPExcel->getActiveSheet()->setCellValue('B'.($i+2), get_post_meta( $post_id, 'ea_name', true ));
          $objPHPExcel->getActiveSheet()->setCellValue('C'.($i+2), get_post_meta( $post_id, 'ea_email', true ));
          $objPHPExcel->getActiveSheet()->setCellValue('D'.($i+2), get_post_meta( $post_id, 'ea_phone', true ));
          $objPHPExcel->getActiveSheet()->setCellValue('E'.($i+2), get_post_meta( $post_id, 'ea_address_1', true )."<br/>".get_post_meta( $post_id, 'ea_address_2', true )."<br/>".get_post_meta( $post_id, 'ea_state', true ).", ".get_post_meta( $post_id, 'ea_city', true ).", ".get_post_meta( $post_id, 'ea_country', true ));
          $objPHPExcel->getActiveSheet()->setCellValue('F'.($i+2), get_post_meta( $post_id, 'ea_tshirtsize', true ));
          $objPHPExcel->getActiveSheet()->setCellValue('G'.($i+2), get_post_meta( $post_id, 'ea_ticket_type', true ));

          $objPHPExcel->getActiveSheet()->setCellValue('H'.($i+2), get_post_meta( $post_id, 'ea_event_name', true ));
        }
      }

      // Rename worksheet
      //$objPHPExcel->getActiveSheet()->setTitle('Simple');
      
      // Set active sheet index to the first sheet, so Excel opens this as the first sheet
      $objPHPExcel->setActiveSheetIndex(0);
      
      // Redirect output to a client’s web browser
      ob_clean();
      ob_start();
      switch ( $_GET['format'] ) {
        case 'csv':
          // Redirect output to a client’s web browser (CSV)
          header("Content-type: text/csv");
          header("Cache-Control: no-store, no-cache");
          header('Content-Disposition: attachment; filename="export.csv"');
          $objWriter = new PHPExcel_Writer_CSV($objPHPExcel);
          $objWriter->setDelimiter(',');
          $objWriter->setEnclosure('"');
          $objWriter->setLineEnding("\r\n");
          //$objWriter->setUseBOM(true);
          $objWriter->setSheetIndex(0);
          $objWriter->save('php://output');
          break;
        case 'xls':
          // Redirect output to a client’s web browser (Excel5)
          header('Content-Type: application/vnd.ms-excel');
          header('Content-Disposition: attachment;filename="export.xls"');
          header('Cache-Control: max-age=0');
          $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
          $objWriter->save('php://output');
          break;
        case 'xlsx':
          // Redirect output to a client’s web browser (Excel2007)
          header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
          header('Content-Disposition: attachment;filename="export.xlsx"');
          header('Cache-Control: max-age=0');
          $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
          $objWriter->save('php://output');
          break;
      }
      exit;
    }
  } 