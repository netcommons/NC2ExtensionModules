<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * iframe内動画再生アクションクラス
 *
 * @package     NetCommons
 * @author      Noriko Arai,Ryuji Masukawa
 * @copyright   2006-2007 NetCommons Project
 * @license     http://www.netcommons.org/license.txt  NetCommons License
 * @project     NetCommons Project, supported by National Institute of Informatics
 * @access      public
 */
class Multimedia_View_Main_Item_Iframe extends Action
{
	// リクエストパラメータを受け取るため
	var $item_id = null;
	var $type = null;

    // 使用コンポーネントを受け取るため
    var $db = null;
	var $session = null;
    var $multimediaView = null;
    var $filterChain = null;

	function execute()
	{
		//初期化処理
		$item = null;
		$errStr = null;
		$base_url = null;
		$index_file_name = null;
		$item_path = null;
		$multimedia_movie_player_width = null;
		$multimedia_movie_player_height = null;
		$css_version = null;
		$core_base_url = null;
		$ua = null;
		$flash_use_only = false;
		$auto_start = true;
		$play_mode = null;
		$css = null;
		$play_action = null;
		$xml_action = null;

		//動画情報取得
		$item = $this->multimediaView->getItem($this->item_id);

		$smartyAssign =& $this->filterChain->getFilterByName('SmartyAssign');

		//動画情報が存在しない場合
		if(empty($item)) {
			//エラーメッセージを格納
			$errStr = $smartyAssign->getLang('multimedia_err_no_item');
		}
		//動画情報が存在する場合
		else {
			//動画が公開領域で閲覧可能ではない場合
			if($item['privacy'] > _AUTH_GUEST) {
				//ユーザIDを取得出来ない場合は閲覧不可
				$user_id = $this->session->getParameter("_user_id");
				if(empty($user_id)) {
					//エラーメッセージを格納
					$errStr = $smartyAssign->getLang('multimedia_err_incorrectauth');
				}
			}

			//ユーザエージェント判定
			$ua = $_SERVER['HTTP_USER_AGENT'];
			switch (true) {
				//IEの場合はFlashでのみ再生
				case (preg_match('/MSIE [6-9]/', $ua)):
					$flash_use_only = true;
					break;

				//Tridentの場合はFlashでのみ再生
				case (preg_match('/Trident/', $ua)):
					$flash_use_only = true;
					break;

				//Safari判定
				case (preg_match('/Safari/', $ua)):
					//Macの場合はFlashでのみ再生
					if(preg_match('/Macintosh;/', $ua)) {
						$flash_use_only = true;
						break;
					}
					//Android標準ブラウザの場合は自動再生させない
					if((preg_match('/Android/', $ua)) && !(preg_match('/Chrome/', $ua))) {
						$auto_start = false;
						break;
					}
			}

			//埋め込みかどうかをパラメタから判断し、各値を設定
			//パラメタがないか、通常再生(iframe)の場合
			if(empty($this->type) || $this->type != 'embeded') {
				//自動再生判定
				if($auto_start) {
					$play_mode = '.jPlayer("play")';
				} else {
					$play_mode = '';
				}
				$css = 'jp-video-normal';
				$multimedia_movie_player_width = MULTIMEDIA_MOVIE_PLAYER_WIDTH;
				$multimedia_movie_player_height = MULTIMEDIA_MOVIE_PLAYER_HEIGHT;
			}
			//埋め込み再生の場合
			else {
				$play_mode = '';
				$css = 'jp-video-embeded';
				$multimedia_movie_player_width = MULTIMEDIA_MOVIE_PLAYER_EMBEDED_WIDTH;
				$multimedia_movie_player_height = MULTIMEDIA_MOVIE_PLAYER_EMBEDED_HEIGHT;
			}

			//jPlayerで再生する場合はborder分を減算
			if(! $flash_use_only) {
				$multimedia_movie_player_width -= 2;
				$multimedia_movie_player_height -= 2;
			} else {
				//エラーが発生している場合はborder分を減算
				if($errStr !== null) {
					$multimedia_movie_player_width -= 2;
					$multimedia_movie_player_height -= 2;
				}
			}

			// 各値を設定
			$base_url = BASE_URL;
			$index_file_name = INDEX_FILE_NAME;
			$item_path = $item['item_path'];
			$css_version = _CSS_VERSION;
			$core_base_url = CORE_BASE_URL;
			$play_action = 'multimedia_view_main_play';
			$xml_action = 'multimedia_view_main_xml';
		}

		//閲覧可能である場合
		if($errStr === null) {
			//Flashのみで対応する場合
			if($flash_use_only) {
print <<< EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="content-language" content="ja" />
<meta name="robots" content="noindex,nofollow" />
<title>{$item['item_name']}</title>
<link href="{$base_url}{$index_file_name}?action=common_download_css&dir_name=/multimedia/default/style.css&vs={$css_version}" rel="stylesheet" type="text/css" />
</head>
<body style="margin: 0px; padding: 0px;">
<object width="{$multimedia_movie_player_width}px" height="{$multimedia_movie_player_height}px">
	<param name="movie" value="{$base_url}{$index_file_name}?action=<{$play_action}"></param>
	<param name="allowFullScreen" value="true"></param>
	<param name="allowscriptaccess" value="always"></param>
	<param name="flashvars" value="file={$base_url}{$index_file_name}?action={$xml_action}&id={$item['item_id']}&autostart=true"></param>
	<embed src="{$base_url}{$index_file_name}?action={$play_action}" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" flashvars="file={$base_url}{$index_file_name}?action={$xml_action}&id={$item['item_id']}&autostart=true" width="{$multimedia_movie_player_width}px" height="{$multimedia_movie_player_height}px"></embed>
</object>
</body>
</html>
EOF;
			}
			//jPlayerで再生する場合
			else {
print <<< EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="content-language" content="ja" />
<meta name="robots" content="noindex,nofollow" />
<title>{$item['item_name']}</title>
<link href="{$base_url}{$index_file_name}?action=common_download_css&dir_name=/multimedia/default/style.css&vs={$css_version}" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="{$base_url}/images/multimedia/jquery-1.8.3.min.js"></script>
<script type="text/javascript">
	jQuery.noConflict();
</script>
<script type="text/javascript" src="{$base_url}/images/multimedia/jquery.jplayer.js"></script>
<script class="nc_script" type="text/javascript">
//<![CDATA[
jQuery(document).ready(function(){
	jQuery("#jquery_jplayer_1").jPlayer({
		solution:"flash, html",
		ready: function () {
			jQuery(this).jPlayer("setMedia", {
				m4v: "{$base_url}{$index_file_name}{$item_path}",
				poster: "{$base_url}{$index_file_name}?action=multimedia_view_main_item_thumbnail&item_id={$item['item_id']}"
			}){$play_mode};
		},
		swfPath: "{$core_base_url}/images/multimedia",
		supplied: "m4v",
		autohide: {
			restored: true,
			full: true,
			hold: 2000
		},
		errorAlerts: true,
		warningAlerts: true,
		size: {
			width: "{$multimedia_movie_player_width}px",
			height: "{$multimedia_movie_player_height}px",
			cssClass: "{$css}"
		}
	});
});
//]]>
</script>
</head>
<body style="margin: 0px; padding: 0px;">
<div id="jp_container_1" class="jp-video {$css}">
	<div class="jp-type-single">
		<div id="jquery_jplayer_1" class="jp-jplayer"></div>
		<div class="jp-video-play">
			<a href="javascript:;" class="jp-video-play-icon" tabindex="1">play</a>
		</div>
		<div class="jp-gui">
			<div class="jp-interface">
				<div class="jp-progress">
					<div class="jp-seek-bar">
						<div class="jp-play-bar"></div>
					</div>
				</div>
				<div class="jp-controls-holder">
					<ul class="jp-controls-left">
						<li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li>
						<li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li>
						<li><div class="jp-current-time"></div></li>
						<li><a href="javascript:;" class="jp-stop" tabindex="1">stop</a></li>
						<li><a href="javascript:;" class="jp-repeat" tabindex="1">stop</a></li>
						<li><a href="javascript:;" class="jp-repeat-off" tabindex="1">stop</a></li>
					</ul>
					<ul class="jp-controls-right">
						<li><a href="javascript:;" class="jp-full-screen" tabindex="1" title="full screen">full screen</a></li>
						<li><a href="javascript:;" class="jp-restore-screen" tabindex="1" title="restore screen">restore screen</a></li>
						<li><div class="jp-duration"></div></li>
						<li><a href="javascript:;" class="jp-volume-max" tabindex="1" title="max volume">max volume</a></li>
						<li>
							<div class="jp-volume-bar">
								<div class="jp-volume-bar-value"></div>
							</div>
						</li>
						<li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a></li>
						<li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li>
					</ul>
				</div>
			</div>
		</div>
		<div class="jp-no-solution">
			<span>Update Required</span>
			To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
		</div>
	</div>
</div>
</body>
</html>
EOF;
			}
		}
		//閲覧できない場合
		else {
			//動画情報が存在しない場合
			if(empty($item)) {
print <<< EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="content-language" content="ja" />
<meta name="robots" content="noindex,nofollow" />
<link href="{$base_url}{$index_file_name}?action=common_download_css&dir_name=/multimedia/default/style.css&vs={$css_version}" rel="stylesheet" type="text/css" />
<title>{$errStr}</title>
</head>
<body style="background-color:#000;">
	<div class="multimedia_player_error_detail">
		{$errStr}
	</div>
</body>
</html>
EOF;
			}
			//閲覧権限がない場合（サムネイル画像を出力）
			else {
print <<< EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="content-language" content="ja" />
<meta name="robots" content="noindex,nofollow" />
<link href="{$base_url}{$index_file_name}?action=common_download_css&dir_name=/multimedia/default/style.css&vs={$css_version}" rel="stylesheet" type="text/css" />
<title>{$errStr}</title>
</head>
<body>
	<div class="multimedia_player_background" style="width:{$multimedia_movie_player_width}px; height:{$multimedia_movie_player_height}px">
		<img src="{$base_url}{$index_file_name}?action=multimedia_view_main_item_thumbnail&item_id={$item['item_id']}" class="multimedia_player_thumbnail" />
		<div class="multimedia_player_error_block" style="width:{$multimedia_movie_player_width}px;">
			<div class="multimedia_player_error_detail">
				{$errStr}
			</div>
		</div>
	</div>
</body>
</html>
EOF;
			}

		}

		exit;

	}
}
?>