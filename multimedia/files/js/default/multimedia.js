var clsMultimedia = Class.create();
var multimediaCls = Array();

clsMultimedia.prototype = {
	initialize: function(id) {
		this.id = id;
		this.multimedia_id = null;
		this.album_id = null;
		this.album_edit_flag = null;
		this.item_id = null;
		this.dragDrop = null;
	},
  	initializeMultimedia: function() {
		var post = {
			"action":"multimedia_action_edit_initialize"
		}
		commonCls.sendPost(this.id, post);
	},
	editMultimedia: function(form_el) {
		commonCls.sendPost(this.id, "action=multimedia_action_edit_entry" + "&" + Form.serialize(form_el), {"target_el":$(this.id)});
	},
	editDisplay: function(form_el) {
		commonCls.sendPost(this.id, "action=multimedia_action_edit_display" + "&" + Form.serialize(form_el), {"target_el":$(this.id)});
	},
	showAlbumEntry: function(event, multimedia_id, album_id) {
		var params = new Object();
		params["prefix_id_name"] = "popup_multimedia_album_entry";
		params["action"] = "multimedia_view_main_album_entry";
		params["multimedia_id"] = multimedia_id;
		params["album_id"] = album_id;
		
		var optionParams = new Object();
		var top_el = $(this.id);
		optionParams["top_el"] = top_el;
		optionParams["modal_flag"] = true;
		optionParams["target_el"] = top_el;
		
		commonCls.sendPopupView(event, params, optionParams);
	},
	showAlbumEdit: function() {
		var params = new Object();
		params["action"] = "multimedia_view_main_init";
		params["multimedia_id"] = this.multimedia_id;
		params["album_edit_flag"] = this.album_edit_flag = 1;
		commonCls.sendView(this.id, params);
	},
	showAlbumList: function(param) {
		var params = new Object();
		params["action"] = "multimedia_view_main_init";
		params["multimedia_id"] = this.multimedia_id;
		Object.extend(params, param);

		commonCls.sendView(this.id, params);
	},
	deleteAlbum: function(album_id, confirmMessage) {
		if (!confirm(confirmMessage)) return false;
		var post = {
			"action":"multimedia_action_main_album_delete",
			"album_id":album_id
		};		
		
		var params = new Object();
		params["callbackfunc"] = function(res){
			if(this.album_edit_flag == 1) {
				commonCls.sendView(this.id, {action:'multimedia_view_main_init', album_edit_flag:1});
			}else {
				commonCls.sendView(this.id, {action:'multimedia_view_main_init', album_edit_flag:0});
			}
		}.bind(this);
		commonCls.sendPost(this.id, post, params);
	},
	showItem: function(event, item_id) {
		var play_count_el = $("multimedia_play_count_" + item_id + this.id);
		var play_count = parseInt(play_count_el.innerHTML.match(/\d+/)[0]) + 1;
		play_count_el.innerHTML = play_count_el.innerHTML.replace(/\d+/, play_count);
		
		var params = new Object();
		params = {
			"action":"multimedia_view_main_item_detail",
			"item_id":item_id,
			"prefix_id_name":"popup_multimedia_item_detail"
		};
		
		var popupParams = new Object();
		var top_el = $(this.id);
		popupParams["top_el"] = top_el;
		popupParams['modal_flag'] = true;
		popupParams['target_el'] = top_el;
		
		commonCls.sendPopupView(event, params, popupParams);
	},
	showItemUpload: function(event, album_id) {
		var params = new Object();
		params = {
			"action":"multimedia_view_main_item_upload",
			"album_id":album_id,
			"prefix_id_name":"multimedia_item_upload"
		};
		
		var popupParams = new Object();
		var top_el = $(this.id);
		popupParams['top_el'] = top_el;
		popupParams['target_el'] = top_el;
		popupParams['modal_flag'] = true;

		commonCls.sendPopupView(event, params, popupParams);
	},
	upload: function(form_el) {
		var upload_el = $('multimedia_item_upload_area' + this.id);
		commonCls.displayNone(upload_el);
		var loading_el = $('multimedia_item_upload_loading' + this.id);
		commonCls.displayVisible(loading_el);
		var contrl_el = $('multimedia_item_upload_control' + this.id);
		commonCls.displayNone(contrl_el);
		var cancel_el = $('multimedia_item_upload_cancel' + this.id);
		commonCls.displayVisible(cancel_el);
		var params = new Object();
		params["param"] = {"action":"multimedia_action_main_item_upload"};
		params["top_el"] = $(this.id);
		params["method"] = "post";
		params["form_prefix"] = "multimedia_attachment";
		params["timeout_flag"] = false;
		params["callbackfunc"] = function(files, res) {
			if(this.id.indexOf("_multimedia_item_upload")<0) {
				commonCls.sendView(this.id, {'action':'multimedia_view_main_init'});
			}else {
				var refreshElement_id = this.id.replace("_multimedia_item_upload", "");
				if($(refreshElement_id)) {
					commonCls.sendRefresh(refreshElement_id);
				}
			}
			commonCls.removeBlock(this.id);
		}.bind(this);
		params["callbackfunc_error"] = function(file, res){
			commonCls.alert(res);
			commonCls.displayNone(cancel_el);
			commonCls.displayNone(loading_el);
			commonCls.displayVisible(upload_el);
			commonCls.displayVisible(contrl_el);
		}.bind(this);
		commonCls.sendAttachment(params);
	},
	showItemEdit: function(event, item_id) {
		var params = new Object();
		params = {
			"action":"multimedia_view_main_item_edit",
			"item_id":item_id,
			"prefix_id_name":"popup_multimedia_item_edit"
		};
		
		var popupParams = new Object();
		var top_el = $(this.id);
		popupParams["top_el"] = top_el;
		popupParams["modal_flag"] = true;
		popupParams["target_el"] = top_el;
		
		commonCls.sendPopupView(event, params, popupParams);
	},
	selectJacket: function(src, album_id) {
		var form = $("multimedia_album_form" + this.id);
		form["album_jacket"].value = src.substring(src.lastIndexOf("/") + 1, src.length);
		form["upload_id"].value = "";
		
		var params = new Object();
		params["param"] = {
			"action":"multimedia_view_main_album_jacket",
			"multimedia_id":this.multimedia_id,
			"album_id":album_id,
			"album_jacket":form["album_jacket"].value
		};
		params["top_el"] = $(this.id);
		params["target_el"] = $("multimedia_album_jacket" + this.id).parentNode;
		
		commonCls.send(params);
	},
	uploadJacket: function() {
		var form = $("multimedia_album_form" + this.id);
		
		var params = new Object();
		params["param"] = {
			"action":"multimedia_action_main_album_jacket",
			"multimedia_id":form["multimedia_id"],
			"album_id":form["album_id"]
		};
		
		params["top_el"] = $(this.id);
		params['form_prefix'] = "multimedia_jacket";
		
		params["callbackfunc"] = function(files, res){
			form["album_jacket"].value = "?action="+ files[0]['action_name'] + "&upload_id=" + files[0]['upload_id'];
			form["upload_id"].value = files[0]["upload_id"];

			var params = new Object();
			params["param"] = {
				"action":"multimedia_view_main_album_jacket",
				"multimedia_id":this.multimedia_id,
				"album_id":this.album_id,
				"upload_id":form["upload_id"].value,
				"album_jacket":form["album_jacket"].value
			};
			
			params["top_el"] = $(this.id);
			params["target_el"] = $("multimedia_album_jacket" + this.id).parentNode;
			
			commonCls.send(params);
		}.bind(this);

		commonCls.sendAttachment(params);
	},
	editItem: function(form_el) {
		var params = new Object();
		params["callbackfunc"] = function(res){
			commonCls.removeBlock(this.id);
			commonCls.sendRefresh(this.id.replace("_popup_multimedia_item_edit", ""));
		}.bind(this);
		commonCls.sendPost(this.id, "action=multimedia_action_main_item_edit" + "&" + Form.serialize(form_el), params);
	},
	deleteItem: function(confirmMessage, item_id) {
		if (!confirm(confirmMessage)) return false;
		var post = {
			"action":"multimedia_action_main_item_delete",
			"item_id":item_id
		};
		var params = new Object();
		params["callbackfunc"] = function(res){
			commonCls.sendRefresh(this.id);
		}.bind(this);
		
		commonCls.sendPost(this.id, post, params);
	},
	enterAlbum: function() {
		var post = "action=multimedia_action_main_album_entry&" + Form.serialize($("multimedia_album_form" + this.id));
		var params = new Object();
		params["callbackfunc"] = function(res){
			var id = this.id.replace("_popup_multimedia_album_entry", "");
			commonCls.removeBlock(this.id);
			if(multimediaCls[id].album_edit_flag == 1) {
				commonCls.sendView(id, {action:'multimedia_view_main_init', album_edit_flag:1});
			}else {
				commonCls.sendView(id, {action:'multimedia_view_main_init', album_edit_flag:0});
			}
		}.bind(this);
		
		commonCls.sendPost(this.id, post, params);
	},
	showAlbumSeq: function(event, multimedia_id) {
		var param_popup = new Object();
		param_popup = {
			"action":"multimedia_view_main_album_sequence",
			"multimedia_id":multimedia_id,
			"prefix_id_name":"multimedia_album_sequence_popup"
		};
		
		var params = new Object();
		params['top_el'] = $(this.id);
		params['modal_flag'] = true;
		commonCls.sendPopupView(event, param_popup, params);
	},
	closeAlbumSeq: function() {
		var id = this.id.replace("_multimedia_album_sequence_popup", "");
		commonCls.removeBlock(this.id);
		if(multimediaCls[id].album_edit_flag == 1) {
			commonCls.sendView(id, {action:'multimedia_view_main_init', album_edit_flag:1});
		}else {
			commonCls.sendView(id, {action:'multimedia_view_main_init', album_edit_flag:0});
		}
	},
	vote: function(album_id, item_id) {
		var post = {
			"action":"multimedia_action_main_item_vote",
			"album_id":album_id,
			"item_id":item_id
		};
		var params = new Object();
		params["callbackfunc"] = function(res) {
			var id = this.id.replace("_popup_multimedia_item_detail", "");
			var top_vote_count_el = $("multimedia_vote_count_" + item_id + id);
			var detail_vote_count_el = $("multimedia_vote_count" + this.id);
			var vote_count = parseInt(top_vote_count_el.innerHTML.match(/\d+/)[0]) + 1;
			top_vote_count_el.innerHTML = top_vote_count_el.innerHTML.replace(/\d+/, vote_count);
			detail_vote_count_el.innerHTML = detail_vote_count_el.innerHTML.replace(/\d+/, vote_count);
			var vote_link_el = $("multimedia_vote_link" + this.id);
			vote_link_el.innerHTML = "";
		}.bind(this);
		commonCls.sendPost(this.id, post, params);
	},
	showComment: function(album_id, item_id) {
		var params = new Object();
		params["param"] = {
			"action":"multimedia_view_main_comment",
			"item_id":item_id
		};
		params["top_el"] = $(this.id);
		params["target_el"] = $("multimedia_comment_area" + this.id);
		
		commonCls.send(params);
	},
	enterComment: function(album_id, item_id) {
		var params = new Object();
		params["target_el"] = $("multimedia_comment_area" + this.id);
		params["callbackfunc"] = function(res) {
			var count_el = $("multimedia_comment_count" + this.id);
			var count = parseInt(count_el.innerHTML.match(/\d+/)[0]) + 1;
			count_el.innerHTML = count_el.innerHTML.replace(/\d+/, count);
		}.bind(this);
		commonCls.sendPost(this.id, Form.serialize($("multimedia_comment_form" + this.id)), params);
	},
	cancelComment: function() {
		commonCls.displayNone($("multimedia_comment_area" + this.id));
		commonCls.focus($("_href" + this.id));
	},
	showCommentEntry: function(comment_id) {
		var commentForm = $("multimedia_comment_form" + this.id);
		commentForm["comment_value"].value = $("multimedia_comment_value" + comment_id + this.id).innerHTML.replace(/\n/ig,"").replace(/(<br(?:.|\s|\/)*?>)/ig,"\n").unescapeHTML();
		commentForm["comment_id"].value = comment_id;

		commentForm["comment_value"].focus();
		commentForm["comment_value"].select();
	},
	deleteComment: function(comment_id, item_id, album_id, confirmMessage) {
		if (!confirm(confirmMessage)) return false;
		
		var post = {
			"action":"multimedia_action_main_comment_delete",
			"album_id":album_id,
			"item_id":item_id,
			"comment_id":comment_id
		};
					
		var params = new Object();
		params["target_el"] = $("multimedia_comment_area" + this.id);
		params["callbackfunc"] = function(res){
			this.showFooter(album_id, item_id);
		}.bind(this);

		commonCls.sendPost(this.id, post, params);
	},
	itemMouseOver: function(div) {
		if (this.dragDrop && !this.dragDrop.hasSelection()) {
			Element.addClassName(div, "multimedia_item_over");
		}
	},
	itemMouseOut: function(div) {
 		if (this.dragDrop && !this.dragDrop.hasSelection()) {
 			Element.removeClassName(div, 'multimedia_item_over');
 		}
  	},
	setItem: function(type, data) {
		var top_el = $(this.id);
		var obj_el = $('multimedia_toolbar_' + type + '_text' + this.id);
		var params = new Object();
		switch(type) {
			case "list":
				obj_el = $('multimedia_toolbar_' + data + '_text' + this.id);
				params["target_el"] = top_el;
				params["param"] = {"action":"multimedia_view_main_init","view_type":data,"now_page":1};
				break;
			case "album":
				params["target_el"] = top_el;
				params["param"] = {"action":"multimedia_view_main_init","album":data,"now_page":1};
				break;
			case "date":
				params["target_el"] = top_el;
				params["param"] = {"action":"multimedia_view_main_init","date":data,"now_page":1};
				break;
			case "sort":
				params["target_el"] = top_el;
				params["param"] = {"action":"multimedia_view_main_init","sort":data,"now_page":1};
				break;
			case "search":
				params["method"] = "post";
				params["target_el"] = $("multimedia_top_main" + this.id);
				var form_el = $("multimedia_search_form" + this.id);
				form_el.sort.value = data;
				params["param"] = "action=multimedia_action_main_search&now_page=1&"+Form.serialize(form_el);
				break;
		}
		params["top_el"] = top_el;
		params["loading_el"] = obj_el;
		commonCls.send(params);
	},
  	toPage: function(el, now_page, type) {
		var top_el = $(this.id);
		var params = new Object();
		switch(type) {
			case "search":
				params["method"] = "post";
				params["target_el"] = $("multimedia_top_main" + this.id);
				params["param"] = "action=multimedia_action_main_search&now_page="+ now_page +"&" +Form.serialize($("multimedia_search_form" + this.id));
				break;
			case "item":
				params["target_el"] = top_el;
				params["param"] = "action=multimedia_view_main_item_init&now_page="+ now_page +"&" +Form.serialize($("multimedia_item_init_form" + this.id));
				break;
			case "comment":
				params["target_el"] = $("multimedia_comment_area" + this.id);
				params["param"] = "action=multimedia_view_main_comment&now_page="+ now_page +"&item_id=" + this.item_id;
				break;
			default:
				params["target_el"] = top_el;
				params["param"] = "action=multimedia_view_main_init&now_page="+ now_page;
				break;
		}
		params["top_el"] = top_el;
		params["loading_el"] = el;
		commonCls.send(params);
	},
	search : function(el, type, condition) {
		condition = condition.unescapeHTML();
        var params = new Object();
		var id = "";
		if(this.id.indexOf("_popup_multimedia_item_detail") < 0) {
			id = this.id;
		}else {
			id = this.id.replace("_popup_multimedia_item_detail", "");
            params["callbackfunc"] = function(res) {
               commonCls.removeBlock(this.id);
            }.bind(this);
		}
		var form_el = $("multimedia_search_form" + id);
		params["method"] = "post";
		params["param"] = "action=multimedia_action_main_search&";
		params["loading_el"] = el;
		params["target_el"] = $("multimedia_top_main" + id);
		switch(type) {
			case "user":
				form_el.keyword.value = condition;
				form_el.poster_check.checked = true;
				form_el.tag_check.checked = false;
				form_el.name_check.checked = false;
				form_el.description_check.checked = false;
				break;
			case "tag":
				form_el.keyword.value = condition;
				form_el.poster_check.checked = false;
				form_el.tag_check.checked = true;
				form_el.name_check.checked = false;
				form_el.description_check.checked = false;
				break;
			case "channel":
				form_el.keyword.value = "";
				form_el.poster_check.checked = true;
				form_el.tag_check.checked = true;
				form_el.name_check.checked = true;
				form_el.description_check.checked = true;
				form_el.search_album.value = condition;
				break;
		}
		params["param"] += Form.serialize(form_el);
		commonCls.send(params);
  	}
}