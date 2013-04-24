var clsBlogparts = Class.create();
var blogpartsCls = Array();

clsBlogparts.prototype = {
	initialize: function(id) {
		this.id = id;
		
		this.parts_id = null;
		this.currentBlogpartsID = null;
		this.oldSortCol = null;
	},
	//カレントをハイライトしてラジオボタンをチェック状態にする
	checkCurrent: function() {
		var currentRow = $("blogparts_current_row" + this.currentBlogpartsID + this.id);
		if (!currentRow) {
			return;
		}
		//指定したエレメントにスタイルのクラスを追加
		Element.addClassName(currentRow, "highlight");

		var current = $("blogparts_current" + this.currentBlogpartsID + this.id);
		//カレントのラジオボタンのチェック状態にする
		current.checked = true;
	},
	//カレントを変更してハイライトの位置も変える
	changeCurrent: function(blogpartsID) {
		var oldCurrentRow = $("blogparts_current_row" + this.currentBlogpartsID + this.id);
		if (oldCurrentRow) {
			//指定したところの指定したクラス名を削除する
			Element.removeClassName(oldCurrentRow, "highlight");
		}
		
		this.currentBlogpartsID = blogpartsID;
		var currentRow = $("blogparts_current_row" + this.currentBlogpartsID + this.id);
		Element.addClassName(currentRow, "highlight");
		
		var post = {
			"action":"blogparts_action_edit_current",
			"parts_id":blogpartsID
		};
		var params = new Object();
		//エラーが帰ってきたときの処理
		params["callbackfunc_error"] = function(res){
											commonCls.alert(res);
											commonCls.sendView(this.id, "blogparts_view_edit_list");
										}.bind(this);
		commonCls.sendPost(this.id, post, params);
	},
	//参照を押したときに呼び出される
	referBlogparts: function(event, parts_id) {
		var params = new Object();
		params["action"] = "blogparts_view_edit_preview";
		params["parts_id"] = parts_id;
		params["prefix_id_name"] = "popup_parts_reference" + parts_id;

		var popupParams = new Object();
		var top_el = $(this.id);
		popupParams['top_el'] = top_el;
		popupParams['target_el'] = top_el;
	
		commonCls.sendPopupView(event, params, popupParams);
	},
	//削除を押すときに呼び出される
	deleteBlogparts: function(parts_id, confirmMessage) {
		if (!commonCls.confirm(confirmMessage)) return false;
		
		var post = {
			"action":"blogparts_action_edit_delete",
			"parts_id":parts_id
		};
					
		var params = new Object();
		params["target_el"] = $(this.id);
		params["callbackfunc_error"] = function(res){
											commonCls.alert(res);
											commonCls.sendView(this.id, "blogparts_view_edit_list");
										}.bind(this);
		commonCls.sendPost(this.id, post, params);
	},
	//管理の編集を押す時に呼び出される。
	editBlogparts: function(parts_id) {
		var params = new Object();
		params["action"] = "blogparts_view_edit_modify";
		params["parts_id"] = parts_id;
		//paramsの情報を持ってactionに指定した場所を表示する
		commonCls.sendView(this.id, params);
	},
	//新規登録と編集のアクションを起こす
	insertParts: function(form_el) {
		var params = new Object();
		params["target_el"]=$(this.id);
		params["focus_flag"]=true;
		//blogparts/action/edit/entryにformから受け取った内容をつなげて送る
		var post="action=blogparts_action_edit_entry&" + Form.serialize(form_el);
		commonCls.sendPost(this.id, post, params);
	}
	
}