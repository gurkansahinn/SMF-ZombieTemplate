<?php
/**
 * Simple Machines Forum (SMF)
 *
 * @package SMF
 * @author Simple Machines
 * @copyright 2011 Simple Machines
 * @license http://www.simplemachines.org/about/smf/license.php BSD
 *
 * @version 2.0
 */

/*	This template is, perhaps, the most important template in the theme. It
	contains the main template layer that displays the header and footer of
	the forum, namely with main_above and main_below. It also contains the
	menu sub template, which appropriately displays the menu; the init sub
	template, which is there to set the theme up; (init can be missing.) and
	the linktree sub template, which sorts out the link tree.

	The init sub template should load any data and set any hardcoded options.

	The main_above sub template is what is shown above the main content, and
	should contain anything that should be shown up there.

	The main_below sub template, conversely, is shown after the main content.
	It should probably contain the copyright statement and some other things.

	The linktree sub template should display the link tree, using the data
	in the $context['linktree'] variable.

	The menu sub template should display all the relevant buttons the user
	wants and or needs.

	For more information on the templating system, please see the site at:
	http://www.simplemachines.org/
*/

// Initialize the template... mainly little settings.
function template_init()
{
	global $context, $settings, $options, $txt;

	/* Use images from default theme when using templates from the default theme?
		if this is 'always', images from the default theme will be used.
		if this is 'defaults', images from the default theme will only be used with default templates.
		if this is 'never' or isn't set at all, images from the default theme will not be used. */
	$settings['use_default_images'] = 'never';

	/* What document type definition is being used? (for font size and other issues.)
		'xhtml' for an XHTML 1.0 document type definition.
		'html' for an HTML 4.01 document type definition. */
	$settings['doctype'] = 'xhtml';

	/* The version this template/theme is for.
		This should probably be the version of SMF it was created for. */
	$settings['theme_version'] = '2.0';
	
	/* Define the Theme variants. */
	$settings['theme_variants'] = array('style3', 'style2', 'style1');
	
	/* Set a setting that tells the theme that it can render the tabs. */
	$settings['use_tabs'] = true;

	/* Use plain buttons - as opposed to text buttons? */
	$settings['use_buttons'] = true;

	/* Show sticky and lock status separate from topic icons? */
	$settings['separate_sticky_lock'] = true;

	/* Does this theme use the strict doctype? */
	$settings['strict_doctype'] = false;

	/* Does this theme use post previews on the message index? */
	$settings['message_index_preview'] = false;

	/* Set the following variable to true if this theme requires the optional theme strings file to be loaded. */
	$settings['require_theme_strings'] = true;
}

// The main sub template above the content.
function template_html_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	// Show right to left and the character set for ease of translating.
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"', $context['right_to_left'] ? ' dir="rtl"' : '', '>
<head>';

	// The ?fin20 part of this link is just here to make sure browsers don't cache it wrongly.
	echo '	
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/index', $context['theme_variant'], '.css?fin20" />';

	// Some browsers need an extra stylesheet due to bugs/compatibility issues.
	foreach (array('ie7', 'ie6', 'webkit') as $cssfix)
		if ($context['browser']['is_' . $cssfix])
			echo '
	<link rel="stylesheet" type="text/css" href="', $settings['default_theme_url'], '/css/', $cssfix, '.css" />';

	// RTL languages require an additional stylesheet.
	if ($context['right_to_left'])
		echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/rtl.css" />';

	// Here comes the JavaScript bits!
	echo '
	<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/script.js?fin20"></script>
	<script type="text/javascript" src="', $settings['theme_url'], '/scripts/theme.js?fin20"></script>
	<script type="text/javascript"><!-- // --><![CDATA[
		var smf_theme_url = "', $settings['theme_url'], '";
		var smf_default_theme_url = "', $settings['default_theme_url'], '";
		var smf_images_url = "', $settings['images_url'], '";
		var smf_scripturl = "', $scripturl, '";
		var smf_iso_case_folding = ', $context['server']['iso_case_folding'] ? 'true' : 'false', ';
		var smf_charset = "', $context['character_set'], '";', $context['show_pm_popup'] ? '
		var fPmPopup = function ()
		{
			if (confirm("' . $txt['show_personal_messages'] . '"))
				window.open(smf_prepareScriptUrl(smf_scripturl) + "action=pm");
		}
		addLoadEvent(fPmPopup);' : '', '
		var ajax_notification_text = "', $txt['ajax_in_progress'], '";
		var ajax_notification_cancel_text = "', $txt['modify_cancel'], '";
	// ]]></script>';

	echo '
	<meta http-equiv="Content-Type" content="text/html; charset=', $context['character_set'], '" />
	<meta name="description" content="', $context['page_title_html_safe'], '" />', !empty($context['meta_keywords']) ? '
	<meta name="keywords" content="' . $context['meta_keywords'] . '" />' : '', '
	<title>', $context['page_title_html_safe'], '</title>';

	// Please don't index these Mr Robot.
	if (!empty($context['robot_no_index']))
		echo '
	<meta name="robots" content="noindex" />';

	// Present a canonical url for search engines to prevent duplicate content in their indices.
	if (!empty($context['canonical_url']))
		echo '
	<link rel="canonical" href="', $context['canonical_url'], '" />';

	// Show all the relative links, such as help, search, contents, and the like.
	echo '
	<link rel="help" href="', $scripturl, '?action=help" />
	<link rel="search" href="', $scripturl, '?action=search" />
	<link rel="contents" href="', $scripturl, '" />';

	// If RSS feeds are enabled, advertise the presence of one.
	if (!empty($modSettings['xmlnews_enable']) && (!empty($modSettings['allow_guestAccess']) || $context['user']['is_logged']))
		echo '
	<link rel="alternate" type="application/rss+xml" title="', $context['forum_name_html_safe'], ' - ', $txt['rss'], '" href="', $scripturl, '?type=rss;action=.xml" />';

	// If we're viewing a topic, these should be the previous and next topics, respectively.
	if (!empty($context['current_topic']))
		echo '
	<link rel="prev" href="', $scripturl, '?topic=', $context['current_topic'], '.0;prev_next=prev" />
	<link rel="next" href="', $scripturl, '?topic=', $context['current_topic'], '.0;prev_next=next" />';

	// If we're in a board, or a topic for that matter, the index will be the board's index.
	if (!empty($context['current_board']))
		echo '
	<link rel="index" href="', $scripturl, '?board=', $context['current_board'], '.0" />';

	// Output any remaining HTML headers. (from mods, maybe?)
	echo $context['html_headers'];

	echo '
</head>
<body>';
}

function template_body_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

echo '
<div id="wrapper">
	<div id="topbar">	
		<div id="quicknav">
			<ul>';

			if (!empty($settings['show_date_time']))
			echo '
				<li class="time">', $context['current_time'],'</li>';

			if(!empty($settings['quicknav_but1']))
			echo '
				<li><a href="', $settings['quicknav_url1'] , '">' , $settings['quicknav_but1'] , '</a></li>';

			if(!empty($settings['quicknav_but2']))
			echo '
				<li><a href="', $settings['quicknav_url2'] , '">' , $settings['quicknav_but2'] , '</a></li>';

			if(!empty($settings['quicknav_but3']))
			echo '
				<li><a href="', $settings['quicknav_url3'] , '">' , $settings['quicknav_but3'] , '</a></li>';

			if(!empty($settings['facebook_url']))
			echo '
				<li><a class="social_icon facebook" href="', $settings['facebook_url'] , '" target="_blank"></a></li>';

			if(!empty($settings['twitter_url']))
			echo '
				<li><a class="social_icon twitter" href="', $settings['twitter_url'] , '" target="_blank"></a></li>';

			if(!empty($settings['rss_url']))
			echo '
				<li><a class="social_icon rss" href="', $settings['rss_url'] , '" target="_blank"></a></li>';

		echo'
			</ul>
		</div>
		<div id="modnav">';

			// Are there any members waiting for approval?
			if (!empty($context['unapproved_members']))
			echo '
				<a href="', $scripturl, '?action=admin;area=viewmembers;sa=browse;type=approve">', $txt['approval_member'], ': <span>', $context['unapproved_members'] , '</span></a>';

			if (!empty($context['open_mod_reports']) && $context['show_open_reports'])
			echo '
				<a href="', $scripturl, '?action=moderate;area=reports">', $txt['open_reports'], ': <span>', $context['open_mod_reports'], '</span></a>';

	echo '
		</div>
	</div>
	<div id="header">
		<h1 id="logo">
			<a href="'.$scripturl.'">' . $context['forum_name'] . '</a>
		</h1>
	';
echo '		
	</div>
	<div id="ustbackground">
	<div id="toolbar">
		',template_menu(),'
	</div>
			<div id="user_tools">';

				echo '
				<div id="memb_login">';

				if ($context['user']['is_logged'])
				{
					if (!empty($context['user']['avatar']))
					echo '
						<p class="memb_avatar"><a href="', $scripturl, '?action=profile">', $context['user']['avatar']['image'], '</a></p>';

					echo '
						<p class="memb_greeting"><span>', $context['user']['name'], '</span></p>';
						
					echo '
						<ul>
						
						<strong><p class="first"><span>ID: ',$context['user']['id'] ,'</span></p></strong>';
						
						/*if ($context['allow_pm'])
						echo '
							<li><a class="first" href="', $scripturl, '?action=pm">', $txt['pm'], ' ', $context['user']['messages'], ' <strong>(', $context['user']['unread_messages'] , ')</strong></a></li>';
						*/
					echo '
						</ul>';
				}
				else
				{
					echo '
					<a href="https://zombielandrpg.com/forum/index.php?action=login"><img style="margin-right: 44px;" src="https://media.discordapp.net/attachments/690617080437014569/691738333675520020/girisyap.png"></a>
					<a href="https://zombielandrpg.com/forum/index.php?action=register"><img style="margin-right: 44px;" src="https://media.discordapp.net/attachments/690617080437014569/691738451141197884/kayitol.png"></a>
					';
				}
				

			echo '
				</div>
			</div>
			</div>
				<div id="main_content_inner">';

					// Show the navigation tree.
					theme_linktree();
}

function template_body_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

			echo '
			</div>';

		// Show the "Powered by" and "Valid" logos, as well as the copyright. Remember, the copyright must be somewhere!
		echo '
			<div id="amperFooter">
			<div id="yazi">
				<h2>
					© Zombieland Roleplay 2020
				</h2>
				Bu platformda oluşturulan her içeriğin sorumluluğu içeriğin üreticisine aittir. Oluşturulan her içerik hayal ürünüdür, gerçek kurum, şahıs veya toplulukları temel almaz.<br>', theme_copyright()  ,'<span class="smalltext" style="display: inline; visibility: visible; font-family: Verdana, Arial, sans-serif;"> coded by <a href="https://zombielandrpg.com/forum/index.php?action=profile;u=1" target="_blank">Amper</a></span>
				</div>
				<div id="discordWidgetFooter">
				<a href="https://discord.gg/7NsxS7S" target="_blank"><img src="https://discordapp.com/api/guilds/679602081451081759/widget.png?style=banner2&amp;version=2" alt=""></a>
				</div>
			</div>
		</div>
	</div>
</div>';
}

function template_html_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
</body></html>';
}

// Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
function theme_linktree($force_show = false)
{
	global $context, $settings, $options, $shown_linktree;

	// If linktree is empty, just return - also allow an override.
	if (empty($context['linktree']) || (!empty($context['dont_default_linktree']) && !$force_show))
		return;

		if ($context['user']['is_logged'])
		{
	echo '
	<div class="navigate_section">
		<ul>';

	// Each tree item has a URL and name. Some may have extra_before and extra_after.
	foreach ($context['linktree'] as $link_num => $tree)
	{
		if ($context['user']['is_logged'])
		{
		echo '
			<li', ($link_num == count($context['linktree']) - 1) ? ' class="last"' : '', '>';
		}

		// Show something before the link?
		if (isset($tree['extra_before']))
			echo $tree['extra_before'];

		// Show the link, including a URL if it should have one.
		if ($context['user']['is_logged'])
		{
		echo $settings['linktree_link'] && isset($tree['url']) ? '
				<a href="' . $tree['url'] . '"><span>' . $tree['name'] . '</span></a>' : '<span>' . $tree['name'] . '</span>';
		}
		// Show something after the link...?
		if (isset($tree['extra_after']))
			echo $tree['extra_after'];

		// Don't show a separator for the last one.
		if ($link_num != count($context['linktree']) - 1)
			echo ' &#187;';

		echo '
			</li>';
	}
	echo '
		</ul>
	</div>';

	$shown_linktree = true;
	}
}

// Show the menu up top. Something like [home] [help] [profile] [logout]...
function template_menu()
{
	global $context, $settings, $options, $scripturl, $txt;

	echo '
		<ul id="topnav">';

	foreach ($context['menu_buttons'] as $act => $button)
	{
		echo '
				<li id="button_', $act, '">
					<a class="', $button['active_button'] ? 'active ' : '', '" href="', $button['href'], '"', isset($button['target']) ? ' target="' . $button['target'] . '"' : '', '>
						<span class="', isset($button['is_last']) ? 'last ' : '', 'firstlevel">', $button['title'], '</span>
					</a>';
		if (!empty($button['sub_buttons']))
		{
			echo '
					<ul>';

			foreach ($button['sub_buttons'] as $childbutton)
			{
				echo '
						<li>
							<a href="', $childbutton['href'], '"', isset($childbutton['target']) ? ' target="' . $childbutton['target'] . '"' : '', '>
								<span', isset($childbutton['is_last']) ? ' class="last"' : '', '>', $childbutton['title'], !empty($childbutton['sub_buttons']) ? '...' : '', '</span>
							</a>';
				// 3rd level menus :)
				if (!empty($childbutton['sub_buttons']))
				{
					echo '
							<ul>';

					foreach ($childbutton['sub_buttons'] as $grandchildbutton)
						echo '
								<li>
									<a href="', $grandchildbutton['href'], '"', isset($grandchildbutton['target']) ? ' target="' . $grandchildbutton['target'] . '"' : '', '>
										<span', isset($grandchildbutton['is_last']) ? ' class="last"' : '', '>', $grandchildbutton['title'], '</span>
									</a>
								</li>';

					echo '
							</ul>';
				}

				echo '
						</li>';
			}
				echo '
					</ul>';
		}
		echo '
				</li>';
	}

	echo '
			</ul>';
			echo '
			<div id="search">
			<form action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '">
				<input class="search_input" type="text" name="search" value="', $txt['forum_search'], '" onfocus="this.value = \'\';" onblur="if(this.value==\'\') this.value=\'', $txt['forum_search'], '\';" />';
	
				// Search within current topic?
				if (!empty($context['current_topic']))
				echo '
					<input type="hidden" name="topic" value="', $context['current_topic'], '" />';
	
				// If we're on a certain board, limit it to this board ;).
				elseif (!empty($context['current_board']))
				echo '
					<input type="hidden" name="brd[', $context['current_board'], ']" value="', $context['current_board'], '" />';
	
		echo '
			</form>
		</div>
	 ';
}

// Generate a strip of buttons.
function template_button_strip($button_strip, $direction = 'top', $strip_options = array())
{
	global $settings, $context, $txt, $scripturl;

	if (!is_array($strip_options))
		$strip_options = array();

	// List the buttons in reverse order for RTL languages.
	if ($context['right_to_left'])
		$button_strip = array_reverse($button_strip, true);

	// Create the buttons...
	$buttons = array();
	foreach ($button_strip as $key => $value)
	{
		if (!isset($value['test']) || !empty($context[$value['test']]))
			$buttons[] = '
				<li><a' . (isset($value['id']) ? ' id="button_strip_' . $value['id'] . '"' : '') . ' class="button_strip_' . $key . (isset($value['active']) ? ' active' : '') . '" href="' . $value['url'] . '"' . (isset($value['custom']) ? ' ' . $value['custom'] : '') . '><span>' . $txt[$value['text']] . '</span></a></li>';
	}

	// No buttons? No button strip either.
	if (empty($buttons))
		return;

	// Make the last one, as easy as possible.
	$buttons[count($buttons) - 1] = str_replace('<span>', '<span class="last">', $buttons[count($buttons) - 1]);

	echo '
		<div class="buttonlist', !empty($direction) ? ' float' . $direction : '', '"', (empty($buttons) ? ' style="display: none;"' : ''), (!empty($strip_options['id']) ? ' id="' . $strip_options['id'] . '"': ''), '>
			<ul>',
				implode('', $buttons), '
			</ul>
		</div>';
}

?>