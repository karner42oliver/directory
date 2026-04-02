<?php if (!defined('ABSPATH')) die('Kein direkter Zugriff erlaubt!'); ?>

<div class="wrap">

	<?php $this->render_admin( 'navigation', array( 'page' => 'directory_settings', 'tab' => 'shortcodes' ) ); ?>
	<?php $this->render_admin( 'message' ); ?>

	<h1><?php _e( 'Shortcodes Übersicht', $this->text_domain ); ?></h1>

	<div class="postbox">
		<h3 class='hndle'><span><?php _e( 'Übersicht über Shortcodes', $this->text_domain ) ?></span></h3>
		<div class="inside">
			<p>
				<?php _e( 'Mit Shortcodes kannst Du dynamische Verzeichnis-Inhalte in Beiträge und Seiten Deiner Webseite einbinden. Gib sie einfach ein oder füge sie dort in Deinen Beitrag oder Seiteninhalt ein, wo sie angezeigt werden sollen. Optionale Attribute können in einem Format wie <em>[shortcode attr1="value" attr2="value"]</em> hinzugefügt werden.', $this->text_domain ) ?>
			</p>
			<p>
				<?php _e( 'Attribute: („|“ bedeutet, das eine ODER das andere zu verwenden. dh style="grid" oder style="list" nicht style="grid | list")', $this->text_domain); ?>
				<br /><?php _e( 'text = <em>Text, der auf einer Schaltfläche angezeigt werden soll</em>', $this->text_domain ) ?>
				<br /><?php _e( 'view = <em>Ob die Schaltfläche sichtbar ist, wenn man angemeldet (loggedin), abgemeldet (loggedout) oder immer (always) sind</em>', $this->text_domain ) ?>
				<br /><?php _e( 'redirect = <em>Auf der Abmelden-Schaltfläche wird angegeben, zu welcher Seite nach dem Abmelden gewechselt werden soll</em>', $this->text_domain ) ?>
				<br /><?php _e( 'lcats = <em>Eine durch Kommas getrennte Liste der anzuzeigenden Listing_Category-IDs</em>', $this->text_domain ) ?>
			</p>
			<table class="form-table">
				<tr>
					<th scope="row"><?php _e( 'Liste der Kategorien:', $this->text_domain ) ?></th>
					<td>
						<code><strong>[dr_list_categories style="grid | list" lcats="1,2,3"]</strong></code>
						<br /><span class="description"><?php _e( 'Zeigt eine Liste von Kategorien an.', $this->text_domain ) ?></span>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Einträge-Schaltfläche', $this->text_domain ) ?></th>
					<td>
						<code><strong>[dr_listings_btn text="<?php _e('Einträge', $this->text_domain);?>" view="loggedin | loggedout | always"]</strong></code> or
						<br /><code><strong>[dr_listings_btn view="loggedin | loggedout | always"]&lt;img src="<?php _e('someimage.jpg', $this->text_domain); ?>" /&gt;<?php _e('Einträge', $this->text_domain);?>[/dr_listings_btn]</strong></code>
						<br /><span class="description"><?php _e( 'Links zur Einträge-Seite. Erzeugt einen &lt;button&gt; &lt;/button&gt; mit den von Dir definierten Inhalten.', $this->text_domain ) ?></span>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Schaltfläche um Eintrag hinzufügen:', $this->text_domain ) ?></th>
					<td>
						<code><strong>[dr_add_listing_btn text="<?php _e('Eintrag hinzufügen', $this->text_domain);?>" view="loggedin | loggedout | always"]</strong></code> or
						<br /><code><strong>[dr_add_listing_btn view="loggedin | loggedout | always"]&lt;img src="<?php _e('someimage.jpg', $this->text_domain); ?>" /&gt;<?php _e('Eintrag hinzufügen', $this->text_domain);?>[/dr_add_listing_btn]</strong></code>
						<br /><span class="description"><?php _e( 'Links zur Einträge hinzufügen-Seite. Erzeugt einen &lt;button&gt; &lt;/button&gt; mit den von Dir definierten Inhalten.', $this->text_domain ) ?></span>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Meine Einträge-Schaltfläche:', $this->text_domain ) ?></th>
					<td>
						<code><strong>[dr_my_listings_btn text="<?php _e('Meine Einträge', $this->text_domain);?>" view="loggedin | loggedout | always"]</strong></code> or
						<br /><code><strong>[dr_my_listings_btn view="loggedin | loggedout | always"]&lt;img src="<?php _e('someimage.jpg', $this->text_domain); ?>" /&gt;<?php _e('Meine Einträge', $this->text_domain);?>[/dr_my_listings_btn]</strong></code>
						<br /><span class="description"><?php _e( 'Links zur Meine Einträge-Seite. Erzeugt einen &lt;button&gt; &lt;/button&gt; mit den von Dir definierten Inhalten.', $this->text_domain ) ?></span>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Profilschaltfläche:', $this->text_domain ) ?></th>
					<td>
						<code><strong>[dr_profile_btn text="<?php _e('Gehe zum Profil', $this->text_domain);?>" view="loggedin | loggedout | always"]</strong></code> or
						<br /><code><strong>[dr_profile_btn view="loggedin | loggedout | always"]&lt;img src="<?php _e('someimage.jpg', $this->text_domain); ?>" /&gt;<?php _e('Gehe zum Profil', $this->text_domain);?>[/dr_profile_btn]</strong></code>
						<br /><span class="description"><?php _e( 'Links zur Profilseite. Erzeugt einen &lt;button&gt; &lt;/button&gt; mit den von Dir definierten Inhalten.', $this->text_domain ) ?></span>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Anmelden-Schaltfläche:', $this->text_domain ) ?></th>
					<td>
						<code><strong>[dr_signin_btn text="<?php _e('Anmelden', $this->text_domain);?>" view="loggedin | loggedout | always"]</strong></code> or
						<br /><code><strong>[dr_signin_btn view="loggedin | loggedout | always"]&lt;img src="<?php _e('someimage.jpg', $this->text_domain); ?>" /&gt;<?php _e('Anmelden', $this->text_domain);?>[/dr_signin_btn]</strong></code>
						<br /><span class="description"><?php _e( 'Links zur Anmeldeseite. Erzeugt einen &lt;button&gt; &lt;/button&gt; mit den von Dir definierten Inhalten.', $this->text_domain ) ?></span>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Registrieren-Schaltfläche', $this->text_domain ) ?></th>
					<td>
						<code><strong>[dr_signup_btn text="<?php _e('Registrieren', $this->text_domain);?>" view="loggedin | loggedout | always"]</strong></code> or
						<br /><code><strong>[dr_signup_btn view="loggedin | loggedout | always"]&lt;img src="<?php _e('someimage.jpg', $this->text_domain); ?>" /&gt;<?php _e('Registrieren', $this->text_domain);?>[/dr_signup_btn]</strong></code>
						<br /><span class="description"><?php _e( 'Links zur Registrierung. Erzeugt einen &lt;button&gt; &lt;/button&gt; mit den von Dir definierten Inhalten.', $this->text_domain ) ?></span>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Abmelden-Schaltfläche:', $this->text_domain ) ?></th>
					<td>
						<code><strong>[dr_logout_btn text="<?php _e('Abmelden', $this->text_domain);?>"  view="loggedin | loggedout | always" redirect="http://someurl"]</strong></code> or
						<br /><code><strong>[dr_logout_btn  view="loggedin | loggedout | always" redirect="http://someurl"]&lt;img src="<?php _e('someimage.jpg', $this->text_domain); ?>" /&gt;<?php _e('Abmelden', $this->text_domain);?>[/dr_logout_btn]</strong></code>
						<br /><span class="description"><?php _e( 'Links to the Logout Page. Erzeugt einen &lt;button&gt; &lt;/button&gt; mit den von Dir definierten Inhalten. Das Attribut "redirect" ist die URL, zu der nach dem Abmelden weitergeleitet wird.', $this->text_domain ) ?></span>
					</td>
				</tr>
			</table>
		</div>
	</div>

</div>
