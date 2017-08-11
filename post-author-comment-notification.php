<?php
/*
Plugin Name: Post Author Comment Notification
Plugin URI: http://wordpress.org/extend/plugins/post-author-comment-notification/
Description: Overwrides the wp_notify_moderator function and sends email notification to all blog admins and to all users who can moderate comments.
Author: Aaron Axelsen
Version: 1.0
Author URI: http://www.frozenpc.net
*/

/* wp_notify_moderator
   notifies the moderator of the blog (usually the admin)
   about a new comment that waits for approval
   always returns true
 */
function wp_notify_moderator($comment_id) {
        global $wpdb;

        if( get_option( "moderation_notify" ) == 0 )
                return true;

        $comment = $wpdb->get_row("SELECT * FROM $wpdb->comments WHERE comment_ID='$comment_id' LIMIT 1");
        $post = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE ID='$comment->comment_post_ID' LIMIT 1");

        $comment_author_domain = @gethostbyaddr($comment->comment_author_IP);
        $comments_waiting = $wpdb->get_var("SELECT count(comment_ID) FROM $wpdb->comments WHERE comment_approved = '0'");

        $notify_message  = sprintf( __('A new comment on the post #%1$s "%2$s" is waiting for your approval'), $post->ID, $post->post_title ) . "\r\n";
        $notify_message .= get_permalink($comment->comment_post_ID) . "\r\n\r\n";
        $notify_message .= sprintf( __('Author : %1$s (IP: %2$s , %3$s)'), $comment->comment_author, $comment->comment_author_IP, $comment_author_domain ) . "\r\n";
        $notify_message .= sprintf( __('E-mail : %s'), $comment->comment_author_email ) . "\r\n";
        $notify_message .= sprintf( __('URL    : %s'), $comment->comment_author_url ) . "\r\n";
        $notify_message .= sprintf( __('Whois  : http://ws.arin.net/cgi-bin/whois.pl?queryinput=%s'), $comment->comment_author_IP ) . "\r\n";
        $notify_message .= __('Comment: ') . "\r\n" . $comment->comment_content . "\r\n\r\n";
        $notify_message .= sprintf( __('Approve it: %s'),  get_option('siteurl')."/wp-admin/comment.php?action=mac&c=$comment_id" ) . "\r\n";
        $notify_message .= sprintf( __('Delete it: %s'), get_option('siteurl')."/wp-admin/comment.php?action=cdc&c=$comment_id" ) . "\r\n";
        $notify_message .= sprintf( __('Spam it: %s'), get_option('siteurl')."/wp-admin/comment.php?action=cdc&dt=spam&c=$comment_id" ) . "\r\n";
        $notify_message .= sprintf( __('Currently %s comments are waiting for approval. Please visit the moderation panel:'), $comments_waiting ) . "\r\n";
        $notify_message .= get_option('siteurl') . "/wp-admin/moderation.php\r\n";

        $subject = sprintf( __('[%1$s] Please moderate: "%2$s"'), get_option('blogname'), $post->post_title );
        $admin_email = get_option('admin_email');

        $notify_message = apply_filters('comment_moderation_text', $notify_message, $comment_id);
        $subject = apply_filters('comment_moderation_subject', $subject, $comment_id);

        @wp_mail($admin_email, $subject, $notify_message);

        global $current_user;
	$old_user = $current_user;
        $current_user = new WP_User($post->post_author);

        if ( current_user_can('moderate_comments') && $current_user->user_email != $admin_email) {
                @wp_mail($current_user->user_email, $subject, $notify_message);
        }
	$current_user = $old_user;
        return true;
}

?>
