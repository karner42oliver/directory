<?php if (!defined('ABSPATH')) die('Kein direkter Zugriff erlaubt!');

$options = $this->get_options('general');

$default_email = __(
'Hallo TO_NAME, du hast eine Nachricht erhalten von

Name: FROM_NAME
Email: FROM_EMAIL
Betreff: FROM_SUBJECT
Nachricht:

FROM_MESSAGE


Eintragslink: POST_LINK
', $this->text_domain);

?>

<div class="wrap">

	<?php $this->render_admin( 'navigation', array( 'page' => 'directory_settings', 'tab' => 'general' ) ); ?>
	<?php $this->render_admin( 'message' ); ?>

	<h1><?php _e( 'Allgemeine Einstellungen', $this->text_domain ); ?></h1>

	<form action="#" method="post">
		<div class="postbox">
			<h3 class='hndle'><span><?php _e( 'Verzeichnismitgliedsrolle', $this->text_domain ); ?></span></h3>
			<div class="inside">
				<table class="form-table">
					<tr>
						<th>
							<label for="roles"><?php _e( 'Mitgliederrolle zuweisen', $this->text_domain ) ?></label>
						</th>
						<td>
							<select id="member_role" name="member_role" style="width:200px;">
								<?php wp_dropdown_roles(@$options['member_role']); ?>
							</select>
							<br /><span class="description"><?php _e('Wähle die Rolle aus, der Du bei der Anmeldung ein Verzeichnismitglied zuweisen möchtest.', $this->text_domain); ?></span>
							<br /><span class="description"><?php _e('Wenn Du mehrere Plugins mit Anmeldungen ausführst, verwende dieselbe Rolle für beide.', $this->text_domain); ?></span>
						</td>
					</tr>
					<tr>
						<th>
							<label>Mitgliederrollen verwalten</label>
						</th>
						<td>
							<label>Rollennamen hinzufügen</label><br />
							<input type="text" id="new_role" name="new_role" size="30"/>
							<input type="submit" class="button" id="add_role" name="add_role" value="<?php _e( 'Rolle hinzufügen', $this->text_domain ); ?>" />
							<br /><span class="description"><?php _e('Füge eine neue Rolle hinzu. Nur alphanumerische Zeichen.', $this->text_domain); ?></span>
							<br /><span class="description"><?php _e('Wenn Du eine neue Rolle hinzufügst, musst Du die entsprechenden Funktionen hinzufügen, damit sie funktionsfähig ist.', $this->text_domain); ?></span>
							<br /><br />
							<label>Benutzerdefinierte Rollen</label><br />
							<select id="delete_role" name="delete_role"  style="width:200px;">
								<?php
								global $wp_roles;
								$system_roles = array('administrator', 'editor', 'author', 'contributor', 'subscriber');
								$role_names = $wp_roles->role_names;
								foreach ( (array) $role_names as $role => $name ):
								if(! in_array($role, $system_roles) ): //Don't delete system roles.
								?>
								<option value="<?php echo $role; ?>"><?php echo $name; ?></option>
								<?php
								endif;
								endforeach;
								?>
							</select>
							<input type="button" class="button" onclick="jQuery(this).hide(); jQuery('#remove_role').show();" value="<?php _e( 'Eine Rolle entfernen', $this->text_domain ); ?>" />
							<input type="submit" class="button-primary" id="remove_role" name="remove_role" value="<?php _e( 'Bestätigen Diese Rolle entfernen', $this->text_domain ); ?>" style="display: none;" />

						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="postbox">
			<h3 class='hndle'><span><?php _e( 'Verzeichnisstatusoptionen', $this->text_domain ) ?></span></h3>
			<div class="inside">
				<table class="form-table">
					<tr>
						<th>
							<label for="moderation"><?php _e('Verfügbare Verzeichnisstatusoptionen', $this->text_domain ) ?></label>
						</th>
						<td>
							<label><input type="checkbox" name="moderation[publish]" value="1" <?php checked( ! empty($options['moderation']['publish']) ) ?> /> <?php _e('Veröffentlichen', $this->text_domain); ?></label>
							<br /><span class="description"><?php _e('Mitgliedern erlauben, Einträge selbst zu veröffentlichen.', $this->text_domain); ?></span>
							<br /><label><input type="checkbox" name="moderation[pending]" value="1" <?php checked( ! empty($options['moderation']['pending']) ) ?> /> <?php _e('Ausstehende Bewertung', $this->text_domain); ?></label>
							<br /><span class="description"><?php _e('Der Eintrag muss von einem Administrator überprüft werden.', $this->text_domain ); ?></span>
							<br /><label><input type="checkbox" name="moderation[draft]" value="1" <?php checked( ! empty($options['moderation']['draft']) ) ?> /> <?php _e('Entwurf', $this->text_domain); ?></label>
							<br /><span class="description"><?php _e('Mitgliedern erlauben, Entwürfe zu speichern.', $this->text_domain); ?></span>
						</td>
					</tr>
				</table>
			</div>
		</div>


		<div class="postbox">
			<h3 class='hndle'><span><?php _e( 'Umleitungsoptionen', $this->text_domain ) ?></span></h3>
			<div class="inside">
				<p class="description"><?php _e( 'Hier kannst Du die Seite einstellen, auf die der Benutzer nach Registrierung oder Anmeldung weitergeleitet wird.', $this->text_domain ) ?></p>
				<table class="form-table">
					<tr>
						<th>
							<label for="signin_url"><?php _e( 'Weiterleitungs-URL (Anmelden):', $this->text_domain ) ?></label>
						</th>
						<td>
							<input type="text" name="signin_url" id="signin_url" value="<?php echo (empty($options['signin_url']) ) ? '' : $options['signin_url']; ?>" size="50" />
							<span class="description"><?php _e( 'standardmäßig zur Startseite', $this->text_domain ) ?></span>
						</td>
					</tr>
					<tr>
						<th>
							<label for="logout_url"><?php _e( 'Weiterleitungs-URL (Logout):', $this->text_domain ) ?></label>
						</th>
						<td>
							<input type="text" name="logout_url" id="logout_url" value="<?php echo (empty($options['logout_url']) ) ? '' : $options['logout_url']; ?>" size="50" />
							<span class="description"><?php _e( 'standardmäßig zur Startseite', $this->text_domain ) ?></span>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="postbox">
			<h3 class='hndle'><span><?php _e( 'Optionen für benutzerdefinierte Felder', $this->text_domain ) ?></span></h3>
			<div class="inside">
				<table class="form-table">
					<tr>
						<th>
							<label for="custom_fields_structure"><?php _e( 'Benutzerdefinierte Felder anzeigen als:', $this->text_domain ) ?></label>
						</th>
						<td>
							<select name="custom_fields_structure" id="custom_fields_structure" style="width:100px">
								<option value="table" <?php selected( empty( $options['custom_fields_structure']) ? false : 'table' == $options['custom_fields_structure'] ); ?> ><?php _e( 'table', $this->text_domain ) ?></option>
								<option value="ul" <?php selected( empty( $options['custom_fields_structure']) ? false : 'ul' == $options['custom_fields_structure'] ); ?> ><?php _e( 'ul', $this->text_domain ) ?></option>
								<option value="div" <?php selected( empty( $options['custom_fields_structure']) ? false : 'div' == $options['custom_fields_structure'] ); ?> ><?php _e( 'div', $this->text_domain ) ?></option>
							</select>
							<span class="description"><?php _e( 'Struktur des benutzerdefinierten Felderblocks.', $this->text_domain ) ?></span>
						</td>
					</tr>


				</table>
			</div>
		</div>

		<div class="postbox">
			<h3 class='hndle'><span><?php _e( 'Anzeigeoptionen', $this->text_domain ) ?></span></h3>
			<div class="inside">
				<table class="form-table">
					<tr>
						<th><label for="field_image_req"><?php _e( 'Bildfeld:', $this->text_domain ); ?></label></th>
						<td>
							<input type="hidden" name="field_image_req" value="0" <?php checked( isset( $options['field_image_req'] ) && 1 == $options['field_image_req'] ); ?> />
							<label>
							<input type="checkbox" id="field_image_req" name="field_image_req" value="1" <?php checked( isset( $options['field_image_req'] ) && 1 == $options['field_image_req'] ); ?> />
							<span class="description"><?php _e( 'nicht benötigt', $this->text_domain ); ?></span>
							</label>
						</td>
					</tr>
					<tr>
						<th><label for="media_manager"><?php _e( 'Medien-Manager:', $this->text_domain ); ?></label></th>
						<td>
							<input type="hidden" name="media_manager" value="0" />
							<label>
							<input type="checkbox" id="media_manager" name="media_manager" value="1" <?php checked( isset( $options['media_manager'] ) && 1 == $options['media_manager'] ); ?> />
							<span class="description"><?php _e( 'Vollen Medienmanager für Feature-Image-Uploads aktivieren', $this->text_domain ); ?></span>
							</label>
						</td>
					</tr>
					<tr>
						<th>
							<label for="count_cat"><?php esc_html_e( 'Anzahl der Kategorien:', $this->text_domain ) ?></label>
						</th>
						<td>
							<input type="text" name="count_cat" id="count_cat" value="<?php echo (empty( $options['count_cat'] ) ) ? '10' : $options['count_cat']; ?>" size="2" />
							<span class="description"><?php esc_html_e( 'eine Anzahl von Kategorien, die in der Liste der Kategorien angezeigt werden.', $this->text_domain ) ?></span>
						</td>
					</tr>

					<tr>
						<th><label for="display_parent_count"><?php esc_html_e( 'Anzeigeanzähler in übergeordneten Kategorien:', $this->text_domain ); ?></label></th>
						<td>
							<input type="hidden" name="display_parent_count" value="0" />
							<label>
							<input type="checkbox" id="display_parent_count" name="display_parent_count" value="1" <?php checked( isset( $options['display_parent_count'] ) && 1 == $options['display_parent_count'] ); ?> />
							<span class="description"><?php esc_html_e( 'Zeigt die Anzahl der Einträge für die obersten übergeordneten Kategorien an', $this->text_domain ); ?></span>
							</label>
						</td>
					</tr>

					<tr>
						<th>
							<label for="count_sub_cat"><?php esc_html_e( 'Anzahl der Unterkategorie:', $this->text_domain ) ?></label>
						</th>
						<td>
							<input type="text" name="count_sub_cat" id="count_sub_cat" value="<?php echo ( empty( $options['count_sub_cat'] ) ) ? '5' : $options['count_sub_cat']; ?>" size="2" />
							<span class="description"><?php esc_html_e( 'eine Anzahl von Unterkategorien, die für jede Kategorie in der Kategorienliste angezeigt werden.', $this->text_domain ) ?></span>
						</td>
					</tr>

					<tr>
						<th><label for="display_sub_count"><?php esc_html_e( 'Anzeigeanzahl für Unterkategorien:', $this->text_domain ); ?></label></th>
						<td>
							<input type="hidden" name="display_sub_count" value="0" />
							<label>
								<input type="checkbox" id="display_sub_count" name="display_sub_count" value="1" <?php checked( !isset( $options['display_sub_count'] ) || 1 == $options['display_sub_count'] ); ?> />
								<span class="description"><?php esc_html_e( 'Zeige die Anzahl der Einträge für Unterkategorien an', $this->text_domain ); ?></span>
							</label>
						</td>
					</tr>

					<tr>
						<th>
							<?php esc_html_e( 'Leere Unterkategorie:', $this->text_domain ) ?>
						</th>
						<td>
							<input type="hidden" name="hide_empty_sub_cat" value="0" />
							<label>
								<input type="checkbox" name="hide_empty_sub_cat" id="hide_empty_sub_cat" value="1" <?php checked( empty( $options['hide_empty_sub_cat'] ) ? false : ! empty($options['hide_empty_sub_cat']) ); ?> />
								<span class="description"><?php esc_html_e( 'Leere Unterkategorie ausblenden', $this->text_domain ) ?></span>
							</label>
						</td>
					</tr>
					<?php
					/*
					<tr>
					<th>
					<?php _e( 'Display listing:', $this->text_domain ) ?>
					</th>
					<td>
					<input type="checkbox" name="display_listing" id="display_listing" value="1" <?php echo ( isset( $options['display_listing'] ) && '1' == $options['display_listing'] ) ? 'checked' : ''; ?> />
					<label for="display_listing"><?php _e( 'add Listings to align blocks according to height while  sub-categories are lacking', $this->text_domain ) ?></label>
					</td>
					</tr>
					*/
					?>
				</table>
			</div>
		</div>

		<div class="postbox">
			<h3 class='hndle'><span><?php _e( 'Paginierungseinstellungen', $this->text_domain ); ?></span></h3>
			<div class="inside">
				<table class="form-table">
					<tr>
						<th><label for="field_image_req"><?php _e( 'Paginierungsposition:', $this->text_domain ); ?></label></th>
						<td>
							<input type="hidden" name="pagination_top" value="0" />
							<label>
							<input type="checkbox" id="pagination_top" name="pagination_top" value="1" <?php echo ( isset( $options['pagination_top'] ) && 1 == $options['pagination_top'] ) ? 'checked' : ''; ?> />
							<span class="description"><?php _e( 'oben auf der Seite anzeigen.', $this->text_domain ); ?></span>
							</label>
							<br />
							<input type="hidden" name="pagination_bottom" value="0" />
							<label>
							<input type="checkbox" id="pagination_bottom" name="pagination_bottom" value="1" <?php echo ( isset( $options['pagination_bottom'] ) && 1 == $options['pagination_bottom'] ) ? 'checked' : ''; ?> />
							<span class="description"><?php _e( 'Anzeige am Ende der Seite.', $this->text_domain ); ?></span>
							</label>
						</td>
					</tr>
					<tr>
						<th><label for="pagination_range"><?php _e( 'Paginierungsbereich:', $this->text_domain ); ?></label></th>
						<td>
							<input type="text" id="pagination_range" name="pagination_range" size="4" value="<?php echo ( isset( $options['pagination_range'] ) && '' != $options['pagination_range'] ) ? $options['pagination_range'] : '4'; ?>" />
							<span class="description"><?php _e( 'Anzahl der Seitenlinks, die gleichzeitig in der Paginierung angezeigt werden sollen', $this->text_domain ); ?></span>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="postbox">
			<h3 class='hndle'><span><?php _e( 'Benachrichtigungseinstellungen', $this->text_domain ); ?></span></h3>
			<div class="inside">
				<table class="form-table">
					<tr>
						<th><label for="disable_contact_form"><?php _e( 'Kontaktformular deaktivieren:', $this->text_domain ); ?></label></th>
						<td>
							<input type="hidden" name="disable_contact_form" value="0" />
							<label>
							<input type="checkbox" id="disable_contact_form" name="disable_contact_form" value="1" <?php checked( isset( $options['disable_contact_form'] ) && 1 == $options['disable_contact_form'] ); ?> />
							<span class="description"><?php _e( 'Kontaktformular deaktivieren', $this->text_domain ); ?></span>
							</label>
						</td>
					</tr>
					<tr>
						<th><label for="cc_admin"><?php _e( 'CC den Administrator:', $this->text_domain ); ?></label></th>
						<td>
							<input type="hidden" name="cc_admin" value="0" />
							<label>
							<input type="checkbox" id="cc_admin" name="cc_admin" value="1" <?php checked( isset( $options['cc_admin'] ) && 1 == $options['cc_admin'] ); ?> />
							<span class="description"><?php _e( 'cc der Administrator', $this->text_domain ); ?></span>
							</label>
						</td>
					</tr>
					<th><label for="cc_sender"><?php _e( 'CC den Absender:', $this->text_domain ); ?></label></th>
					<td>
						<input type="hidden" name="cc_sender" value="0" />
						<label>
						<input type="checkbox" id="cc_sender" name="cc_sender" value="1" <?php checked( isset( $options['cc_sender'] ) && 1 == $options['cc_sender'] ); ?> />
						<span class="description"><?php _e( 'cc der Absender', $this->text_domain ); ?></span>
						</label>
					</td>
				</tr>
				<tr>
					<th><label for="email_subject"><?php _e( 'E-Mail Betreff:', $this->text_domain ); ?></label></th>
					<td>
						<input class="dr-full" type="text" id="email_subject" name="email_subject" size="116" value="<?php echo ( isset( $options['email_subject'] ) && '' != $options['email_subject'] ) ? $options['email_subject'] : 'SITE_NAME Kontaktanfrage: FROM_SUBJECT [ POST_TITLE ]'; ?>" />
						<br />
						<span class="description"><?php _e( 'Variablen: TO_NAME, FROM_NAME, FROM_EMAIL, FROM_SUBJECT, FROM_MESSAGE, POST_TITLE, POST_LINK, SITE_NAME', $this->text_domain ); ?></span>
					</td>
				</tr>
				<tr>
					<th><label for="field_image_req"><?php _e( 'E-Mail-Inhalt:', $this->text_domain ); ?></label></th>
					<td>
						<textarea class="dr-full" id="email_content" name="email_content" cols="120" rows="10" wrap="hard" ><?php
							echo esc_textarea( empty($options['email_content']) ? $default_email : $options['email_content'] );
						?></textarea>
						<br />
						<span class="description"><?php _e( 'Variablen: TO_NAME, FROM_NAME, FROM_EMAIL, FROM_SUBJECT, FROM_MESSAGE, POST_TITLE, POST_LINK, SITE_NAME', $this->text_domain ); ?></span>
					</td>
				</tr>
			</table>
		</div>
	</div>

	<div class="postbox">
		<h3 class='hndle'><span><?php _e( 'Erste-Schritte-Seite', $this->text_domain ) ?></span></h3>
		<div class="inside">
			<label for="show_getting_started-yes"><?php _e( 'Zeige &quot;Erste Schritte&quot; Seite, auch nachdem alle Schritte ausgeführt wurden:', $this->text_domain ) ?></label>
			
			<label>
				<input type="radio" value="1" id="show_getting_started-yes" name="show_getting_started" <?php checked( empty( $options['show_getting_started'] ) ? false : ! empty($options['show_getting_started'] ) ); ?> />
				<?php _e( 'Ja', $this->text_domain ) ?>&nbsp;&nbsp;&nbsp;&nbsp;
			</label>
			
			<label>
			<input type="radio" value="0" id="show_getting_started-no" name="show_getting_started" <?php checked( empty( $options['show_getting_started'] ) ? true : empty($options['show_getting_started']) ); ?>>
			<?php _e( 'Nein', $this->text_domain ) ?>
			</label>
			<br />
			<span class="description"><?php _e( 'Standardmäßig wird die Seite "Erste Schritte" ausgeblendet, sobald Du alle Schritte abgeschlossen hast. Verwende diese Option, um dieses Verhalten zu steuern.', $this->text_domain ) ?></span>
		</div>
	</div>

	<p class="submit">
		<?php wp_nonce_field('verify'); ?>
		<input type="hidden" name="key" value="general" />
		<input type="submit" class="button-primary" name="save" value="Änderungen speichern">
	</p>

</form>

</div>
