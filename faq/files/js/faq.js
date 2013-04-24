var clsFaq = Class.create();
var faqCls = Array();

clsFaq.prototype = {
	/**
	 * 初期処理
	 *
	 * @param	id	ID
	 * @return  none
	 **/
	initialize: function(id) {
		this.id = id;

		this.currentFaqId = null;
		this.textarea = null;
		this.textarea_name = null;
		this.pager = null;
		this.category_id =null;
		this.visible_item = null;
		this.inItems = new Object();
		this.inUpdItems = new Object();
		this.popupLnk = null;
		this.dndCustomDrag = null;
		this.dndCustomDropzone = null;
		this.dndMgrCatObj = null;
		this.titleIcon = null;
	},
	initCategory: function(faq_id) {
		// ドラッグ
		this.dndCustomDrag = Class.create();
		this.dndCustomDrag.prototype = Object.extend((new compDraggable), {
			prestartDrag: function() {
				var htmlElement = this.getHTMLElement();
				this._displayChg(htmlElement);
			},
			cancelDrag: function() {
				var draggable = this.htmlElement;
				Element.setStyle(draggable, {opacity:""});
				this._displayChg(draggable, 1);
   			},
			_displayChg: function(htmlElement, cancel_flag) {
				if(Element.hasClassName(htmlElement, "_faq_cat" + this.id)) {
			    	var paramObj= this.getParams();
			    	var el= paramObj['top_el'];
			    	var cat_fields = Element.getElementsByClassName(el, "_faq_cat" + this.id);
					cat_fields.each(function(cat_el) {
						commonCls.displayChange(cat_el);
					}.bind(this));
					if(cancel_flag) {
						var top_row_el = Element.getChildElementByClassName(htmlElement, "_faq_cat" + this.id);
						commonCls.displayVisible(top_row_el);
					}
				}
			}
		});

		// ドロップ
		this.dndCustomDropzone = Class.create();
		this.dndCustomDropzone.prototype = Object.extend((new compDropzone), {
			showHover: function(event) {
				var htmlElement = this.getHTMLElement();
				if ( this._showHover(htmlElement) )
					return;
				if(this.getParams() && !Element.hasClassName(htmlElement, "_faq_cat_top")) {
					this.showChgSeqHoverInside(event);
				} else {
					//inside
					this.showChgSeqHover(event);
				}
			},

			hideHover: function(event) {
				this.hideChgSeqHover(event);
			},

			accept: function(draggableObjects) {
				this.acceptChgSeq(draggableObjects);
				var drag_el = draggableObjects[0].getHTMLElement();	// ドラッグ対象エレメント
				commonCls.blockNotice(null, drag_el);
			},

			save: function(draggableObjects) {
				if(this.ChgSeqPosition == null) {
					return false;
				}
				var htmlElement = this.getHTMLElement();
				var paramObj= draggableObjects[0].getParams();
				var id= paramObj['id'];
				var drag_el = draggableObjects[0].getHTMLElement();	// ドラッグ対象エレメント

				var chgseq_params = new Object();
				var top_el = $(id);

				if (Element.hasClassName(drag_el, "_faq_cat_top")) {
					var category_id = drag_el.id.replace("_faq_cat"+ id + "_category_id_","");

					// 表示順変更
					var drop_category_id = htmlElement.id.replace("_faq_cat"+ id + "_category_id_","");
					chgseq_params["param"] = {
						"action":"faq_action_edit_categoryseq",
						"faq_id":faq_id,
						"category_id":category_id,
						"drop_category_id":drop_category_id,
						"position":this.ChgSeqPosition
					};
				}
				chgseq_params["method"] = "post";
				chgseq_params["top_el"] = top_el;
				chgseq_params["loading_el"] = drag_el;
				commonCls.send(chgseq_params);
				return true;
			}
		});

		var edit_top_el = $("_faq_cat"+ this.id);
		this.dndMgrCatObj = new compDragAndDrop();
		this.dndMgrCatObj.registerDraggableRange(edit_top_el);

		this.dndMgrLinkObj = new compDragAndDrop();
		this.dndMgrLinkObj.registerDraggableRange(edit_top_el);

		var cat_rowfields = Element.getElementsByClassName(edit_top_el, "_faq_cat_chg_seq");
		cat_rowfields.each(function(row_el) {
			var top_row_el = Element.getParentElementByClassName(row_el,"_faq_cat_top");
			this.dndMgrLinkObj.registerDraggable(new this.dndCustomDrag(top_row_el, row_el, {"top_el":edit_top_el,"id":this.id}));
			this.dndMgrLinkObj.registerDropZone(new this.dndCustomDropzone(top_row_el));
		}.bind(this));
	},
	checkCurrent: function() {
		var currentRow = $("faq_current_row" + this.currentFaqId + this.id);
		if (!currentRow) {
			return;
		}
		Element.addClassName(currentRow, "highlight");

		var current = $("faq_current" + this.currentFaqId + this.id);
		current.checked = true;
	},
	//選択
	changeCurrent: function(faq_id) {
		var oldCurrentRow = $("faq_current_row" + this.currentFaqId + this.id);
		if (oldCurrentRow) {
			Element.removeClassName(oldCurrentRow, "highlight");
		}

		this.currentFaqId = faq_id;
		var currentRow = $("faq_current_row" + this.currentFaqId + this.id);
		Element.addClassName(currentRow, "highlight");

		var post = {
			"action":"faq_action_edit_change",
			"faq_id":faq_id
		};
		var params = new Object();
		params["callbackfunc_error"] = function(res){
			commonCls.alert(res);
			commonCls.sendView(this.id, "faq_view_edit_list");
		}.bind(this);
		commonCls.sendPost(this.id, post, params);
	},

	editCancel: function() {
		commonCls.sendView(this.id,"faq_view_main_init");
	},
	referenceFaq: function(event, faq_id) {
		var params = new Object();
		params["action"] = "faq_view_main_init";
		params["faq_id"] = faq_id;
		params["prefix_id_name"] = "popup_faq_reference" + faq_id;

		var popupParams = new Object();
		var top_el = $(this.id);
		popupParams['top_el'] = top_el;
		popupParams['target_el'] = top_el;
		popupParams['center_flag'] = true;

		commonCls.sendPopupView(event, params, popupParams);
	},
	changeSequence: function(drag_id, drop_id, position) {
		var post = {
			"action":"faq_action_main_sequence", 
			"drag_id":drag_id.match(/\d+/)[0],
			"drop_id":drop_id.match(/\d+/)[0],
			"position":position
		};
		
		commonCls.sendPost(this.id, post); 
	},
	//削除
	delFaq: function(faq_id, confirmMessage) {
		if (!commonCls.confirm(confirmMessage)) {
			return false;
		}
		var params = new Object();
		params["target_el"] = $(this.id);
		params["callbackfunc_error"] = function(res){
			commonCls.sendView(this.id, "faq_view_edit_list");
		}.bind(this);
		commonCls.sendPost(this.id, "action=faq_action_edit_delete&faq_id=" + faq_id, params);
	},
	//質問追加
	insFaq: function(form_el) {
		commonCls.sendPost(this.id, "action=faq_action_edit_create&" + Form.serialize(form_el), {"target_el":$(this.id)});
	},
	//表示方法変更
	styleEdit: function(form_el) {
		commonCls.sendPost(this.id, "action=faq_action_edit_style&" + Form.serialize(form_el), {"target_el":$(this.id)});
	},
	//編集画面初期処理
	postInit: function() {
		//テキストエリア
		this.textarea = new compTextarea();
		
		this.textarea.uploadAction = {
			//unique_id   : 0,
			image    : "faq_action_upload_image",
			file     : "faq_action_upload_init"
		};
		
		this.textarea.textareaShow(this.id, "textarea"+this.id, "simple");
	},
	//登録
	post: function(form_el, temp_flag) {
		var top_el = $(this.id);
		var faq_id = form_el.faq_id.value;
		var question_name = form_el.question_name.value;
		var category_id = form_el.category_id.value;
		var question_answer = this.textarea.getTextArea();
		var question_id = form_el.question_id.value;
		var tb_url = "";

		//パラメータ設定
		var ins_params = new Object();
		ins_params["method"] = "post";
		ins_params["param"] = {
			"action":"faq_action_main_post",
			"faq_id":faq_id,
			"question_name":question_name,
			"category_id":category_id,
			"question_answer":question_answer,
			"question_id":question_id
		};
		ins_params["top_el"] = top_el;
		ins_params["loading_el"] = top_el;
		ins_params["target_el"] = top_el;
		ins_params["callbackfunc_error"] = function(res){
			commonCls.alert(res);
		}.bind(this);
		commonCls.send(ins_params);
	},

	toPage: function(el, faq_id, cat_classname, num_classname, now_page, position) {
		var cat_name = null;
		var num_name = null;
		if(position == "bottom") {
			cat_name = cat_classname + "_bottom";
			num_name = num_classname + "_bottom";
		}else {
			cat_name = cat_classname;
			num_name = num_classname;
		}
		var cat = $(cat_name);
		var visible_item = $(num_name);
		var catlist = document.getElementsByClassName(cat_classname);
		var cats = $A(catlist);
		cats.each(function(c){
			c.selectedIndex = cat.selectedIndex;
		});
		var numlist = document.getElementsByClassName(num_classname);
		var nums = $A(numlist);
		nums.each(function(n){
			n.selectedIndex = visible_item.selectedIndex;
		});
		var cat_id = cat.options[cat.selectedIndex].value;
		var num = visible_item.options[visible_item.selectedIndex].value;
		var faq_id = $("faq_id"+this.id).value;
		var top_el = $(this.id);
		var params = new Object();

		params["param"] = {
			"action":"faq_view_main_init",
			"faq_id":faq_id,
			"category_id":cat_id,
			"display_row":num,
			"now_page":now_page
		};
		params["top_el"] = top_el;
		params["loading_el"] = el;
		params["target_el"] = top_el;
		commonCls.send(params);
	},
	deletePost: function(el, question_id, confirmMessage) {
		if (!commonCls.confirm(confirmMessage)) {
			return false;
		}
		var params = new Object();
		params["target_el"] = $(this.id);
		params["loading_el"] = el;
		commonCls.sendPost(this.id, "action=faq_action_main_delete&question_id=" + question_id, params);
	},
	focusItem: function(item_id, focus_flag) {
		this.inItems[item_id] = focus_flag;
	},
	updItems: function(event, this_el, faq_id, category_id) {
		var top_el = $(this.id);

		var edit_el = Element.getParentElementByClassName(this_el,"faq_cat_edit_item");
		var label_el = edit_el.previousSibling;

		var upd_params = new Object();
		upd_params['action'] = "faq_action_edit_category";
		upd_params['faq_id'] = faq_id;
		upd_params['category_id'] = category_id;
		upd_params['category_name'] = this_el.value;
		if(this.inUpdItems[upd_params['category_id']] == true) {
			// 更新中
			return;
		}
		this.inUpdItems[upd_params['category_id']] = true;
		var send_param = new Object();
		send_param["method"] = "post";
		send_param["param"] = upd_params;
		send_param["top_el"] = top_el;
		send_param["callbackfunc"] = function(res){
			// 正常終了
			commonCls.displayNone(edit_el);
			commonCls.displayVisible(label_el);
			label_el.innerHTML = upd_params['category_name'].escapeHTML();
			if(Event.element(event).type == "radio" || Event.element(event).type == "select-one"
				 || Event.element(event).type == "button" || event.keyCode == 13) {
				label_el.focus();
			}
			this.inUpdItems[upd_params['category_id']] = false;
		}.bind(this);
		send_param["callbackfunc_error"] = function(res){
			// エラー(File以外)
			commonCls.alert(res);
			this_el.focus();
			this.inUpdItems[upd_params['category_id']] = false;
		}.bind(this);
		commonCls.send(send_param);
	},
	clkItems: function(this_el) {
		var edit_el = this_el.nextSibling;
		commonCls.displayNone(this_el);
		commonCls.displayVisible(edit_el);
		var input_el = Element.getChildElement(edit_el);
		input_el.focus();
		input_el.select();
	},
	delCategory: function(faq_id, category_id, confirmMessage) {
		if (!commonCls.confirm(confirmMessage)) {
			return false;
		}

		var top_el = $(this.id);
		var del_params = new Object();
		del_params["param"] = {
			"action":"faq_action_edit_categorydel",
			"faq_id":faq_id,
			"category_id":category_id
		};
		del_params["callbackfunc"] = function(){
			commonCls.sendView(this.id,{"action":"faq_view_edit_category","faq_id":faq_id});
		}.bind(this);
		del_params["callbackfunc_error"] = function(res){
			commonCls.alert(res);
			commonCls.sendView(this.id,"faq_view_edit_list");
		}.bind(this);
		del_params["method"] = "post";
		del_params["loading_el"] = top_el;
		del_params["top_el"] = top_el;
		commonCls.send(del_params);
	},
	showAddPopup: function(this_el) {
		if(this.popupLnk == null || !$(this.popupLnk.popupID)) {
			this.popupLnk = new compPopup(this.id, "addCategory" + this.id);
			this.popupLnk.loadObserver = function() {
				commonCls.focus(this.popupLnk.popupElement.contentWindow.document.getElementsByTagName("form")[0]);
			}.bind(this);
		}
		this.popupLnk.showPopup(this.popupLnk.getPopupElementByEvent(this_el), this_el);
	},
	addCategory: function() {
		var top_el = $(this.id);
		var form = this.popupLnk.popupElement.contentWindow.document.getElementsByTagName("form")[0];
		var add_params = new Object();
		add_params["param"] = "faq_action_edit_categoryadd" + "&"+ Form.serialize(form);
		add_params["callbackfunc"] = function(){
			this.popupLnk.closePopup();
			commonCls.sendView(this.id,{"action":"faq_view_edit_category","faq_id":form.faq_id.value});
		}.bind(this);
		add_params["callbackfunc_error"] = function(res){
			if(res.match("^(category_name):")) {
				var mesArr = res.split(":");
				//メッセージ表示
				commonCls.alert(mesArr[1]);
				//フォーカスの移動
				form.content.focus();
				form.content.select();
			} else {
				commonCls.alert(res);
			}
		}.bind(this);
		add_params["method"] = "post";
		add_params["loading_el"] = top_el;
		add_params["top_el"] = top_el;
		commonCls.send(add_params);
	}
}