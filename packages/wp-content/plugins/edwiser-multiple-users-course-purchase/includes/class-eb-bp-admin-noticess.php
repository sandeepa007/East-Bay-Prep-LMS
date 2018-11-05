<?php

namespace app\wisdmlabs\edwiserBridge\BulkPurchase;

if (!class_exists('\app\wisdmlabs\edwiserBridge\BulkPurchase\BPAdminNoticess')) {

    /**
     * Class provides the functionality to display admin notivcess
     */
    class BPAdminNoticess
    {

        /**
         * Message to display in admin notice.
         * @var String
         */
        private $message;
        /**
         * Css clsses in string format.
         * Use space for the class sepration
         * @var String
         */
        private $cssClass = "notice is-dismissible";
        /**
         * Message Type
         * Use success= 0, warning= 1, error= 2
         * @var Integer
         */
        private $type=4;

        /**
         * The only prameterised constructor to trigger the wp admin notice.
         * This will access two parmeters first one is message and second is messge type
         *
         * Message type are in integer format
         * 0= Success
         * 1= Warning
         * 2= Error
         *
         * @param String $message String formated message can cointain HTML
         * @param Integer $type type of the message Use success= 0, warning= 1, error= 2
         */
        public function __construct($message, $type)
        {
            $this->message = $message;
            $this->type=$type;
            $this->triggerMessage();
        }


        /**
         * Method Checkes the notice type to display and prepares the parameters
         */
        private function triggerMessage()
        {
            switch ($this->type) {
                case 0:
                    $this->cssClass.="notice-success";
                    break;
                case 1:
                    $this->cssClass.="notice-warning";
                    break;
                case 2:
                    $this->cssClass.="notice-error";
                    break;
                case 3:
                    $this->cssClass.="notice-info";
                    break;
                default:
                    break;
            }
            error_log($this->message);
            error_log($this->type);
            if ($this->type < 4) {
                add_action('admin_notices', array($this, "displayAdminNotice"));
            }
        }

        /**
         * Provides the functionality to trigger the message.
         */
        public function displayAdminNotice()
        {
            error_log($this->message);
            ?>
            <div class="<?php echo $this->cssClass; ?>">
                <p><?php _e($this->message, 'ebbp-textdomain'); ?></p>
            </div>
            <?php
        }
    }
}

