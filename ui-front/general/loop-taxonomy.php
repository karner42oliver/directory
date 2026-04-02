<?php
/**
* The loop that displays posts.
* You can override this file in your active theme.
*
* The loop displays the posts and the post content.  See
* http://codex.wordpress.org/The_Loop to understand it and
* http://codex.wordpress.org/Template_Tags to understand
* the tags used in it.
*
* This can be overridden in child themes with loop.php or
* loop-template.php, where 'template' is the loop context
* requested by a template. For example, loop-index.php would
* be used if it exists and we ask for the loop with:
* <code>get_template_part( 'loop', 'index' );</code>
*
* @package Verzeichnis
* @subpackage Author
* @since Verzeichnis 1.0
*/

global $post, $wp_query, $query_string, $Directory_Core;

$dr = $Directory_Core;
?>

<?php /* Display navigation to next/previous pages when applicable */ ?>
<?php echo $dr->pagination( $dr->pagination_top );

//breadcrumbs
if ( ! is_post_type_archive('directory_listing') ): ?>

<div class="breadcrumbtrail">
	<p class="page-title dp-taxonomy-name"><?php the_dr_breadcrumbs(); ?></p>
	<div class="clear"></div>
</div>
<?php endif; ?>
<div class="clear"></div>
<?php /* If there are no posts to display, such as an empty archive page */ ?>
<?php if ( ! have_posts() ) : ?>
<div id="post-0" class="post error404 not-found">
	<h1 class="entry-title"><?php _e( 'Nicht gefunden', DR_TEXT_DOMAIN ); ?></h1>
	<div class="entry-content">
		<p><?php _e( 'Entschuldigung, aber für das angeforderte Verzeichnis wurden keine Ergebnisse gefunden. Vielleicht hilft die Suche dabei, einen entsprechenden Eintrag zu finden.', DR_TEXT_DOMAIN ); ?></p>
		<?php get_search_form(); ?>
	</div><!-- .entry-content -->
</div><!-- #post-0 -->
<?php endif; ?>

<?php
/* Start the Loop.
*
* In Twenty Ten we use the same loop in multiple contexts.
* It is broken into three main parts: when we're displaying
* posts that are in the gallery category, when we're displaying
* posts in the asides category, and finally all other posts.
*
* Additionally, we sometimes check for whether we are on an
* archive page, a search page, etc., allowing for small differences
* in the loop on each template without actually duplicating
* the rest of the loop that is shared.
*
* Without further ado, the loop:
*/

$last = $wp_query->post_count;
$count = 1;

?>
<div id="dr_listing_list">

	<?php while ( have_posts() ) : the_post();
	// Retrieves categories list of current post, separated by commas.
	$categories_list = get_the_category_list( __(', ',DR_TEXT_DOMAIN),'');

	// Retrieves tag list of current post, separated by commas.
	$tags_list = get_the_tag_list('', __(', ',DR_TEXT_DOMAIN), '');

	//add last css class for styling grids
	if ( $count == $last )
	$class = 'dr_listing last-listing';
	else
	$class = 'dr_listing';

	?>
	<div class="<?php echo $class ?>">
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<div class="entry-post">
				<h2 class="entry-title">
					<a href="<?php echo the_permalink(); ?>" title="<?php echo sprintf( esc_attr__( 'Permalink zu %s', DR_TEXT_DOMAIN ), get_the_title() ); ?>" rel="bookmark"><?php the_title();?></a>
				</h2>

				<div class="entry-meta">
					<?php the_dr_posted_on(); ?>
					<div class="entry-utility">
						<?php if ( $categories_list ): ?>
						<span class="cat-links"><?php echo sprintf( __( '<span class="%1$s">Veröffentlicht in</span> %2$s', DR_TEXT_DOMAIN ), 'entry-utility-prep entry-utility-prep-cat-links', $categories_list ); ?></span><br />
						<?php
						unset( $categories_list );
						endif;
						if ( $tags_list ): ?>
						<span class="tag-links"><?php echo sprintf ( __( '<span class="%1$s">Markiert</span> %2$s', DR_TEXT_DOMAIN ), 'entry-utility-prep entry-utility-prep-tag-links', $tags_list ); ?></span><br />
						<?php
						unset( $tags_list );
						endif;
						do_action( 'sr_avg_ratings_of_listings', get_the_ID() ); ?>
						<br /><span class="comments-link"><?php comments_popup_link( __( 'Hinterlasse eine Bewertung', DR_TEXT_DOMAIN ), __( '1 Bewertung', DR_TEXT_DOMAIN ), esc_attr__( '% Bewertungen', DR_TEXT_DOMAIN ), '', __( 'Bewertungen deaktiviert', DR_TEXT_DOMAIN ) ); ?></span>
					</div>
				</div>

				<div class="entry-summary">

					<?php if (has_post_thumbnail()): ?>
					<a href="<?php the_permalink(); ?>">
						<?php the_post_thumbnail( array(50,50), array('class' => 'alignleft dr_listing_image_listing', 'title' => get_the_title(),) ); ?>
					</a>
					<?php
					endif;
					the_excerpt();

					$dr_profile_defaults = array(
						'company' => '',
						'phone' => '',
						'city' => '',
						'website' => '',
					);
					$dr_profile = get_post_meta(get_the_ID(), '_dr_business_profile', true);
					$dr_profile = is_array($dr_profile) ? array_merge($dr_profile_defaults, $dr_profile) : $dr_profile_defaults;
					?>

					<?php if (!empty($dr_profile['company']) || !empty($dr_profile['phone']) || !empty($dr_profile['city']) || !empty($dr_profile['website'])): ?>
					<div class="dr-business-snippet">
						<?php if (!empty($dr_profile['company'])): ?><span><strong><?php _e('Firma:', DR_TEXT_DOMAIN); ?></strong> <?php echo esc_html($dr_profile['company']); ?></span><?php endif; ?>
						<?php if (!empty($dr_profile['phone'])): ?><span><strong><?php _e('Telefon:', DR_TEXT_DOMAIN); ?></strong> <?php echo esc_html($dr_profile['phone']); ?></span><?php endif; ?>
						<?php if (!empty($dr_profile['city'])): ?><span><strong><?php _e('Ort:', DR_TEXT_DOMAIN); ?></strong> <?php echo esc_html($dr_profile['city']); ?></span><?php endif; ?>
						<?php if (!empty($dr_profile['website'])): ?><span><a href="<?php echo esc_url($dr_profile['website']); ?>" target="_blank" rel="noopener noreferrer"><?php _e('Webseite', DR_TEXT_DOMAIN); ?></a></span><?php endif; ?>
					</div>
					<?php endif; ?>
					?>
				</div>
				<div class="clear"></div>
			</div><!-- .entry-post -->

			<?php $count++;
			?>
		</div><!-- #post-## -->
	</div>
	<?php endwhile; ?>
</div>

<?php /* Display navigation to next/previous pages when applicable */ ?>
<?php echo $dr->pagination( $dr->pagination_bottom ); ?>