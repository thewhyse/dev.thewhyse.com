<?php
// namespace BetterDocs\BetterDocs_Email_Template;
trait BetterDocs_Email_Template {

    /**
     * Set Email Subject
     * By Default, subject will be "Weekly Reporting for NotificationX"
     * Admin can set Custom Subject from NotificationX Advanced Settings Panel
     * @return subject||String
     */
    public function email_subject() {
        $subject = wp_sprintf( '%s %s %s', __( 'Your Documentation Performance of', 'betterdocs' ),  get_bloginfo( 'name' ), __( 'Website', 'betterdocs' ) );
        return apply_filters( 'betterdocs_reporting_email_subject' , $subject);
    }

    public function header(){
        $output = <<<BDTEMHEADER
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>BetterDocs Email Template</title>
        <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
        <style type="text/css">
            .betterdocs-email-body, .betterdocs-wrapper-body {
                font-size: 14px;
                font-family: 'Roboto', sans-serif;
            }
            .betterdocs-box-analytics-parent {
                padding: 0px 25px;
            }
            .betterdocs-table-heading {
                background-color: rgb(229, 236, 242);
            }
            .betterdocs-table-heading th:first-child {
                width: 60%;
            }
        </style>
    </head>
    <body class="betterdocs-wrapper-body" style="background-color: #fff; margin: 0; padding: 0; font-family: 'Roboto';">
        <table class="betterdocs-email-wrapper" cellpadding="50" cellspacing="0" border="0" width="100%" align="center" bgcolor="#fff">
            <tbody>
                <tr>
                    <td align="center" style="padding-top:0px;padding-bottom:20px;">
                        <table class="betterdocs-email-body" cellpadding="35" cellspacing="0" border="0" width="700" align="center" bgcolor="#FFF">
                            <tbody>
BDTEMHEADER;
        return $output;
    }

    public function footer() {
        $output = <<<BDTEMFOOTER
        </tbody>
        </table> <!-- /.betterdocs-email-body -->
    </td>
</tr>
</td> <!-- td table body wrapper end -->
</tr> <!-- tr table body wrapper end -->
</tbody> <!-- tbody table body wrapper end -->
</table> <!-- table table body wrapper end -->
</tbody>
</table>
</body>
</html>
BDTEMFOOTER;
        return $output;
    }

    public function body_header( $args = array(), $frequency = 'betterdocs_weekly' ){
        $args = current( $args );
        $from_date = isset( $args['from_date'] ) ? date( 'M j, Y', strtotime( $args['from_date'] ) ) : '';
        $to_date = isset( $args['to_date'] ) ? date( 'M j, Y', strtotime( $args['to_date'] ) ) : '';

        if ( empty( $from_date ) || empty( $to_date ) ) {
            return '';
        }

        $days = $this->frequency($frequency);

        if( $frequency !== 'betterdocs_daily' ) {
            $to_date = "- " . $to_date;
        } else {
            $to_date = '';
        }
        $bloginfo = get_bloginfo();
        $subject = $this->email_subject();
        
        $output = <<<BDBODYHEADER
<tr style="background: #26d67d">
    <td style="padding: 20px 40px">
        <table style="width: 100%; border-spacing: 0;">
            <tr>
                <td>
                    <img src="https://betterdocs.co/wp-content/uploads/2022/12/BetterDocs-logo-white.png" style="max-width: 100%; height: 35px" alt="logo" />
                </td>
                <td align="right">
                    <span style="font-size: 12px; line-height: 1em; text-align: right; color: #fff; flex-direction: column; column-gap: 5px;">
                        <span style="display:block; margin-bottom: 5px; font-weight: 600; font-size: 18px;">Last $days</span>
                        <span>$from_date $to_date</span>
                    </span>
                </td>
            </tr>
        </table>
    </td>
</tr>
<tr style="background: #f7f9fb"> <!-- tr table body wrapper start -->
<td style="padding: 50px">
<table cellpadding="0" cellspacing="0" border="0" align="center">
<tbody>
<tr>
    <td style="text-align: center; font-size: 20px; font-weight: bold; padding-bottom: 40px;">
        $subject
    </td>
</tr>
BDBODYHEADER;

        return $output;
    }

    public function body( $args = array(), $frequency = 'betterdocs_weekly' ){
        if( empty( $args ) ) {
            return '';
        }

        $body_header = $this->body_header( $args, $frequency );

        $analytics = $this->analytics_overview( $args, $frequency );
        $analytics .= $this->leading_docs( $args['docs']['current_data'], $args['docs']['total_current_reactions'], $frequency );
        $analytics .= $this->search_keywords( $args['search']['keywords'], $frequency );

        $analytics_tables = apply_filters('betterdocs_analytics_reporting_tables', $analytics, $args, $frequency);

        $pro_msg = $this->pro_message();

        $output = <<<BDTEMBODY
$body_header
$analytics_tables
$pro_msg
BDTEMBODY;
        return $output;
    }

    public function template_body( $args, $frequency ){
        $output = $this->header() . $this->body( $args, $frequency ) . $this->footer();
        return $output;
    }

    private function percentage ($previous, $current) {
        if ( $previous == 0 ) {
          $factor = $current * 100;
        } else if ( $current == 0 ) {
          $factor = $previous * 100;
        } else {
          $factor = (( $current - $previous ) / $previous) * 100;
        }
        return number_format($factor, 2, '.', '');
    }

    public function leading_docs( $args = array(), $total_reactions = '', $frequency = 'betterdocs_weekly' ) {
        $output = '';
        if ( $args ) {
            $output .= '<tr><td style="text-align: left; font-size: 20px; padding-top: 40px;">Top Performing Docs</td></tr>
            <tr><td style="padding:0">
                <table style="width: 100%; border-collapse: separate; border-spacing: 2px; padding-top: 20px;">
                <tr style="background: #fff;">
                    <th style="text-align: left; font-size: 14px; font-weight: 600; color:#6e6e73; text-transform: capitalize; padding: 8px 15px; width: 48%;">Doc Title</th>
                    <th style="text-align: center; font-size: 14px; font-weight: 600; color:#6e6e73; text-transform: capitalize; padding: 8px 8px;">Total Views</th>
                    <th style="text-align: center; font-size: 14px; font-weight: 600; color:#6e6e73; text-transform: capitalize; padding: 8px 8px;">Unique Views</th>';
                    if ( $total_reactions > 0 ) {
                        $output .= '<th style="text-align: center; font-size: 14px; font-weight: 600; color:#6e6e73; text-transform: capitalize; padding: 8px 15px;">Reactions</th>';
                    }
                $output .= '</tr>';
                foreach ($args as $docs) {
                    $output .= '<tr style="background: #fff;">
                        <td style="text-align: left; font-size: 14px; color:#1d1d1f; border-width: 1px; border-style: solid; border: none; padding: 8px 15px;">
                            <a style="text-decoration:none;color:#222;" href="'.get_permalink($docs->ID).'">'. $docs->title .'</a>
                        </td>
                        <td style="text-align: center; font-size: 14px; color:#1d1d1f; border-width: 1px; border-style: solid; border: none; padding: 8px 15px;">
                            '. $docs->total_views .'
                        </td>
                        <td style="text-align: center; font-size: 14px; color:#1d1d1f; border-width: 1px; border-style: solid; border: none; padding: 8px 15px;">
                            '. $docs->total_unique_visit .'
                        </td>';
                        if ( $total_reactions > 0 ) {
                            $output .= '<td style="text-align: center; font-size: 14px; color:#1d1d1f; border-width: 1px; border-style: solid; border: none; padding: 8px 15px;">
                                '. $docs->total_reactions .'
                            </td>';
                        }
                        $output .= '</tr>';
                }
            $output .= '</table></td></tr>';
        }
        return $output;
    }

    public function search_keywords( $args = array(), $frequency = 'betterdocs_weekly' ) { 
        $output = '';
        if ( $args ) {       
            $output .= '<tr><td style="text-align: left; font-size: 20px; padding-top: 40px;">Most Searched Keywords</td></tr>
            <tr><td><table style="width: 100%; border-collapse: separate; border-spacing: 2px; padding-top: 20px;">   
                <tr style="background: #fff;">
                    <th style="text-align: left; font-size: 14px; font-weight: 600; color:#6e6e73; text-transform: capitalize; padding: 8px 15px; width: 50%;">Search Keyword</th>
                    <th style="text-align: center; font-size: 14px; font-weight: 600; color:#6e6e73; text-transform: capitalize; padding: 8px 15px">Total Search</th>
                    <th style="text-align: center; font-size: 14px; font-weight: 600; color:#6e6e73; text-transform: capitalize; padding: 8px 15px">Result Found</th>
                </tr>';

                foreach ( $args as $keyword ) {
                    if ( $keyword->count > 0 ) {
                        $found_icon = '<img style="width: 14px;" src="https://betterdocs.co/wp-content/uploads/2022/12/tik.png" alt="" />';
                    } else {
                        $found_icon = '<img style="width: 14px;" src="https://betterdocs.co/wp-content/uploads/2022/12/nontik.png" alt="" />';
                    }

                    $output .= '<tr style="background: #fff;">
                        <td style="text-align: left; font-size: 14px; color:#1d1d1f; border-width: 1px; border-style: solid; border: none; padding: 8px 15px;">'. $keyword->keyword .'</td>
                        <td style="text-align: center; font-size: 14px; color:#1d1d1f; border-width: 1px; border-style: solid; border: none; padding: 8px 15px;">'. $keyword->total_search .'</td>
                        <td style="text-align: center; font-size: 14px; color:#1d1d1f; border-width: 1px; border-style: solid; border: none; padding: 8px 15px;">'. $found_icon .'</td>
                    </tr>';
                }
            $output .= '</table></td></tr>';
        }
        return $output;
    }

    public function frequency( $frequency = 'betterdocs_weekly' ) {
        
        switch( $frequency ) {
            case 'betterdocs_weekly' :
                $days_ago = '7 days';
                break;
            case 'betterdocs_daily' :
                $days_ago = '1 day';
                break;
            case 'betterdocs_monthly' :
                $initial_timestamp = strtotime('first day of last month', current_time('timestamp'));
                $days_in_last_month = cal_days_in_month(CAL_GREGORIAN, date( 'm', $initial_timestamp ), date( 'Y', $initial_timestamp ));
                $days_ago = $days_in_last_month . ' days';
                break;
        }

        return $days_ago;
    }

    protected function analytics_overview( $args = array(), $frequency = 'betterdocs_weekly' ){
        if( empty( $args ) ) {
            return false;
        }

        $up_arrow = $view_arrow = $search_arrow = $reactions_arrow = $docs_arrow = '<img src="https://betterdocs.co/wp-content/uploads/2022/12/polygon-up.png" alt="" />';
        $down_arrow = '<img src="https://betterdocs.co/wp-content/uploads/2022/12/polygon-down.png" alt="" />';
        $neutral_arrow = '';

        $views = (isset($args['views']['current_data'][0]->views)) ? number_format( $args['views']['current_data'][0]->views ) : 0;
        $prev_views = (isset($args['views']['previous_data'][0]->views)) ? number_format( $args['views']['previous_data'][0]->views ) : 0;
        $percentage_views  = $this->percentage( $prev_views, $views );

        $view_color = $search_color = $reactions_color = $docs_color = '#34cf8a';
        if ( $percentage_views == '0.00' ) {
            $view_color = '#f7d070';
            $view_arrow = $neutral_arrow;
        } else if ( $views < $prev_views ) {
            $view_color = '#ff616c';
            $view_arrow = $down_arrow;
        }

        $reactions = (isset($args['views']['current_data'][0]->reactions)) ? number_format( $args['views']['current_data'][0]->reactions ) : 0;
        $prev_reactions = (isset($args['views']['previous_data'][0]->reactions)) ? number_format( $args['views']['previous_data'][0]->reactions ) : 0;
        $percentage_reactions    = $this->percentage( $prev_reactions, $reactions );

        if ( $percentage_reactions == '0.00' ) {
            $reactions_color = '#f7d070';
            $reactions_arrow = $neutral_arrow;
        } else if ( $reactions < $prev_reactions ) {
            $reactions_color = '#ff616c';
            $reactions_arrow = $down_arrow;
        }

        $total_search = (isset($args['search']['current_data'][0]->search_count)) ? number_format( $args['search']['current_data'][0]->search_count ) : 0;
        $prev_total_search = (isset($args['search']['previous_data'][0]->search_count)) ? number_format( $args['search']['previous_data'][0]->search_count ) : 0;
        $percentage_total_search = $this->percentage( $prev_total_search, $total_search );

        if ( $percentage_total_search == '0.00' ) {
            $search_color = '#f7d070';
            $search_arrow = $neutral_arrow;
        } else if ( $total_search < $prev_total_search ) {
            $search_color = '#ff616c';
            $search_arrow = $down_arrow;
        }

        $total_docs = (isset($args['new_docs']['current_data'])) ? number_format( $args['new_docs']['current_data'] ) : 0;
        $prev_total_docs = (isset($args['new_docs']['previous_data'])) ? number_format( $args['new_docs']['previous_data'] ) : 0;
        $percentage_total_docs = $this->percentage( $prev_total_docs, $total_docs );

        if ( $percentage_total_docs == '0.00' ) {
            $docs_color = '#f7d070';
            $docs_arrow = $neutral_arrow;
        } else if ( $total_docs < $prev_total_docs ) {
            $docs_color = '#ff616c';
            $docs_arrow = $down_arrow;
        }

        $days_ago = esc_html( $this->frequency( $frequency ) );
        $view_color   = esc_attr( $view_color );
        $reactions_color   = esc_attr( $reactions_color );
        $search_color = esc_attr( $search_color );

        $output = <<<BDBOXTEM
<tr>
    <td>
        <table style="width: 100%; border-spacing: 0;">
            <tr>
                <td>
                    <span style="display: flex; align-items: center; background: #fff; margin-right: 7px; height: 100%; padding: 20px;">
                        <span>
                            <img src="https://betterdocs.co/wp-content/uploads/2022/12/eye-solid.png" style="max-width: 100%; width: 45px; height: auto; margin-right: 15px;" alt="eye_logo">
                        </span>
                        <span style="margin-top: 5px;">
                            <h3 style="font-size: 12px; line-height: 1em; color: #6e6e73; font-weight: 600; text-transform: uppercase; padding-bottom: 5px; margin: 0;">Total Views</h3>
                            <h2 
                                style="font-size: 22px; line-height: 1em; font-weight: 700; color: #1d1d1f; margin: 0;">$views
                                $view_arrow<span style="font-size: 14px; line-height: 1.1em; font-weight: 600; color: $view_color; margin-left: 5px;">$percentage_views%</span>
                            </h2>
                        </span>
                    </span>
                </td>
                <td>
                    <span style="display: flex; align-items: center; background: #fff; margin-left: 7px; height: 100%; padding: 20px;">
                        <span>
                            <img src="https://betterdocs.co/wp-content/uploads/2022/12/search.png" style="max-width: 100%; width: 45px; height: auto; margin-right: 15px;" alt="eye_logo">
                        </span>
                        <span style="margin-top: 5px;">
                            <h3 style="font-size: 12px; line-height: 1em; color: #6e6e73; font-weight: 600; text-transform: uppercase; padding-bottom: 5px; margin: 0;">Total Searches</h3>
                            <h2 
                                style="font-size: 22px; line-height: 1em; font-weight: 700; color: #1d1d1f; margin: 0;">$total_search
                                $search_arrow<span style="font-size: 14px; line-height: 1.1em; font-weight: 600; color: $search_color; margin-left: 5px;">$percentage_total_search%</span>
                            </h2>
                        </span>

                    </span>
                </td>
            </tr>
            <tr>
                <td style="padding-top: 15px;">
                    <span style="display: flex; align-items: center; background: #fff; margin-right: 7px; height: 100%; padding: 20px;">
                        <span>
                            <img src="https://betterdocs.co/wp-content/uploads/2022/12/search.png" style="max-width: 100%; width: 45px; height: auto; margin-right: 15px;" alt="eye_logo">
                        </span>
                        <span style="margin-top: 5px;">
                            <h3 style="font-size: 12px; line-height: 1em; color: #6e6e73; font-weight: 600; text-transform: uppercase; padding-bottom: 5px; margin: 0;">Total Reactions</h3>
                            <h2 
                                style="font-size: 22px; line-height: 1em; font-weight: 700; color: #1d1d1f; margin: 0;">$reactions
                                $reactions_arrow<span style="font-size: 14px; line-height: 1.1em; font-weight: 600; color: $reactions_color; margin-left: 5px;">$percentage_reactions%</span>
                            </h2>
                        </span>

                    </span>
                </td>
                <td style="padding-top: 15px;">
                    <span style="display: flex; align-items: center; background: #fff; margin-left: 7px; height: 100%; padding: 20px;">
                        <span>
                            <img src="https://betterdocs.co/wp-content/uploads/2022/12/file-.png" style="max-width: 100%; width: 45px; height: auto; margin-right: 15px;" alt="eye_logo">
                        </span>
                        <span style="margin-top: 5px;">
                            <h3 style="font-size: 12px; line-height: 1em; color: #6e6e73; font-weight: 600; text-transform: uppercase; padding-bottom: 5px; margin: 0;">Newly Created Docs</h3>
                            <h2 
                                style="font-size: 22px; line-height: 1em; font-weight: 700; color: #1d1d1f; margin: 0;">$total_docs
                                $docs_arrow<span style="font-size: 14px; line-height: 1.1em; font-weight: 600; color: $docs_color; margin-left: 5px;">$percentage_total_docs%</span>
                            </h2>
                        </span>
                    </span>
                </td>
            </tr>
        </table>
    </td>
</tr>
BDBOXTEM;
        return $output;
    }
    
    public static function pro_message() {
        $is_pro              = defined( 'BETTERDOCS_PRO_VERSION' );
        $graph               = 'https://betterdocs.co/wp-content/uploads/2022/11/Analytics.gif';
        $admin_analytics_url = admin_url( 'admin.php?page=betterdocs-analytics' );
        if( $is_pro ) {
            $output = <<<BDPROMSG
<tr>
    <td style="padding-top: 40px;" align="center">
        <a style="display:inline-block; text-decoration: none; font-size: 12px; font-weight: 600; color: #fff; padding: 16px 22px; border: none; background: #26d67d; border-radius: 10px; cursor: pointer; display: inline-block; letter-spacing: 1px;" href="$admin_analytics_url" target="_blank">View Analytics</a>
    </td>
</tr>
<tr>
    <td style="padding-top: 20px;">
        <table style="border-spacing: 0;">
            <tr>
                <td style="display: flex;">
                    <span style="margin-right: 10px; margin-top: 5px;">📚</span>
                    <p style="text-align: justify; font-size: 14px; line-height: 1.7em; font-weight: 400; margin: 0;"> Want to know what more you can do with BetterDocs? Check out these tutorials: 
                        <a href="https://betterdocs.co/blog/improved-betterdocs-analytics/" style="font-weight: 500; color: #1d1d1f; font-style: italic;">Complete Guide To BetterDocs Analytics,</a> 
                        <a href="https://betterdocs.co/blog/key-customer-service-metrics/" style="font-weight: 500; color: #1d1d1f; font-style: italic;">Key Customer Service Metrics To Measure Performance</a> & 
                        <a href="https://betterdocs.co/blog/internal-documentation-wordpress/" style="font-weight: 500; color: #1d1d1f; font-style: italic;">How To Use BetterDocs Internal Knowledge Base.</a>
                    </p>
                </td>
            </tr>
        </table>
    </td>
</tr>
BDPROMSG;
        } else {
            $output = <<<BDPROMSG
<tr>
    <td style="padding-top: 40px;">
        <table style="width: 100%; background: rgba(38, 214, 125, 0.1); padding: 20px 20px 20px 20px; border-radius: 5px; border-spacing: 0;">
            <tr>
                <td style="display: flex; gap: 10px;">
                    <span style="margin-top: 4px; margin-right: 3px;">💡</span>
                    <p style="float: right; font-size: 14px; line-height: 1.7em; font-weight: 400; text-align: justify; margin: 0;">
                        This advanced <span style="font-weight: 700; color: #1d1d1f; text-decoration: none;">Email Reporting</span> feature is part of our premium built in Analytics tool available only with BetterDocs PRO, but you
                        are getting it for <span style="font-weight: 700; color: #1d1d1f; text-decoration: none;">FREE!</span>
                    </p>
                </td>
            </tr>
        </table>
    </td>
</tr>
<tr>
    <td style="padding-top: 40px;">
        <table style="width: 100%; background: #fff; padding: 10px; border-spacing: 0;">
            <tr>
                <td>
                    <img style="display: block; max-width: 100%;" src="$graph" alt="View Analytics">
                </td>
            </tr>
        </table>
    </td>
</tr>
<tr>
    <td align="center" style="padding-top: 30px;">
        <a href="https://betterdocs.co/upgrade" style="text-decoration: none; font-size: 12px; font-weight: 600; color: #fff; padding: 16px 22px; border: none; background: #26d67d; border-radius: 10px; cursor: pointer; display: inline-block; letter-spacing: 1px;">
            Unlock BetterDocs Analytics <span style="padding-left: 5px;">🔐</span>
        </a>
    </td>
</tr>
<tr>
    <td style="padding-top: 20px;">
        <table style="border-spacing: 0;">
            <tr>
                <td style="display: flex;">
                    <span style="margin-right: 10px; margin-top: 5px;">📚</span>
                    <p style="text-align: justify; font-size: 14px; line-height: 1.7em; font-weight: 400; margin: 0;"> Want to know what more you can do with BetterDocs? Check out these tutorials: 
                        <a href="https://betterdocs.co/blog/improved-betterdocs-analytics/" style="font-weight: 500; color: #1d1d1f; font-style: italic;">Complete Guide To BetterDocs Analytics,</a> 
                        <a href="https://betterdocs.co/blog/key-customer-service-metrics/" style="font-weight: 500; color: #1d1d1f; font-style: italic;">Key Customer Service Metrics To Measure Performance</a> & 
                        <a href="https://betterdocs.co/blog/internal-documentation-wordpress/" style="font-weight: 500; color: #1d1d1f; font-style: italic;">How To Use BetterDocs Internal Knowledge Base.</a>
                    </p>
                </td>
            </tr>
        </table>
    </td>
</tr>
BDPROMSG;
        }       
        return $output;
    }
}