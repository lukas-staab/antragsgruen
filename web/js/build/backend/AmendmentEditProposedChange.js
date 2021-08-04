define(["require","exports","../shared/AntragsgruenEditor","../frontend/MotionMergeAmendments"],(function(e,t,i,n){"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.AmendmentEditProposedChange=void 0;class o{constructor(e){this.$form=e,this.hasChanged=!1,this.textEditCalled(),this.initCollisionDetection(),e.on("submit",(()=>{$(window).off("beforeunload",o.onLeavePage)}))}textEditCalled(){$(".wysiwyg-textarea:not(#sectionHolderEditorial)").each(((e,t)=>{let n=$(t).find(".texteditor"),o=new i.AntragsgruenEditor(n.attr("id")).getEditor();n.parents("form").on("submit",(()=>{n.parent().find("textarea.raw").val(o.getData()),void 0!==o.plugins.lite&&(o.plugins.lite.findPlugin(o).acceptAll(),n.parent().find("textarea.consolidated").val(o.getData()))})),$("#"+n.attr("id")).on("keypress",this.onContentChanged.bind(this))})),this.$form.find(".resetText").on("click",(e=>{let t=$(e.currentTarget).parents(".wysiwyg-textarea").find(".texteditor");window.CKEDITOR.instances[t.attr("id")].setData(t.data("original-html")),$(e.currentTarget).parents(".modifiedActions").addClass("hidden")}))}initCollisionDetection(){this.$collisionIndicator=this.$form.find("#collisionIndicator");let e=null;window.setInterval((()=>{let t=this.getTextConsolidatedSections();if(JSON.stringify(t)===e)return;e=JSON.stringify(t);let i=this.$form.data("collision-check-url");$.post(i,{_csrf:this.$form.find("> input[name=_csrf]").val(),sections:t},(e=>{if(e.error)this.$collisionIndicator.removeClass("hidden"),this.$collisionIndicator.find(".collisionList").html("<li>"+e.error+"</li>");else if(0==e.collisions.length)this.$collisionIndicator.addClass("hidden");else{this.$collisionIndicator.removeClass("hidden");let t="";e.collisions.forEach((e=>{t+=e.html})),this.$collisionIndicator.find(".collisionList").html(t)}}))}),5e3)}getTextConsolidatedSections(){let e={};return $(".proposedVersion .wysiwyg-textarea:not(#sectionHolderEditorial)").each(((t,i)=>{let o=$(i),s=o.find(".texteditor"),a=o.parents(".proposedVersion").data("section-id"),d=s.clone(!1);d.find(".ice-ins").each(((e,t)=>{n.MotionMergeChangeActions.insertAccept(t)})),d.find(".ice-del").each(((e,t)=>{n.MotionMergeChangeActions.deleteAccept(t)})),e[a]=d.html()})),e}static onLeavePage(){return __t("std","leave_changed_page")}onContentChanged(){this.hasChanged||(this.hasChanged=!0,$("body").hasClass("testing")||$(window).on("beforeunload",o.onLeavePage))}}t.AmendmentEditProposedChange=o}));
//# sourceMappingURL=AmendmentEditProposedChange.js.map
