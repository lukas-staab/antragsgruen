define(["require","exports","../shared/DraftSavingEngine","../shared/AntragsgruenEditor"],function(t,n,e,i){"use strict";Object.defineProperty(n,"__esModule",{value:!0});var a=function(){function t(n){this.$form=n,this.hasChanged=!1,$(".input-group.date").datetimepicker({locale:$("html").attr("lang"),format:"L"}),$(".wysiwyg-textarea").each(this.initWysiwyg.bind(this)),$(".form-group.plain-text").each(this.initPlainTextFormGroup.bind(this));var i=$("#draftHint"),a=i.data("motion-type"),o=i.data("motion-id");new e.DraftSavingEngine(n,i,"motion_"+a+"_"+o),n.on("submit",function(){$(window).off("beforeunload",t.onLeavePage)})}return t.onLeavePage=function(){return __t("std","leave_changed_page")},t.prototype.initWysiwyg=function(n,e){var a=this,o=$(e),r=o.find(".texteditor"),d=new i.AntragsgruenEditor(r.attr("id"));r.parents("form").submit(function(){r.parent().find("textarea").val(d.getEditor().getData())}),d.getEditor().on("change",function(){a.hasChanged||(a.hasChanged=!0,$(window).on("beforeunload",t.onLeavePage))})},t.prototype.initPlainTextFormGroup=function(t,n){var e=$(n),i=e.find("input.form-control");if(0!=e.data("max-len")){var a=e.data("max-len"),o=!1,r=e.find(".maxLenTooLong"),d=e.parents("form").first().find("button[type=submit]"),s=e.find(".maxLenHint .counter");a<0&&(o=!0,a*=-1),i.on("keyup change",function(){var t=i.val().length;s.text(t),t>a?(r.removeClass("hidden"),o||d.prop("disabled",!0)):(r.addClass("hidden"),o||d.prop("disabled",!1))}).trigger("change")}},t}();n.MotionEditForm=a});
//# sourceMappingURL=MotionEditForm.js.map
