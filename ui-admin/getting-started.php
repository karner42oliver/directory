<?php if (!defined('ABSPATH')) die('Kein direkter Zugriff erlaubt!'); ?>

<div id="dr-plugin-setting" class="wrap">
	<h2><?php _e( 'Erste Schritte', $this->text_domain );?></h2>

	<div class="metabox-holder">

		<!-- Getting Started box -->
		<div class="postbox">
			<h3 class="hndle"><span><?php _e( 'Leitfaden für die ersten Schritte', $this->text_domain ); ?></span></h3>
			<div class="inside">
				<div class="note">
					<p><?php _e( 'Willkommen bei den Ersten  Schritten für das<b>Verzeichnis</b>.', $this->text_domain  ); ?></p>
				</div>
				<p><?php echo '' .
					'<p>' . __( 'Das Verzeichnis-Plugin verwandelt Deine WordPress-Installation von einer Blogging-Plattform in ein leistungsstarkes Online-Verzeichnis mit vielen Funktionen und integrierten Zahlungsgateways.', $this->text_domain ) . '</p>' .
					'<ul>' .
					'<li>' . __( 'Du kannst Deine Webseite kostenlos zur Verfügung stellen, um ein Business-Verzeichnis zu erstellen oder Geld dafür zu verlangen.', $this->text_domain ) . '</li>' .
					'</ul>' .
				''; ?></p>
				<ol class="dr-steps">
					<li>
						<?php if ( isset( $dr_tutorial['settings'] ) && 1 == $dr_tutorial['settings'] ) { ?>
						<span class="dr_del">
							<?php } ?>
							<?php _e( 'Zuerst musst Du Deine Einstellungen konfigurieren. Hier kannst Du die Zahlungsart, den Preis und andere Einstellungen festlegen.', $this->text_domain ); ?>
							<?php if ( isset( $dr_tutorial['settings'] ) && 1 == $dr_tutorial['settings'] ) { ?>
						</span>
						<?php } ?>
						<a href="admin.php?page=dr-get_started&amp;dr_intent=settings" class="button"><?php _e( 'Konfiguriere Deine Einstellungen', $this->text_domain ); ?></a>
					</li>
					<li>
						<?php if ( isset( $dr_tutorial['category'] ) && 1 == $dr_tutorial['category'] ) { ?>
						<span class="dr_del">
							<?php } ?>
							<?php _e( 'Erstelle als Nächstes neue Kategorien.', $this->text_domain ); ?>
							<?php if ( isset( $dr_tutorial['category'] ) && 1 == $dr_tutorial['category'] ) { ?>
						</span>
						<?php } ?>
						<a href="admin.php?page=dr-get_started&amp;dr_intent=category" class="button"><?php _e( 'Kategorien erstellen', $this->text_domain ); ?></a>
					</li>
					<li>
						<?php if ( isset( $dr_tutorial['listing'] ) && 1 == $dr_tutorial['listing'] ) { ?>
						<span class="dr_del">
							<?php } ?>
							<?php _e( 'Schließlich kannst Du Deine eigenen Einträge erstellen.', $this->text_domain ); ?>
							<?php if ( isset( $dr_tutorial['listing'] ) && 1 == $dr_tutorial['listing'] ) { ?>
						</span>
						<?php } ?>
						<a href="admin.php?page=dr-get_started&amp;dr_intent=listing" class="button"><?php _e( 'Einträge erstellen', $this->text_domain ); ?></a>
					</li>
				</ol>
			</div>
		</div>

		<?php if ( !defined( 'PSOURCE_REMOVE_BRANDING' ) || !constant( 'PSOURCE_REMOVE_BRANDING' ) ) { ?>
		<!-- More Help box -->
		<div class="postbox">
			<h3 class="hndle"><span><?php _e( 'Benötigst Du weitere Hilfe?', $this->text_domain ); ?></span></h3>
			<div class="inside">
				<ul>
					<li><a href="https://github.com/cp-psource/docs/verzeichnis-handbuch/" target="_blank"><?php _e( 'Plugin-Projektseite', $this->text_domain ); ?></a></li>
					<li><a href="https://github.com/cp-psource/docs/verzeichnis-handbuch/" target="_blank"><?php _e( 'Installations- und Anleitungsseite', $this->text_domain ); ?></a></li>
					<!--<li><a href="#" target="_blank"><?php _e( 'Videoanleitung', $this->text_domain ); ?></a></li>-->
					<li><a href="https://github.com/cp-psource/docs/verzeichnis-handbuch/" target="_blank"><?php _e( 'Hilfeforum', $this->text_domain ); ?></a></li>
				</ul>
			</div>
		</div>
		<?php } ?>
	</div>

</div>