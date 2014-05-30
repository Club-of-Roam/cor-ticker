<?php
/*
  Plugin Name: SMS Ticker
  Version: 1.0.1
  Author: MindoMobile
  Description: SMS plugin to support Symbian SMS gateway
  Plugin URI: http://www.mindomobile.com
*/

if ( ! class_exists('Mindo_SMS_Ticker') ):

    class Mindo_SMS_Ticker {

        function __construct() {
            add_action( 'admin_menu', array( $this, 'createMenu' ) );
        }

        public function createMenu() {
            add_options_page(
                'SMS Ticker',
                'SMS Ticker',
                'activate_plugins',
                'wpSmsTicker',
                array($this, 'optionsPage')
            );

            /* process actions */
            $this->processActions();
        }

        public function processActions() {
            global $wpdb;

            if (preg_match_all("/^([0-9]+)+$/sim", $_GET['id'], $m) && $_GET['action'] == "delete") {
                // removing item
                $wpdb->query("DELETE FROM ".$wpdb->prefix."sms_ticker WHERE id='".$_GET['id']."' LIMIT 1");
            }
        }

        public function optionsPage() {
            global $wpdb;

            echo '<div class="wrap">'.
                   '<div id="icon-options-general" class="icon32"><br></div>'.
                   '<h2>'.__("SMS Ticker", "sms_ticker").'</h2>';

            echo '<br/><table class="widefat">'.
                  '<thead>'.
                    '<tr>'.
                      '<th>'.__('From', 'sms_ticker').'</th>'.
                      '<th>'.__('Msg Type', 'sms_ticker').'</th>'.
                      '<th>'.__('Message', 'sms_ticker').'</th>'.
                      '<th>'.__('Date & Time', 'sms_ticker').'</th>'.
                      '<th>'.__('Actions', 'sms_ticker').'</th>'.
                    '</tr>'.
                  '</thead>'.
                  '<tfoot>'.
                    '<tr>'.
                      '<th>'.__('From', 'sms_ticker').'</th>'.
                      '<th>'.__('Msg Type', 'sms_ticker').'</th>'.
                      '<th>'.__('Message', 'sms_ticker').'</th>'.
                      '<th>'.__('Date & Time', 'sms_ticker').'</th>'.
                      '<th>'.__('Actions', 'sms_ticker').'</th>'.
                    '</tr>'.
                  '</tfoot>';
            echo '<tbody>';
            echo '</tbody>';

            $msgs = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."sms_ticker ".
               "ORDER BY timestamp DESC", ARRAY_A);

            $type_array = array('1' => __('SMS', 'sms_ticker'), '2' => __('MMS', 'sms_ticker'));

            if (count($msgs)) {
                foreach ($msgs as $msg) {

                    if ($msg['type'] == 1) {
                        $type_text = '<td>'.$type_array[$msg['type']].'</td>';
                    } else {
                        $type_text = '<td><a href="http://'.$_SERVER['SERVER_NAME'].'/wp-content/uploads/mms/'.$msg['img_url'].'" target="_new"/>'.$type_array[$msg['type']].'</a></td>';
                    }

                    echo '<tr>'.
                            '<td>'.$msg['from'].'</td>'.
                            $type_text.
                            '<td>'.$msg['msg'].'</td>'.
                            '<td>'.date('Y-m-d H:i:s', $msg['timestamp']).'</td>'.
                            '<td>'. '<a href="'.$_SERVER['PHP_SELF'].'?page='.$_GET['page'].'&amp;action=delete&amp;id='.$msg['id'].'">'.__('Delete', 'sms_ticker').'</a></td>'.
                          '</tr>';
                }
            } else {
                echo '<tr><td colspan="5">'.__('There are no messages yet!', 'sms_ticker').'</td></tr>';
            }

            echo '</table>';
            echo '</div>';
        }
    }

$mindo_sms_ticker = new Mindo_SMS_Ticker();
endif;

function mindo_sms_install() {
    global $wpdb;

    $wpdb->query(
        "CREATE TABLE `".$wpdb->prefix."sms_ticker` (".
            "`id` int(10) NOT NULL AUTO_INCREMENT,".
            "`from` varchar(30) COLLATE utf32_bin NOT NULL,".
            "`msg` text COLLATE utf32_bin NOT NULL,".
            "`type` int(1) NOT NULL,".
            "`img_url` text COLLATE utf32_bin NOT NULL,".
            "`timestamp` int(11) NOT NULL,".
            "PRIMARY KEY (`id`),".
            "UNIQUE KEY `id` (`id`)".
        ") ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_bin AUTO_INCREMENT=1 ;"
    );

    $wpdb->query(
        "CREATE TABLE `".$wpdb->prefix."sms_ticker_status` (".
            "`key` varchar(30) NOT NULL,".
            "`timestamp` int(10) NOT NULL,".
            "UNIQUE KEY `key` (`key`)".
        ") ENGINE=InnoDB DEFAULT CHARSET=latin1;"
    );
}
register_activation_hook( __FILE__, 'mindo_sms_install' );