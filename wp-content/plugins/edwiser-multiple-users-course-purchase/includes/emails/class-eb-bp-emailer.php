<?php

namespace app\wisdmlabs\edwiserBridge\BulkPurchase;

use app\wisdmlabs\edwiserBridge as edwiserBridge;

if (!class_exists("EbBpSendEmailer")) {

    class EbBpSendEmailer
    {

        public function sendBulkPurchaseEmail($args)
        {
            $emailTmplData = edwiserBridge\EBAdminEmailTemplate::getEmailTmplContent("eb_emailtmpl_bulk_prod_purchase_notifn");
            $allowNotify = get_option("eb_emailtmpl_bulk_prod_purchase_notifn_notify_allow");
            if ($emailTmplData && $allowNotify == "ON") {
                $emailTmplObj = new edwiserBridge\EBAdminEmailTemplate();
                return $emailTmplObj->sendEmail($args['user_email'], $args, $emailTmplData);
            }
        }
        public function sendCohortEnrollmentEmail($args)
        {
            $emailTmplData = edwiserBridge\EBAdminEmailTemplate::getEmailTmplContent("eb_emailtmpl_student_enroll_in_cohort_notifn");
            $allowNotify = get_option("eb_emailtmpl_student_enroll_in_cohort_notifn_notify_allow");
            if ($emailTmplData && $allowNotify == "ON") {
                $emailTmplObj = new edwiserBridge\EBAdminEmailTemplate();
                return $emailTmplObj->sendEmail($args['user_email'], $args, $emailTmplData);
            }
        }
        public function sendCohortUnEnrollmentEmail($args)
        {
            $emailTmplData = edwiserBridge\EBAdminEmailTemplate::getEmailTmplContent("eb_emailtmpl_student_unenroll_in_cohort_notifn");
            $allowNotify = get_option("eb_emailtmpl_student_unenroll_in_cohort_notifn_notify_allow");
            if ($emailTmplData && $allowNotify == "ON") {
                $emailTmplObj = new edwiserBridge\EBAdminEmailTemplate();
                return $emailTmplObj->sendEmail($args['user_email'], $args, $emailTmplData);
            }
        }
    }
}
