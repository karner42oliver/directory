<?php if (!defined('ABSPATH')) die('Kein direkter Zugriff erlaubt!');

$transactions = new DR_Transactions;

?>

<div class="wrap">

	<?php $this->render_admin( 'navigation', array( 'page' => 'directory_credits','tab' => 'my-credits' ) ); ?>
	<?php $this->render_admin( 'message' ); ?>

	<h1><?php _e( 'Mein Verzeichnis Guthaben', $this->text_domain ); ?></h1>

	<form action="#" method="post">

		<h3><?php _e( 'Verfügbares Guthaben', $this->text_domain ); ?></h3>
		<table class="form-table">
			<tr>
				<th>
					<label for="available_credits"><?php _e('Verfügbares Guthaben', $this->text_domain ) ?></label>
				</th>
				<td>
					<input type="text" id="available_credits" class="small-text" name="available_credits" value="<?php echo $transactions->credits; ?>" disabled="disabled" />
					<span class="description"><?php _e( 'Dein derzeit verfügbares Guthaben.', $this->text_domain ); ?></span>
				</td>
			</tr>
		</table>

	</form>

	<form action="#" method="post" class="purchase_credits" >
		<h3><?php _e( 'Kaufe zusätzliches Guthaben', $this->text_domain ); ?></h3>
		<table class="form-table">
			<tr>
				<th>
					<label for="purchase_credits"><?php _e('Kaufe zusätzliches Guthaben', $this->text_domain ) ?></label>
				</th>
				<td>
					<p class="submit">
						<?php wp_nonce_field('verify'); ?>
						<input type="submit" class="button-secondary" name="purchase" value="<?php _e( 'Kaufen', $this->text_domain ); ?>" />
					</p>
				</td>
			</tr>
		</table>

	</form>

	<?php $credits_log = $transactions->credits_log; ?>
	<h3><?php _e( 'Kaufhistorie', $this->text_domain ); ?></h3>
	<table class="form-table">
		<?php if ( is_array( $credits_log ) ): ?>
		<?php foreach ( $credits_log as $log ): ?>
		<tr>
			<th>
				<label for="available_credits"><?php _e('Datum:', $this->text_domain ) ?> <?php echo $this->format_date( $log['date'] ); ?></label>
			</th>
			<td>
				<input type="text" id="available_credits" class="small-text" name="available_credits" value="<?php echo $log['credits']; ?>" disabled="disabled" />
				<?php if($log['credits'] < 0): ?> 
				<span class="description"><?php _e( 'Verzeichnis Ausgegebenes Guthaben.', $this->text_domain ); ?></span>
				<?php else: ?>
				<span class="description"><?php _e( 'Verzeichnis Angekauftes Guthaben.', $this->text_domain ); ?></span>
				<?php endif; ?>
			</td>
		</tr>
		<?php endforeach; ?>
		<?php else: ?>
		<?php echo $credits_log; ?>
		<?php endif; ?>
	</table>

</div>