<?php
/**
* The template for displaying Comments.
*
*/
?>
	<?php if ( post_password_required() ) : ?>
<div id="comments">
	<p class="nopassword"><?php _e( 'Dieser Beitrag ist passwortgeschützt. Gib das Passwort ein, um Kommentare anzuzeigen.', THEME_TEXT_DOMAIN ); ?></p>
</div><!-- #comments -->
<?php
/* Stop the rest of comments.php from being processed,
* but don't kill the script entirely -- we still have
* to fully load the template.
*/
return;
endif; ?>

<?php // You can start editing here -- including this comment! ?>

<div id="comments">

<?php
if ( have_comments() ) {
	?>
	<h3 id="comments-title">
		<?php
	printf( _n( 'Eine Bewertung für %2$s', '%1$s Bewertungen für %2$s.', get_comments_number(), THEME_TEXT_DOMAIN ),
	number_format_i18n( get_comments_number() ), '<em>' . get_the_title() . '</em>' );
		?>
	</h3>

<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
<div class="navigation">
	<div class="nav-previous"><?php previous_comments_link( __( '<span class="meta-nav">&larr;</span> Ältere Rezensionen', THEME_TEXT_DOMAIN ) ); ?></div>
	<div class="nav-next"><?php next_comments_link( __( 'Neuere Rezensionen <span class="meta-nav">&rarr;</span>', THEME_TEXT_DOMAIN ) ); ?></div>
</div> <!-- .navigation -->
<?php endif; // check for comment navigation ?>

<ol class="commentlist">
	<?php
	/* Loop through and list the comments. Tell wp_list_comments()
	* to use twentyten_comment() to format the comments.
	* If you want to overload this in a child theme then you can
	* define twentyten_comment() and that will be used instead.
	* See twentyten_comment() in twentyten/functions.php for more.
	*/
	wp_list_comments( array( 'callback' => 'the_dr_comment' ) );
	?>
</ol>

<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>

<div class="navigation">
	<div class="nav-previous"><?php previous_comments_link( __( '<span class="meta-nav">&larr;</span> Ältere Rezension', THEME_TEXT_DOMAIN ) ); ?></div>
	<div class="nav-next"><?php next_comments_link( __( 'Neuere Rezension <span class="meta-nav">&rarr;</span>', THEME_TEXT_DOMAIN ) ); ?></div>
</div><!-- .navigation -->

<?php endif; // check for comment navigation ?>

	<?php
} else { // or, if we don't have comments:

/* If there are no comments and comments are closed,
* let's leave a little note, shall we?
*/
if ( ! comments_open() ) :
?>
<p class="nocomments"><?php echo apply_filters( 'comments_close_text', __( 'Rezensionen sind geschlossen.', THEME_TEXT_DOMAIN ) ); ?></p>
	<?php
	endif; // end ! comments_open()

} // end have_comments()
?>

<?php comment_form( array(
//'comment_field'        => '<p class="comment-form-comment"><label for="comment">' . _x( 'Review', 'noun' ) . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>',
'must_log_in'          => '<p class="must-log-in">' .  sprintf( __( 'Du musst <a href="%s">angemeldet</a> sein, um eine Bewertung abzugeben.', THEME_TEXT_DOMAIN ), wp_login_url( apply_filters( 'the_permalink', get_permalink( $post->ID ) ) ) ) . '</p>',
'id_form'              => 'commentform',
'id_submit'            => 'submit',
'title_reply'          => __( 'Eine Rezension schreiben',THEME_TEXT_DOMAIN ),
'title_reply_to'       => __( 'Schreibe eine Bewertung für %s',THEME_TEXT_DOMAIN),
'cancel_reply_link'    => __( 'Bewertung abbrechen',THEME_TEXT_DOMAIN ),
'label_submit'         => __( 'PRezension abgeben',THEME_TEXT_DOMAIN ),
'comment_field'        => '<p class="comment-form-comment"><label for="comment">' . __( 'Rezension', THEME_TEXT_DOMAIN ) . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>',
'logged_in_as'         =>	'<p class="logged-in-as">' . sprintf( __( 'Angemeldet als <a href="%1$s">%2$s</a>. <a href="%3$s" title="Von diesem Konto abmelden">Abmelden?</a>',THEME_TEXT_DOMAIN ), admin_url( 'profile.php' ), $user_identity, wp_logout_url( apply_filters( 'the_permalink', get_permalink( ) ) ) ) . '</p>',
'comment_notes_after'  => '<p class="form-allowed-tags">' . sprintf( __( 'Du kannst diese <abbr title="HyperText Markup Language">HTML</abbr>-Tags und -Attribute verwenden: %s', THEME_TEXT_DOMAIN ), ' <code>' . allowed_tags() . '</code>' ) . '</p>',
) ); ?>

</div><!-- #comments -->
