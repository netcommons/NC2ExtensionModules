var clsScheduler = Class.create();
var schedulerCls = new Array();

clsScheduler.prototype = {
	initialize: function(id) {
		this.id = id;
		this.icon = null;
		this.description = null;
		this.calendar = null;
		this.replyFineElementNumber = null;
		this.dateIteration = 0;
	},

	initializeScheduler: function() {
		var post = {
			"action":"scheduler_action_edit_initialize"
		}
		commonCls.sendPost(this.id, post);
	},

	changeMailSend: function(send) {
		if (send) {
			Element.removeClassName($("scheduler_mail_send_content" + this.id), "display-none");
		} else {
			Element.addClassName($("scheduler_mail_send_content" + this.id), "display-none");
		}
	},

	entryScheduler: function() {
		this._entry();
	},

	entryDisplayMethod: function() {
		this._entry();
	},

	_entry: function() {
		var post = Form.serialize($("scheduler_form" + this.id));
		var params = {
			"target_el":$(this.id),
			"focus_flag":true
		};
		commonCls.sendPost(this.id, post, params);
	},

	showScheduleList: function(appendParams) {
		var params = {
			"action":"scheduler_view_main_list"
		}
		Object.extend(params, appendParams);
		commonCls.sendView(this.id, params);
	},

	selectSchedule: function() {
		var inputElements = $(this.id).getElementsByTagName("input");
		var scheduleId = null;
		for (var i = 0, length = inputElements.length; i < length; i++) {
			if (inputElements[i].type != "radio") {
				continue;
			}
			if (inputElements[i].checked) {
				scheduleId = inputElements[i].value;
			}
		}

		var post = {
			"action":"scheduler_action_main_select",
			"scheduler_id":$("scheduler_scheduler_id" + this.id).value,
			"schedule_id":scheduleId
		};
		var params = {
			"target_el":$(this.id)
		};
		commonCls.sendPost(this.id, post, params);
	},

	deleteSchedule: function(scheduleId, confirmMessage) {
		if (!commonCls.confirm(confirmMessage)) {
			return false;
		}
		var post = {
			"action":"scheduler_action_main_delete",
			"schedule_id":scheduleId
		};
		var params = {
			"callbackfunc":function(response) {
								commonCls.sendView(this.id, "scheduler_view_main_list");
							}.bind(this)
		}

		commonCls.sendPost(this.id, post, params);
	},

	showScheduleDetail: function(event, scheduleId) {
		var params = {
			"action":"scheduler_view_main_detail",
			"schedule_id":scheduleId,
			"prefix_id_name":"scheduler"
		}
		var options = {
			"top_el":$(this.id),
			"modal_flag":true
		}
		commonCls.sendPopupView(event, params, options);
	},

	showScheduleEntry: function(event, scheduleId) {
		var params = {
			"action":"scheduler_view_main_entry",
			"schedule_id":scheduleId,
			"prefix_id_name":"scheduler"
		}
		var options = {
			"top_el":$(this.id),
			"modal_flag":true
		}
		commonCls.sendPopupView(event, params, options);
	},

	showIcon: function() {
		if (this.icon == null) {
			this.icon = new compTitleIcon(this.id);
		}
		this.icon.showDialogBox($("scheduler_icon" + this.id), $("icon_hidden" + this.id));
	},

	showWysiwyg: function() {
		var postForm = $("scheduler_form" + this.id);
		this.description = new compTextarea();
		this.description.uploadAction = {
			image	: "scheduler_action_main_upload_image",
			file	: "scheduler_action_main_upload_file"
		};
		this.description.textareaEditShow(this.id, postForm["description"]);
	},

	entrySchedule: function() {
		var params = {
			"callbackfunc":function(response) {
										var mailElement = $("scheduler_mail_send" + this.id);
										if (mailElement != null && mailElement.checked) {
											commonCls.sendPost(this.id, {"action":"scheduler_action_main_mail"}, {"loading_el":null});
										}
										var id = this.id.replace("_scheduler", "");
										commonCls.sendView(id,"scheduler_view_main_init");
										commonCls.removeBlock(this.id);
							}.bind(this)
		};
		var post = Form.serialize($("scheduler_form" + this.id));
		post += "&description=" + encodeURIComponent(this.description.getTextArea());

		commonCls.sendPost(this.id, post, params);
	},

	changePeriod: function() {
		this.calendar.disabledCalendar(!$("scheduler_period_checkbox" + this.id).checked);
	},

	switchAllday: function(iteration) {
		var postForm = $("scheduler_form" + this.id);
		var disabled = postForm["allday_flag[" + iteration + "]"].checked;
		postForm["start_hour[" + iteration + "]"].disabled = disabled;
		postForm["start_minute[" + iteration + "]"].disabled = disabled;
		postForm["end_hour[" + iteration + "]"].disabled = disabled;
		postForm["end_minute[" + iteration + "]"].disabled = disabled;
	},

	addDate: function() {
		var params = {
			"action":"scheduler_view_main_date_add",
			"dateIteration":this.dateIteration + 1
		}

		var options = {
			"top_el":$(this.id),
			"target_el":$("scheduler_entry_schedule_date_add" + this.id),
			"callbackfunc":function(response) {
								var appendElement = $("scheduler_entry_schedule_date_list" + this.id);
								appendElement.appendChild($("scheduler_entry_schedule_date_add" + this.id).firstChild);
							}.bind(this),
			"param":params
		}

		commonCls.send(options);
	},

	deleteDate: function(dateElement) {
		dateElement.parentNode.removeChild(dateElement);
	},

	initializeCalendar: function(dateElement, dateId) {
		if (Element.hasClassName(dateElement, "scheduler_calendar_day")) {
			dateElement = dateElement.parentNode;
		}
		Element.addClassName(dateElement, "scheduler_calendar_selected");
		dateElement.childNodes[0].value = dateId;
	},

	selectCalendarAllDate: function(dateElement) {
		var params = {
			"action":"scheduler_action_main_calendar"
		}

		var separateDayElement = dateElement.lastChild;
		var separateElements = separateDayElement.getElementsByTagName("a");
		for (var i = 0, length = separateElements.length; i < length; i++) {
			Element.removeClassName(separateDayElement.childNodes[i], "scheduler_calendar_selected");

			params["date_id[" + i + "]"] = separateElements[i].childNodes[0].value;
			params["allday_flag[" + i + "]"] = separateElements[i].childNodes[1].value;
			params["calendarStartDate[" + i + "]"] = separateElements[i].childNodes[2].value;
			params["deleteFlag[" + i + "]"] = true;
		}

		params["date_id[" + i + "]"] = dateElement.childNodes[0].value;
		params["allday_flag[" + i + "]"] = dateElement.childNodes[1].value;
		params["calendarStartDate[" + i + "]"] = dateElement.childNodes[2].value;
		if (Element.hasClassName(dateElement, "scheduler_calendar_selected")) {
			params["deleteFlag[" + i + "]"] = true;
			Element.removeClassName(dateElement, "scheduler_calendar_selected");
		} else {
			Element.addClassName(dateElement, "scheduler_calendar_selected");
			params["deleteFlag[" + i + "]"] = null;
		}

		var options = {
			"top_el":$(this.id),
			"loading_el":dateElement,
			"method":"post",
			"param":params
		}

		commonCls.send(options);
	},

	selectCalendarSeparate: function(dateElement) {
		if (Element.hasClassName(dateElement.parentNode.parentNode, "scheduler_calendar_selected")) {
			dateElement = (dateElement.parentNode.parentNode);
			this.selectCalendarAllDate(dateElement);
			return;
		}

		var params = {
			"action":"scheduler_action_main_calendar",
			"date_id":dateElement.childNodes[0].value,
			"allday_flag":dateElement.childNodes[1].value,
			"calendarStartDate":dateElement.childNodes[2].value,
			"deleteFlag":Element.hasClassName(dateElement, "scheduler_calendar_selected")
		}

		if (params["deleteFlag"]) {
			Element.removeClassName(dateElement, "scheduler_calendar_selected");
		} else {
			Element.addClassName(dateElement, "scheduler_calendar_selected");
			params["deleteFlag"] = null;
		}

		var options = {
			"top_el":$(this.id),
			"loading_el":dateElement,
			"method":"post",
			"param":params
		}

		commonCls.send(options);
	},

	selectCalendarAllSeparate: function(separateDayElement) {
		var deleteFlag = false;
		if (Element.hasClassName(separateDayElement.childNodes[0], "scheduler_calendar_selected")
			&& Element.hasClassName(separateDayElement.childNodes[1], "scheduler_calendar_selected")
			&& Element.hasClassName(separateDayElement.childNodes[2], "scheduler_calendar_selected")) {
			deleteFlag = true;
		}

		var params = {
			"action":"scheduler_action_main_calendar"
		}
		var separateElements = separateDayElement.getElementsByTagName("a");
		for (var i = 0, length = separateElements.length; i < length; i++) {
			if (deleteFlag) {
				Element.removeClassName(separateElements[i], "scheduler_calendar_selected");
			} else {
				Element.addClassName(separateElements[i], "scheduler_calendar_selected");
			}

			params["date_id[" + i + "]"] = separateElements[i].childNodes[0].value;
			params["allday_flag[" + i + "]"] = separateElements[i].childNodes[1].value;
			params["calendarStartDate[" + i + "]"] = separateElements[i].childNodes[2].value;
			params["deleteFlag[" + i + "]"] = deleteFlag;
		}

		var options = {
			"top_el":$(this.id),
			"loading_el":separateElements.parentNode,
			"method":"post",
			"param":params
		}

		commonCls.send(options);
	},

	showInputDate: function(entryType) {
		var params = {
			"action":"scheduler_view_main_date_input"
		}

		var options = {
			"top_el":$(this.id),
			"target_el":$("scheduler_entry_schedule_date_list" + this.id),
			"callbackfunc":function(response) {
								commonCls.displayVisible($("scheduler_entry_schedule_date_row" + this.id));
								commonCls.displayNone($("scheduler_calendar" + this.id));
								var schedulerForm = $("scheduler_form" + this.id);
								schedulerForm["entry_type"].value = entryType;
							}.bind(this),
			"param":params
		}

		commonCls.send(options);
	},

	showReply: function(scheduleId) {
		var params = {
			"action":"scheduler_view_main_reply",
			"schedule_id":scheduleId
		}
		commonCls.sendView(this.id, params);
	},

	initializeReply: function() {
		var frameElement = $("scheduler_replies"+this.id);
		var tableElement = frameElement.firstChild;
		if (tableElement.offsetWidth < frameElement.offsetWidth) {
			Element.removeClassName(frameElement, "scheduler_replies_frame_width");
		}
		if (tableElement.offsetHeight < frameElement.offsetHeight) {
			Element.removeClassName(frameElement, "scheduler_replies_frame_height");
		}

		var ownReplyRowElement = $("scheduler_own_reply" + this.id);
		if (ownReplyRowElement == null) {
			return;
		}
		var ownReplyElements = ownReplyRowElement.getElementsByTagName("td");

		for (var i = 0, length = ownReplyElements.length; i < length; i++) {
			var reply = Number(ownReplyElements[i].firstChild.value);
			reply++;
			Element.removeClassName(ownReplyElements[i].childNodes[reply], "display-none");

			if (reply == this.replyFineElementNumber) {
				Element.removeClassName(ownReplyElements[i].childNodes[reply + 1], "display-none");
			}
		}
	},

	switchReply: function(eventElement, reply) {
		var ownReplyElement = eventElement.parentNode;
		ownReplyElement.firstChild.value = reply;
		reply = Number(reply);
		reply++;
		Element.addClassName(eventElement, "display-none");
		Element.removeClassName(ownReplyElement.childNodes[reply], "display-none");

		var commentElementNumber = this.replyFineElementNumber + 1;
		if (reply == this.replyFineElementNumber) {
			Element.removeClassName(ownReplyElement.childNodes[commentElementNumber], "display-none");
		} else {
			Element.addClassName(ownReplyElement.childNodes[commentElementNumber], "display-none");
		}
	},

	reply: function(eventElement, reply) {
		this._entry();
	},

	showComment: function(event, dateId, replyUserId) {
		var schedulerForm = $("scheduler_form" + this.id);
		var replyCommentName = "reply_comment[" + dateId + "]";
		var params = {
			"action":"scheduler_view_main_comment",
			"date_id":dateId,
			"reply_user_id":replyUserId,
			"reply_comment":schedulerForm[replyCommentName].value,
			"prefix_id_name":"scheduler"
		}
		var optionParams = {
			"top_el":$(this.id),
			"modal_flag":true
		}
		commonCls.sendPopupView(event, params, optionParams);
	},

	replyComment: function() {
		var commentForm = $("scheduler_comment_form" + this.id);
		var dateId = commentForm["date_id"].value;

		var id = this.id.replace("_scheduler", "");
		var schedulerForm = $("scheduler_form" + id);

		var replyCommentName = "reply_comment[" + dateId + "]";
		schedulerForm[replyCommentName].value = commentForm["reply_comment"].value;
		commonCls.removeBlock(this.id);
	},

	changeMonth: function(date, scheduleId) {
		var params = {
			"action":"scheduler_view_main_calendar",
			"calendarDate":date,
			"schedule_id":scheduleId
		}

		var options = {
			"top_el":$(this.id),
			"target_el":$("scheduler_calendar" + this.id),
			"param":params
		}

		commonCls.send(options);
	}
}