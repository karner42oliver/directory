<!-- start messages -->
<?php if ( is_category() ): ?>
	<h3><?php _e("Leider können wir die gesuchte Kategorie unter dieser URL nicht finden. Bitte versuche, einen Menüpunkt oben oder neben dieser Nachricht auszuwählen, um dorthin zu gelangen, wo Du hin möchtest.", THEME_TEXT_DOMAIN); ?></h3>
<?php elseif ( is_archive() ): ?>
	<h3><?php _e("Leider können wir das gesuchte Archiv unter dieser URL nicht finden. Bitte versuche, einen Menüpunkt oben oder neben dieser Nachricht auszuwählen, um dorthin zu gelangen, wo Du hin möchtest.", THEME_TEXT_DOMAIN); ?></h3>
<?php elseif ( is_search() ): ?>
	<h3><?php _e("Leider können wir den gesuchten Suchbegriff nicht finden. Bitte versuche, einen Menüpunkt oben oder neben dieser Nachricht auszuwählen, um dorthin zu gelangen, wo Du hin möchtest.", THEME_TEXT_DOMAIN); ?></h3>
<?php elseif ( is_author() ): ?>
	<h3><?php _e("Leider können wir den gesuchten Autor unter dieser URL nicht finden. Bitte versuche, einen Menüpunkt oben oder neben dieser Nachricht auszuwählen, um dorthin zu gelangen, wo Du hin möchtest.", THEME_TEXT_DOMAIN); ?></h3>
<?php elseif ( is_single() ): ?>
	<h3><?php _e("Leider können wir den gesuchten Beitrag unter dieser URL nicht finden. Bitte versuche, einen Menüpunkt oben oder neben dieser Nachricht auszuwählen, um dorthin zu gelangen, wo Du hin möchtest.", THEME_TEXT_DOMAIN); ?></h3>
<?php elseif ( is_home() ): ?>
	<h3><?php _e("Leider können wir den gesuchten Inhalt unter dieser URL nicht finden. Bitte versuche, einen Menüpunkt oben oder neben dieser Nachricht auszuwählen, um dorthin zu gelangen, wo Du hin möchtest.", THEME_TEXT_DOMAIN); ?></h3>
<?php elseif ( is_404() ): ?>
	<h3><?php _e("Leider können wir den gesuchten Inhalt unter dieser URL nicht finden. Bitte versuche, einen Menüpunkt oben oder neben dieser Nachricht auszuwählen, um dorthin zu gelangen, wo Du hin möchtest.", THEME_TEXT_DOMAIN); ?></h3>
<?php endif; ?>
<!-- end messages -->
