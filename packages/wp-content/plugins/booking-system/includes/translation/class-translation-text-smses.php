<?php

/*
* Title                   : Pinpoint Booking System WordPress Plugin
* File                    : includes/translation/class-translation-text-smses.php
* Author                  : PINPOINT.WORLD
* Copyright               : © 2018 PINPOINT.WORLD
* Website                 : http://www.pinpoint.world
* Description             : SMSes translation text PHP class.
*/

    if (!class_exists('DOPBSPTranslationTextSmses')){
        class DOPBSPTranslationTextSmses{
            /*
             * Constructor
             */
            function __construct(){
                /*
                 * Initialize SMSes text.
                 */
                add_filter('dopbsp_filter_translation_text', array(&$this, 'smses'));
                add_filter('dopbsp_filter_translation_text', array(&$this, 'smsesDefault'));
                
                add_filter('dopbsp_filter_translation_text', array(&$this, 'smsesSms'));
                add_filter('dopbsp_filter_translation_text', array(&$this, 'smsesAddSms'));
                add_filter('dopbsp_filter_translation_text', array(&$this, 'smsesDeleteSms'));
                
                add_filter('dopbsp_filter_translation_text', array(&$this, 'smsesHelp'));
            }
            
            /*
             * SMSes text.
             * 
             * @param lang (array): current translation
             * 
             * @return array with updated translation
             */
            function smses($text){
                array_push($text, array('key' => 'PARENT_SMSES',
                                        'parent' => '',
                                        'text' => 'SMS templates'));
                
                array_push($text, array('key' => 'SMSES_TITLE',
                                        'parent' => 'PARENT_SMSES',
                                        'text' => 'SMS templates'));
                array_push($text, array('key' => 'SMSES_CREATED_BY',
                                        'parent' => 'PARENT_SMSES',
                                        'text' => 'Created by'));
                array_push($text, array('key' => 'SMSES_LOAD_SUCCESS',
                                        'parent' => 'PARENT_SMSES',
                                        'text' => 'SMS templates  list loaded.'));
                array_push($text, array('key' => 'SMSES_NO_SMSES',
                                        'parent' => 'PARENT_SMSES',
                                        'text' => 'No SMS templates. Click the above "plus" icon to add new ones.'));
                
                return $text;
            }
            
            /*
             * SMSes default text.
             * 
             * @param lang (array): current translation
             * 
             * @return array with updated translation
             */
            function smsesDefault($text){
                array_push($text, array('key' => 'PARENT_SMSES_DEFAULT',
                                        'parent' => '',
                                        'text' => 'SMS templates - Default messages'));
                
                array_push($text, array('key' => 'SMSES_DEFAULT_NAME',
                                        'parent' => 'PARENT_SMSES_DEFAULT',
                                        'text' => 'Default SMS templates'));
                
                /*
                 * Default booking, with payment on arrival.
                 */
                array_push($text, array('key' => 'SMSES_DEFAULT_BOOK_ADMIN',
                                        'parent' => 'PARENT_SMSES_DEFAULT',
                                        'text' => 'You received a booking request.'));
                array_push($text, array('key' => 'SMSES_DEFAULT_BOOK_USER',
                                        'parent' => 'PARENT_SMSES_DEFAULT',
                                        'text' => 'Your booking request has been sent.'));
                /*
                 * Booking with approval.
                 */
                array_push($text, array('key' => 'SMSES_DEFAULT_BOOK_WITH_APPROVAL_ADMIN',
                                        'parent' => 'PARENT_SMSES_DEFAULT',
                                        'text' => 'You received a booking request.'));
                array_push($text, array('key' => 'SMSES_DEFAULT_BOOK_WITH_APPROVAL_USER',
                                        'parent' => 'PARENT_SMSES_DEFAULT',
                                        'text' => 'Your booking request has been sent.Please wait for approval.'));
                /*
                 * Approved reservation.
                 */
                array_push($text, array('key' => 'SMSES_DEFAULT_APPROVED',
                                        'parent' => 'PARENT_SMSES_DEFAULT',
                                        'text' => 'Your booking request has been approved.'));
                /*
                 * Canceled reservation.
                 */
                array_push($text, array('key' => 'SMSES_DEFAULT_CANCELED',
                                        'parent' => 'PARENT_SMSES_DEFAULT',
                                        'text' => 'Your booking request has been canceled.'));
                /*
                 * Rejected reservation.
                 */
                array_push($text, array('key' => 'SMSES_DEFAULT_REJECTED',
                                        'parent' => 'PARENT_SMSES_DEFAULT',
                                        'text' => 'Your booking request has been rejected.'));
                
                return $text;
            }
            
            /*
             * SMSes - Sms text.
             * 
             * @param lang (array): current translation
             * 
             * @return array with updated translation
             */
            function smsesSms($text){
                array_push($text, array('key' => 'PARENT_SMSES_SMS',
                                        'parent' => '',
                                        'text' => 'SMS templates - Templates'));
                
                array_push($text, array('key' => 'SMSES_SMS_NAME',
                                        'parent' => 'PARENT_SMSES_SMS',
                                        'text' => 'Name'));
                array_push($text, array('key' => 'SMSES_SMS_LANGUAGE',
                                        'parent' => 'PARENT_SMSES_SMS',
                                        'text' => 'Language'));
                
                array_push($text, array('key' => 'SMSES_SMS_TEMPLATE_SELECT',
                                        'parent' => 'PARENT_SMSES_SMS',
                                        'text' => 'Select template'));
                array_push($text, array('key' => 'SMSES_SMS_TEMPLATE_SELECT_BOOK_ADMIN',
                                        'parent' => 'PARENT_SMSES_SMS',
                                        'text' => 'Admin notification'));
                array_push($text, array('key' => 'SMSES_SMS_TEMPLATE_SELECT_BOOK_USER',
                                        'parent' => 'PARENT_SMSES_SMS',
                                        'text' => 'User notification'));
                array_push($text, array('key' => 'SMSES_SMS_TEMPLATE_SELECT_BOOK_WITH_APPROVAL_ADMIN',
                                        'parent' => 'PARENT_SMSES_SMS',
                                        'text' => 'Instant approval admin notification'));
                array_push($text, array('key' => 'SMSES_SMS_TEMPLATE_SELECT_BOOK_WITH_APPROVAL_USER',
                                        'parent' => 'PARENT_SMSES_SMS',
                                        'text' => 'Instant approval user notification'));
                array_push($text, array('key' => 'SMSES_SMS_TEMPLATE_SELECT_APPROVED',
                                        'parent' => 'PARENT_SMSES_SMS',
                                        'text' => 'Approve resevation'));
                array_push($text, array('key' => 'SMSES_SMS_TEMPLATE_SELECT_CANCELED',
                                        'parent' => 'PARENT_SMSES_SMS',
                                        'text' => 'Cancel resevation'));
                array_push($text, array('key' => 'SMSES_SMS_TEMPLATE_SELECT_REJECTED',
                                        'parent' => 'PARENT_SMSES_SMS',
                                        'text' => 'Reject resevation'));
                array_push($text, array('key' => 'SMSES_SMS_MESSAGE',
                                        'parent' => 'PARENT_SMSES_SMS',
                                        'text' => 'Message'));
                
                array_push($text, array('key' => 'SMSES_SMS_LOADED',
                                        'parent' => 'PARENT_SMSES_SMS',
                                        'text' => 'SMS templates loaded.'));
                
                return $text;
            }
            
            /*
             * SMS templates - Add SMS text.
             * 
             * @param lang (array): current translation
             * 
             * @return array with updated translation
             */
            function smsesAddSms($text){
                array_push($text, array('key' => 'PARENT_SMSES_ADD_SMS',
                                        'parent' => '',
                                        'text' => 'SMS templates - Add templates'));
                
                array_push($text, array('key' => 'SMSES_ADD_SMS_NAME',
                                        'parent' => 'PARENT_SMSES_ADD_SMS',
                                        'text' => 'New SMS templates'));
                array_push($text, array('key' => 'SMSES_ADD_SMS_SUBMIT',
                                        'parent' => 'PARENT_SMSES_ADD_SMS',
                                        'text' => 'Add SMS templates'));
                array_push($text, array('key' => 'SMSES_ADD_SMS_ADDING',
                                        'parent' => 'PARENT_SMSES_ADD_SMS',
                                        'text' => 'Adding new SMS templates ...'));
                array_push($text, array('key' => 'SMSES_ADD_SMS_SUCCESS',
                                        'parent' => 'PARENT_SMSES_ADD_SMS',
                                        'text' => 'You have succesfully added new SMS templates.'));
                
                return $text;
            }
            
            /*
             * SMSes - Delete SMS text.
             * 
             * @param lang (array): current translation
             * 
             * @return array with updated translation
             */
            function smsesDeleteSms($text){
                array_push($text, array('key' => 'PARENT_SMSES_DELETE_SMS',
                                        'parent' => '',
                                        'text' => 'SMS templates - Delete templates'));
                
                array_push($text, array('key' => 'SMSES_DELETE_SMS_CONFIRMATION',
                                        'parent' => 'PARENT_SMSES_DELETE_SMS',
                                        'text' => 'Are you sure you want to delete the SMS templates?'));
                array_push($text, array('key' => 'SMSES_DELETE_SMS_SUBMIT',
                                        'parent' => 'PARENT_SMSES_DELETE_SMS',
                                        'text' => 'Delete SMS templates'));
                array_push($text, array('key' => 'SMSES_DELETE_SMS_DELETING',
                                        'parent' => 'PARENT_SMSES_DELETE_SMS',
                                        'text' => 'Deleting SMS templates ...'));
                array_push($text, array('key' => 'SMSES_DELETE_SMS_SUCCESS',
                                        'parent' => 'PARENT_SMSES_DELETE_SMS',
                                        'text' => 'You have succesfully deleted the SMS templates.'));
                
                return $text;
            }
            
            /*
             * SMSes - Help text.
             * 
             * @param lang (array): current translation
             * 
             * @return array with updated translation
             */
            function smsesHelp($text){
                array_push($text, array('key' => 'PARENT_SMSES_HELP',
                                        'parent' => '',
                                        'text' => 'SMS templates - Help'));
                
                array_push($text, array('key' => 'SMSES_HELP',
                                        'parent' => 'PARENT_SMSES_HELP',
                                        'text' => 'Click on a templates item to open the editing area.'));
                array_push($text, array('key' => 'SMSES_ADD_SMS_HELP',
                                        'parent' => 'PARENT_SMSES_HELP',
                                        'text' => 'Click on the "plus" icon to add SMS templates.'));
                
                /*
                 * Sms help.
                 */
                array_push($text, array('key' => 'SMSES_SMS_HELP',
                                        'parent' => 'PARENT_SMSES_HELP',
                                        'text' => 'Click the "trash" icon to delete the SMS.'));
                array_push($text, array('key' => 'SMSES_SMS_NAME_HELP',
                                        'parent' => 'PARENT_SMSES_HELP',
                                        'text' => 'Change SMS templates name.'));
                array_push($text, array('key' => 'SMSES_SMS_LANGUAGE_HELP',
                                        'parent' => 'PARENT_SMSES_HELP',
                                        'text' => 'Change to the language you want to edit the SMS templates.'));
                array_push($text, array('key' => 'SMSES_SMS_TEMPLATE_SELECT_HELP',
                                        'parent' => 'PARENT_SMSES_HELP',
                                        'text' => 'Select the template you want to edit and modify the message.'));
                
                return $text;
            }
        }
    }