define(["require","exports","./MotionSupporterEdit","../shared/AntragsgruenEditor"],(function(e,t,o,i){"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.MotionEdit=void 0;t.MotionEdit=class{constructor(){let e=$("html").attr("lang");this.$updateForm=$("#motionUpdateForm"),this.$status=$("#motionStatus"),$("#motionDateCreationHolder").datetimepicker({locale:e}),$("#motionDatePublicationHolder").datetimepicker({locale:e}),$("#motionDateResolutionHolder").datetimepicker({locale:e}),$("#resolutionDateHolder").datetimepicker({locale:$("#resolutionDate").data("locale"),format:"L"}),$("#motionTextEditCaller").find("button").on("click",(()=>{this.initMotionTextEdit()})),$(".checkAmendmentCollisions").on("click",(e=>{e.preventDefault(),this.loadAmendmentCollisions()})),this.$updateForm.on("submit",(function(){$(".amendmentCollisionsHolder .amendmentOverrideBlock > .texteditor").each((function(){let e=CKEDITOR.instances[$(this).attr("id")].getData();$(this).parents(".amendmentOverrideBlock").find("> textarea").val(e)}))})),$(".motionDeleteForm").on("submit",((e,t)=>{this.onSubmitDeleteForm(e,t)})),this.initVotingFunctions(),this.initSlug(),new o.MotionSupporterEdit($("#motionSupporterHolder"))}initSlug(){$(".urlSlugHolder .shower button").on("click",(e=>{e.preventDefault(),$(".urlSlugHolder .shower").addClass("hidden"),$(".urlSlugHolder .holder").removeClass("hidden")}))}initVotingFunctions(){const e=$(".contentVotingResultCaller, .votingDataHolder"),t=$(".votingDataCloser"),o=$(".votingDataOpener"),i=$("select[name=votingBlockId]");o.on("click",(()=>{e.addClass("explicitlyOpened")})),t.on("click",(()=>{e.removeClass("explicitlyOpened")})),this.$status.on("change",(()=>{11===parseInt(this.$status.val(),10)?e.addClass("hasVotingStatus"):e.removeClass("hasVotingStatus")})).trigger("change"),$(".votingItemBlockRow select").on("change",(e=>{const t=$(e.currentTarget);if(t.val()){const e=t.find("option[value="+t.val()+"]").data("group-name");$(".votingItemBlockNameRow input").val(e),$(".votingItemBlockNameRow").removeClass("hidden")}else $(".votingItemBlockNameRow").addClass("hidden")})),i.on("change",(()=>{if("NEW"===i.val())$(".votingBlockRow .newBlock").removeClass("hidden"),$(".votingItemBlockRow").addClass("hidden"),$(".votingItemBlockNameRow").addClass("hidden");else{$(".votingBlockRow .newBlock").addClass("hidden"),$(".votingItemBlockRow").addClass("hidden");const e=$(".votingItemBlockRow"+i.val());e.removeClass("hidden"),e.length>0?(e.removeClass("hidden"),e.find("select").trigger("change")):$(".votingItemBlockNameRow").addClass("hidden")}})).trigger("change")}onSubmitDeleteForm(e,t){t&&(t.confirmed,1)&&!0===t.confirmed||(e.preventDefault(),bootbox.confirm(__t("admin","delMotionConfirm"),(function(e){e&&$(".motionDeleteForm").trigger("submit",{confirmed:!0})})))}initMotionTextEdit(){$("#motionTextEditCaller").addClass("hidden"),$("#motionTextEditHolder").removeClass("hidden"),$(".wysiwyg-textarea").each((function(){let e=$(this).find(".texteditor"),t=new i.AntragsgruenEditor(e.attr("id")).getEditor();e.parents("form").on("submit",(()=>{e.parent().find("textarea").val(t.getData())}))})),this.$updateForm.append("<input type='hidden' name='edittext' value='1'>"),$(".checkAmendmentCollisions").length>0&&($(".wysiwyg-textarea .texteditor").on("focus",(function(){$(".checkAmendmentCollisions").show(),$(".saveholder .save").prop("disabled",!0).hide()})),$(".checkAmendmentCollisions").show(),$(".saveholder .save").prop("disabled",!0).hide())}loadAmendmentCollisions(){let e=$(".checkAmendmentCollisions").data("url"),t={},o=$(".amendmentCollisionsHolder");$("#motionTextEditHolder").children().each((function(){let e=$(this);if(e.hasClass("wysiwyg-textarea")){let o=e.attr("id").replace("section_holder_","");t[o]=CKEDITOR.instances[e.find(".texteditor").attr("id")].getData()}})),$.post(e,{newSections:t,_csrf:this.$updateForm.find("> input[name=_csrf]").val()},(function(e){o.html(e),o.find(".amendmentOverrideBlock > .texteditor").length>0&&(o.find(".amendmentOverrideBlock > .texteditor").each((function(){new i.AntragsgruenEditor($(this).attr("id"))})),$(".amendmentCollisionsHolder").scrollintoview({top_offset:-50})),$(".checkAmendmentCollisions").hide(),$(".saveholder .save").prop("disabled",!1).show()}))}}}));
//# sourceMappingURL=MotionEdit.js.map
