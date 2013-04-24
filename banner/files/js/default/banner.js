var clsBanner = Class.create();
var bannerCls = Array();

clsBanner.prototype = {
	initialize: function(id) {
		this.id = id;
		this.popup = null;
		this.popupForm = null;
	},

	showList: function(categoryId) {
		var params = {
			"action":"banner_view_edit_list",
			"category_id":categoryId
		};
		commonCls.sendView(this.id, params);
	},

	initializeDisplay: function() {
		var trElements = $("banner_list" + this.id).getElementsByTagName("tr");
		for (var i = trElements.length - 1; i >= 0; i--) {
			if (Element.hasClassName(trElements[i], "banner_display_checked")
				|| Element.hasClassName(trElements[i], "banner_display_checked_none")) {
				break;
			}

			var inputElements = trElements[i].getElementsByTagName("input");
			this._changeElementClass(inputElements[0].checked, trElements[i]);
		}
	},

	changeDislpay: function(bannerId) {
		var checkBox = $("banner_display" + bannerId + this.id);
		var trElement = $("banner_list_tr" + bannerId + this.id);
		this._changeElementClass(checkBox.checked, trElement);

		var post = {
			"action":"banner_action_edit_display",
			"banner_id":bannerId
		};
		if (checkBox.checked) {
			post["display"] = checkBox.checked;
		}
		commonCls.sendPost(this.id, post);
	},

	changeDislpayAll: function(checked) {
		var inputElements = $("banner_list" + this.id).getElementsByTagName("input");
		var trElements = $("banner_list" + this.id).getElementsByTagName("tr");
		for (var i = 0, length = inputElements.length; i < length; i++) {
			inputElements[i].checked = checked;
			this._changeElementClass(inputElements[i].checked, trElements[i]);
		}

		var post = {
			"action":"banner_action_edit_display",
			"category_id":$("banner_category" + this.id).value
		};
		if (checked) {
			post["display"] = checked;
		}
		commonCls.sendPost(this.id, post);
	},

	_changeElementClass: function(checked, trElement) {
		if (checked) {
			Element.addClassName(trElement, "banner_display_checked");
			Element.removeClassName(trElement, "banner_display_checked_none");
		} else {
			Element.addClassName(trElement, "banner_display_checked_none");
			Element.removeClassName(trElement, "banner_display_checked");
		}
	},

	showEntry: function(event, bannerId) {
		var params = {
			"action":"banner_view_edit_entry",
			"banner_id":bannerId,
			"prefix_id_name":"banner_entry"
		}
		var options = {
			"top_el":$(this.id)
		}
		if (event == undefined) {
			options["center_flag"] = true;
		}
		commonCls.sendPopupView(event, params, options);
	},

	selectBannerTypeUrl: function() {
		commonCls.displayVisible($("banner_link_url_tr" + this.id));
		commonCls.displayVisible($("banner_image_url_tr" + this.id));
		commonCls.displayVisible($("banner_size_tr" + this.id));

		commonCls.displayNone($("banner_upload_tr" + this.id));
		commonCls.displayNone($("banner_source_tr" + this.id));
	},

	selectBannerTypeImage: function() {
		commonCls.displayVisible($("banner_link_url_tr" + this.id));
		commonCls.displayVisible($("banner_upload_tr" + this.id));
		commonCls.displayVisible($("banner_size_tr" + this.id));

		commonCls.displayNone($("banner_image_url_tr" + this.id));
		commonCls.displayNone($("banner_source_tr" + this.id));
	},

	selectBannerTypeSource: function() {
		commonCls.displayVisible($("banner_source_tr" + this.id));

		commonCls.displayNone($("banner_link_url_tr" + this.id));
		commonCls.displayNone($("banner_image_url_tr" + this.id));
		commonCls.displayNone($("banner_upload_tr" + this.id));
		commonCls.displayNone($("banner_size_tr" + this.id));
	},

	selectBannerTypeLink: function() {
		commonCls.displayVisible($("banner_link_url_tr" + this.id));

		commonCls.displayNone($("banner_image_url_tr" + this.id));
		commonCls.displayNone($("banner_upload_tr" + this.id));
		commonCls.displayNone($("banner_size_tr" + this.id));
		commonCls.displayNone($("banner_source_tr" + this.id));
	},

	changeSizeFlag: function(value) {
		$("banner_display_width" + this.id).disabled = !value;
		$("banner_display_height" + this.id).disabled = !value;
	},

	enterBanner: function() {
		var params = {
			"param":{
				"action":"banner_action_edit_entry"
			},
			"top_el":$(this.id),
			"callbackfunc":function(files, res){
								var id = this.id.replace("_banner_entry", "");
								commonCls.sendView(id, 'banner_view_edit_list');
								commonCls.removeBlock(this.id);
							}.bind(this)
		}

		commonCls.sendAttachment(params);
	},

	deleteBanner: function(bannerId, confirmMessage) {
		if (!commonCls.confirm(confirmMessage)) {
			return false;
		}

		var post = {
			"action":"banner_action_edit_delete",
			"banner_id":bannerId
		};
		var params = {
			"callbackfunc":function(response) {
								commonCls.sendView(this.id, "banner_view_edit_list");
							}.bind(this)
		}

		commonCls.sendPost(this.id, post, params);
	},

	showSequence: function(event) {
		commonCls.sendView(this.id, "banner_view_edit_sequence");
	},

	changeSequence: function(drag_id, drop_id, position) {
		var post = {
			"action":"banner_action_edit_sequence", 
			"drag_banner_id":drag_id.match(/\d+/)[0],
			"drop_banner_id":drop_id.match(/\d+/)[0],
			"position":position
		};

		commonCls.sendPost(this.id, post); 
	},

	showCategory: function(event) {
		commonCls.sendView(this.id, "banner_view_edit_category_list");
	},

	showCategoryEntry: function(eventElement) {
		this.popup = new compPopup(this.id);
		this.popup.modal = true;
		this.popup.loadObserver = function() {
									this.popupForm = this.popup.popupElement.contentWindow.document.getElementsByTagName("form")[0];
									commonCls.focus(this.popupForm);
								}.bind(this);

		var params = {
			"param":{
				"action":"banner_view_edit_category_entry"
			},
			"top_el":this.id,
			"callbackfunc":function(res) {
								this.popup.showPopup(res, eventElement);
							}.bind(this)
		};
		commonCls.send(params);
	},

	showCategoryInputElement: function(element) {
		commonCls.displayNone(element);
		commonCls.displayVisible(element.nextSibling);
		commonCls.focus(element.nextSibling);
	},

	enterCategory: function(categoryId) {
		if (categoryId == undefined) {
			var categoryElement = this.popupForm["category_name"];
		} else {
			var categoryElement = $("banner_category_name" + categoryId + this.id);
		}
		var post = {
			"action":"banner_action_edit_category_entry",
			"category_id":categoryId,
			"category_name":categoryElement.value
		};
		if (this.id.match(/^_banner/)) {
			var params = {
				"target_el":$("baner_category_select" + this.id),
				"callbackfunc":function(response) {
									this.popup.closePopup();
								}.bind(this)
			}
		} else if (categoryId == null) {
			var params = {
				"callbackfunc":function(response) {
									commonCls.sendView(this.id, "banner_view_edit_category_list");
								}.bind(this)
			}
		} else {
			var params = {
				"callbackfunc":function(response) {
									categoryElement.previousSibling.innerHTML = categoryElement.value.escapeHTML();
									commonCls.displayNone(categoryElement);
									commonCls.displayVisible(categoryElement.previousSibling);
								}.bind(this)
			}
		}

		commonCls.sendPost(this.id, post, params);
	},

	deleteCategory: function(categoryId, confirmMessage) {
		if (!commonCls.confirm(confirmMessage)) {
			return false;
		}

		var post = {
			"action":"banner_action_edit_category_delete",
			"category_id":categoryId
		};
		var params = {
			"callbackfunc":function(response) {
								this.showCategory();
							}.bind(this)
		}

		commonCls.sendPost(this.id, post, params);
	},

	changeCategorySequence: function(drag_id, drop_id, position) {
		var post = {
			"action":"banner_action_edit_category_sequence", 
			"drag_category_id":drag_id.match(/\d+/)[0],
			"drop_category_id":drop_id.match(/\d+/)[0],
			"position":position
		};

		commonCls.sendPost(this.id, post); 
	},

	showPreview: function(event, bannerId) {
		var params = {
			"action":"banner_view_edit_preview",
			"banner_id":bannerId,
			"prefix_id_name":"banner_preview" + bannerId
		}
		var options = {
			"top_el":$(this.id)
		}
		commonCls.sendPopupView(event, params, options);
	},

	clickBanner: function(bannerId) {
		var post = {
			"action":"banner_action_main_click",
			"banner_id":bannerId
		}

		commonCls.sendPost(this.id, post);
	}
}