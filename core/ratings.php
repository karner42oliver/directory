<?php

/**
 * Ratings_Core
 *
 * @package Ratings
 * @version 1.0.0
 * @copyright PSOURCE 2011-2024 {@link https://github.com/cp-psource}
 * @author DerN3rd (PSOURCE)
 * @license GNU General Public License (Version 2 - GPLv2) {@link http://www.gnu.org/licenses/gpl-2.0.html}
 */
class DR_Ratings
{

    /** @var string The current page. Used for custom hooks. */
    var $page;
    /** @var array Array with values used for determining post quality. */
    var $quality;

    /**
     * Class constructor.
     */

    function __construct()
    {
        add_action('init', array(&$this, 'init'));
        add_action('wp_enqueue_scripts', array(&$this, 'enqueue_styles'));
        add_action('wp_enqueue_scripts', array(&$this, 'enqueue_scripts'));
        add_action('wp_head', array(&$this, 'print_scripts'));
        add_action('sr_avg_rating', array(&$this, 'render_avg_rating'));
        add_action('sr_avg_ratings_of_listings', array(&$this, 'render_avg_ratings_of_listings'));
        add_action('sr_user_rating', array(&$this, 'render_user_rating'));
        add_action('sr_rate_this', array(&$this, 'render_rate_this'));
        add_action('wp_ajax_sr_save_vote', array(&$this, 'handle_ajax_requests'));
        add_action('wp_ajax_nopriv_sr_save_vote', array(&$this, '/usr/share/nginx'));
    }

    /**
     * Hook class methods.
     *
     * @access public
     * @return void
     */
    function init()
    {
        $this->quality = array(
            1 => __('Nicht so toll', DR_TEXT_DOMAIN),
            2 => __('Ziemlich gut', DR_TEXT_DOMAIN),
            3 => __('Gut', DR_TEXT_DOMAIN),
            4 => __('Großartig!', DR_TEXT_DOMAIN),
            5 => __('Exzellent!', DR_TEXT_DOMAIN)
        );
    }

    /**
     * Returns rating for post. If user ID is passed it will return the rating
     * given by the particular user ( if it exists for the current post ). If no
     * user ID is passed the method returns the aggregated rating for the post.
     *
     * @param mixed $post_id
     * @param mixed $user_id
     * @access public
     * @return string|array
     */
    function get_rating($post_id, $user_id = null)
    {
        if (isset($user_id)) {
            $rating = get_user_meta($user_id, '_sr_post_vote', true);
            if (isset($rating[$post_id])) {
                return $rating[$post_id];
            } else {
                return 'no_rate';
            }
        } else {
            $votes = get_post_meta($post_id, '_sr_post_votes', true) ? get_post_meta($post_id, '_sr_post_votes', true) : '0';
            $rating = get_post_meta($post_id, '_sr_post_rating', true) ? get_post_meta($post_id, '_sr_post_rating', true) : '0';
            $avg = (!empty($rating) && !empty($votes)) ? round((int)$rating / (int)$votes) : '0';
            return array('votes' => $votes, 'rating' => $rating, 'avg' => $avg);
        }
    }

    /**
     * save_rating
     *
     * @param mixed $post_id
     * @param mixed $rating
     * @access public
     * @return void
     */
    function save_rating($post_id, $rating) {
        if (!is_user_logged_in()) return; //Not logged in, nowhere to store a vote.
    
        $votes = get_post_meta($post_id, '_sr_post_votes', true);
        $current_rating = get_post_meta($post_id, '_sr_post_rating', true);
    
        $user_id = get_current_user_id();
        $voted = get_user_meta($user_id, '_sr_post_vote', true);
        $vote = empty($voted[$post_id]) ? 1 : 0;
        $current_rating = empty($voted[$post_id]) ? $current_rating : (int) $current_rating - (int) $voted[$post_id]; //Remove any previous rating
        $voted[$post_id] = $rating;
        update_user_meta($user_id, '_sr_post_vote', $voted);
        $votes = intval($votes) + $vote;
        $rating = intval($current_rating) + intval($rating);
        update_post_meta($post_id, '_sr_post_votes', $votes);
        update_post_meta($post_id, '_sr_post_rating', $rating);
    }

    function delete_rating($post_id){
    if (!is_user_logged_in()) return; //Not logged in, nowhere to store a vote.

    $votes = get_post_meta($post_id, '_sr_post_votes', true);
    $votes = intval($votes) - 1;
    update_post_meta($post_id, '_sr_post_votes', $votes);

    $current_rating = get_post_meta($post_id, '_sr_post_rating', true);
    $voted = get_user_meta(get_current_user_id(), '_sr_post_vote', true);
    $score = isset($voted[$post_id]) ? $voted[$post_id] : 0;
    $current_rating = $current_rating - intval($score);
    update_post_meta($post_id, '_sr_post_rating', $current_rating);

    unset($voted[$post_id]);
    update_user_meta(get_current_user_id(), '_sr_post_vote', $voted);
}

    /**
     * Ajax callback which gets the post types associated with each page.
     *
     * @return JSON Encoded data
     **/
    function handle_ajax_requests(){
        if ($_POST['rate'] == 0) {
            $this->delete_rating($_POST['post_id']);
        } else {
            // verify user input!
            $rating = $this->in_range($_POST['rate'], 1, 5);
            // update statistic and save to file
            $this->save_rating($_POST['post_id'], $rating);
        }
        $respons = $this->get_rating($_POST['post_id']);
        do_action('directory_after_rated', $_POST['post_id'], $rating);
        // return json object
        header("Content-Type: application/json");
        echo json_encode($respons);
        exit;
    }

    /**
     * Enqueue styles.
     */
    function enqueue_styles()
    {
        wp_enqueue_style('jquery-ui-stars', DR_PLUGIN_URL . 'ui-front/js/jquery-ui-stars/jquery-ui-stars.css');
    }

    /**
     * Enqueue scripts.
     */
    function enqueue_scripts()
    {

        wp_register_script('jquery-ui-stars', DR_PLUGIN_URL . 'ui-front/js/jquery-ui-stars/jquery-ui-stars.js', array('jquery', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-form'));
        wp_enqueue_script('jquery-ui-stars');
    }

    /**
     * Print document scripts.Handles the colorpickers.
     *
     * @access public
     * @global object $post
     * @return void
     */
    function print_scripts() {
        global $post;
        $post_id = isset($post->ID) ? $post->ID : 0;
        $ajaxurl = admin_url('admin-ajax.php'); 
        ?>
        <script type="text/javascript">
            jQuery(function ($) {
                var $avg = $(".avg"),
                    $rat = $(".rat"),
                    $user_votes = $(".user_votes"),
                    $messages = $('<div class="messages"/>').appendTo($rat);
    
                $avg.children().not(":input").hide();
                $rat.children().not("select, .messages").hide();
                $user_votes.children().not(":input").hide();
                
                $avg.stars();
                $(".avg_of_listings").stars();
                $user_votes.stars();
    
                $rat.stars({
                    inputType: "select",
                    cancelShow: true,
                    captionEl: $(".caption"),
                    callback: function (ui, type, value) {
                        $messages.text("<?php _e('Speichern...', DR_TEXT_DOMAIN); ?>").stop().css("opacity", 1).fadeIn(30);
    
                        $.post("<?php echo $ajaxurl; ?>", {
                            action: 'sr_save_vote',
                            post_id: <?php echo $post_id; ?>,
                            rate: value
                        }, function (response) {
                            if (response.error) {
                                $messages.text("<?php _e('Fehler beim Speichern der Bewertung.', DR_TEXT_DOMAIN); ?>").fadeIn(500);
                                return;
                            }
                            
                            $avg.stars("select", Math.round(response.avg));
                            $(".all_votes-<?php echo $post_id; ?>").text(response.votes);
                            $(".all_avg<?php echo $post_id; ?>").text(response.avg);
    
                            $messages.text("<?php _e('Bewertung gespeichert', DR_TEXT_DOMAIN); ?> (" + value + "). <?php _e('Danke!', DR_TEXT_DOMAIN); ?>").fadeIn(500);
                            setTimeout(function () {
                                $messages.fadeOut(1000);
                            }, 2000);
                        }, "json");
                    }
                });
            });
        </script>
        <?php
    }

    /**
     * Render rate this block.
     *
     * @access public
     * @global object $post
     * @return void
     */
    function render_rate_this()
    {
        global $post;

        $user_id = get_current_user_id();
        $rating = $this->get_rating($post->ID, $user_id);
        /*
        if ( 'no_rate' != $rating ) {
        ?>
        <div class="sr-user-rating"><strong><?php _e( 'Rating:', DR_TEXT_DOMAIN ); ?></strong>
        <span>(<?php echo $this->quality[$rating] ?>)</span>
        <form class="user_votes" style="float: left; padding: 3px 8px 0 0;" action="#">
        <?php foreach ( $this->quality as $scale => $text ): ?>
        <input type="radio" name="rate_avg" value="<?php echo $scale; ?>" title="<?php echo $text; ?>" disabled="disabled" <?php echo $scale == $rating ? 'checked="checked"' : '' ?> />
        <?php endforeach; ?>
        </form>
        </div>

        <?php

        }
        else
        */
        {
            //$rating = $this->get_rating( $post->ID );
            ?>
            <?php /*
			<?php if (isset($post_message)): ?>
			<div class="message-box ok">Thanks, vote saved: <?php echo $post_message ?></div>
			<?php endif; ?>
			*/
            ?>
            <div class="clear-left"></div>
            <div class="sr-avg-rating"><strong><?php _e('Bewerte dies:', DR_TEXT_DOMAIN); ?></strong> <span
                    class="caption"></span>

                <form class="rat" action="#" method="post">
                    <select name="rate">
                        <?php foreach ($this->quality as $scale => $text): ?>
                            <option <?php selected($scale == $rating); ?>
                                value="<?php echo $scale; ?>"><?php echo $text; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="submit" value="<?php _e('Bewerte es!', DR_TEXT_DOMAIN); ?>"/>
                </form>
            </div> <?php
        }
    }

    /**
     * Render avarage rating.
     *
     * @access public
     * @return void
     */
    function render_avg_rating()
    {
        global $post;
        $rating = $this->get_rating($post->ID); ?>
        <div class="sr-rate-this"><strong><?php _e('Durchschnittliche Bewertung', DR_TEXT_DOMAIN); ?></strong>
            <span>(<span
                    class="all_votes-<?php echo $post->ID; ?>"><?php echo $rating['votes']; ?></span> <?php _e('Bewertung(en)', DR_TEXT_DOMAIN); ?>
                : <span class="all_avg<?php echo $post->ID; ?>"><?php echo $rating['avg']; ?></span> <?php _e('Sterne', DR_TEXT_DOMAIN); ?>)</span>

            <form class="avg" style="float: left; padding: 3px 8px 0 0;" action="#">
                <?php foreach ($this->quality as $scale => $text): ?>
                    <input type="radio" name="rate_avg" value="<?php echo $scale; ?>" title="<?php echo $text; ?>"
                           disabled="disabled" <?php echo $scale == $rating['avg'] ? 'checked="checked"' : '' ?> />
                <?php endforeach; ?>
            </form>
        </div> <?php
    }

    /**
     * Render avarage rating of listings on category page.
     *
     * @access public
     * @return void
     */
    function render_avg_ratings_of_listings($post_id = 0)
    {

        if (0 == $post_id) {
            global $post;
            $post_id = $post->ID;
        }

        $rating = $this->get_rating($post_id); ?>
        <div class="sr-rate-this"><strong><?php _e('Durchschnittliche Bewertung', DR_TEXT_DOMAIN); ?></strong>
            <span>(<span
                    class="all_votes<?php echo $post_id; ?>"><?php echo $rating['votes']; ?></span> <?php _e('Bewertung(en)', DR_TEXT_DOMAIN); ?>
                : <span class="all_avg<?php echo $post_id; ?>"><?php echo $rating['avg']; ?></span> <?php _e('Sterne', DR_TEXT_DOMAIN); ?>)</span>

            <form class="avg_of_listings" style="padding: 3px 8px 0 0;" action="#">
                <?php foreach ($this->quality as $scale => $text): ?>
                    <input type="radio" name="rate_avg" value="<?php echo $scale; ?>" title="<?php echo $text; ?>"
                           disabled="disabled" <?php echo $scale == $rating['avg'] ? 'checked="checked"' : '' ?> />
                <?php endforeach; ?>
            </form>
        </div> <?php
    }

    /**
     * render_user_rating
     *
     * @access public
     * @return void
     */
    function render_user_rating($user_id = 0)
    {
        global $post;

        $user_id = (empty($user_id)) ? get_current_user_id() : $user_id;

        $rating = $this->get_rating($post->ID, $user_id);

        ?>
        <div class="sr-user-rating"><strong><?php _e('Bewertung:', DR_TEXT_DOMAIN); ?></strong>
            <span>(<?php echo $this->quality[$rating] ?>)</span>

            <form class="user_votes" style="float: left; padding: 3px 8px 0 0;" action="#">
                <?php
                foreach ($this->quality as $scale => $text):
                    ?>
                    <input type="radio" name="rate_avg" value="<?php echo $scale; ?>" title="<?php echo $text; ?>"
                           disabled="disabled" <?php echo $scale == $rating ? 'checked="checked"' : '' ?> />
                <?php endforeach; ?>
            </form>
        </div> <?php
    }

    /**
     * in_range
     *
     * @param mixed $val
     * @param int $from
     * @param float $to
     * @access public
     * @return void
     */
    function in_range($val, $from = 0, $to = 100)
    {
        return min($to, max($from, (int)$val));
    }

}

new DR_Ratings();
