define(["require","exports","../shared/DraftSavingEngine","../shared/AntragsgruenEditor"],(function(t,e,i,n){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e.MotionEditForm=void 0;class a{constructor(t){this.$form=t,this.hasChanged=!1,$(".input-group.date").datetimepicker({locale:$("html").attr("lang"),format:"L"}),$(".wysiwyg-textarea").each(this.initWysiwyg.bind(this)),$(".form-group.plain-text").each(this.initPlainTextFormGroup.bind(this));let e=$("#draftHint"),n=e.data("motion-type"),o=e.data("motion-id");new i.DraftSavingEngine(t,e,"motion_"+n+"_"+o),t.on("submit",(t=>{let e=!1;this.checkMultipleTagsError()&&(e=!0),e?t.preventDefault():$(window).off("beforeunload",a.onLeavePage)}))}checkMultipleTagsError(){let t=this.$form.find(".multipleTagsGroup");return 0!==t.length&&(this.$form.find(".multipleTagsGroup input[type=checkbox]").length||t.find("input:checked").length>0?(t.removeClass("has-error"),!1):(t.addClass("has-error"),t.scrollintoview({top_offset:-50}),!0))}static onLeavePage(){return __t("std","leave_changed_page")}initWysiwyg(t,e){let i=$(e).find(".texteditor"),o=new n.AntragsgruenEditor(i.attr("id"));i.parents("form").on("submit",(()=>{i.parent().find("textarea").val(o.getEditor().getData())})),o.getEditor().on("change",(()=>{this.hasChanged||(this.hasChanged=!0,$("body").hasClass("testing")||$(window).on("beforeunload",a.onLeavePage))}))}initPlainTextFormGroup(t,e){let i=$(e),n=i.find("input.form-control");if(0!=i.data("max-len")){let t=i.data("max-len"),e=!1,a=i.find(".maxLenTooLong"),o=i.parents("form").first().find("button[type=submit]"),r=i.find(".maxLenHint .counter");t<0&&(e=!0,t*=-1),n.on("keyup change",(()=>{let i=n.val().length;r.text(i),i>t?(a.removeClass("hidden"),e||o.prop("disabled",!0)):(a.addClass("hidden"),e||o.prop("disabled",!1))})).trigger("change")}}}e.MotionEditForm=a}));
//# sourceMappingURL=MotionEditForm.js.map
